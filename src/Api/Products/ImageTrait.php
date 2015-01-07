<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\Product as Model;
use \Illuminate\Support\Facades\File;
use \Neomerx\Core\Models\Image as ImageModel;
use \Neomerx\Core\Models\Variant as VariantModel;
use \Neomerx\Core\Models\Language as LanguageModel;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Models\ProductImage as ProductImageModel;
use \Symfony\Component\HttpFoundation\File\UploadedFile;

trait ImageTrait
{
    /**
     * @param array             $descriptions
     * @param array             $files
     * @param LanguageModel     $languageModel
     * @param ProductImageModel $productImageModel
     * @param Model             $product
     * @param VariantModel      $variant
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function saveImages(
        array $descriptions,
        array $files,
        LanguageModel $languageModel,
        ProductImageModel $productImageModel,
        Model $product,
        VariantModel $variant = null
    ) {
        count($descriptions) === count($files) ?: S\throwEx(new InvalidArgumentException('files'));

        $index = 0;
        $isoCodeCache = [];
        $createdFiles = [];
        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            foreach ($descriptions as $description) {

                $file = $files[$index];

                $file instanceof UploadedFile  ?: S\throwEx(new InvalidArgumentException("[$index][file]"));
                isset($description['alts'])    ?: S\throwEx(new InvalidArgumentException("[$index][alts]"));
                is_array($description['alts']) ?: S\throwEx(new InvalidArgumentException("[$index][alts]"));

                $alts = $description['alts'];
                unset($description['alts']);

                $fileName = $this->moveWithUniqueFileName($file, $product->link);
                $createdFiles[] = $fileName;

                $imageProperties = [];
                foreach ($alts as $isoCode => $alt) {

                    if (isset($isoCodeCache[$isoCode])) {
                        $languageId = $isoCodeCache[$isoCode];
                    } else {
                        $languageId = $languageModel->selectByCode($isoCode)
                            ->firstOrFail([LanguageModel::FIELD_ID])->{LanguageModel::FIELD_ID};
                        $isoCodeCache[$isoCode] = $languageId;
                    }

                    $imageProperties[] = [
                        LanguageModel::FIELD_ID => $languageId,
                        'alt' => $alt,
                    ];

                }

                // so here we have
                // - uploaded file at $fileName
                // - image description (position, is_cover) at $description (we haven't checked that but should be so)
                // - image properties (alts in different languages) at $imageProperties
                $productImageModel->addImageOrFail($product, $fileName, $description, $imageProperties, $variant);
            }

            $allExecutedOk = true;

        } finally {

            if (isset($allExecutedOk)) {
                /** @noinspection PhpUndefinedMethodInspection */
                DB::commit();
            } else {
                /** @noinspection PhpUndefinedMethodInspection */
                DB::rollBack();
                foreach ($createdFiles as $fileToDelete) {
                    /** @noinspection PhpUndefinedMethodInspection */
                    File::delete(ImageModel::getUploadFolderPath($fileToDelete));
                }
            }

        }

        Event::fire(new ProductArgs(Products::EVENT_PREFIX . 'addedImages', $product));
    }

    /**
     * Move file to upload folder with a name $baseFileName-XX.ext
     * where XX - number, ext - original file extension.
     *
     * @param UploadedFile $file
     * @param string       $baseFileName
     *
     * @return string
     */
    private function moveWithUniqueFileName(UploadedFile $file, $baseFileName)
    {
        // will check all files with mask '/full/path/to/$baseFileName-*.ext'
        $originalFileExt  = $file->getClientOriginalExtension();
        $uploadFolderPath = ImageModel::getUploadFolderPath();
        $fileMask = "$uploadFolderPath$baseFileName-*.$originalFileExt";

        // find latest modified file name which starts with $baseFileName
        $latestFile    = null;
        $latestModTime = 0;
        /** @noinspection PhpUndefinedMethodInspection */
        foreach (File::glob($fileMask) as $fileName) {
            /** @noinspection PhpUndefinedMethodInspection */
            if ($curModTime = File::lastModified($fileName) > $latestModTime) {
                $latestFile    = $fileName;
                $latestModTime = $curModTime;
            }
        }

        // if not found then 0 or find a number from end by separator '-'
        $latestIndex = ($latestFile === null) ? 0 : (int)substr($latestFile, 1 + strrpos($latestFile, '-', -1));

        ++$latestIndex;
        /** @noinspection PhpUndefinedMethodInspection */
        while (File::exists("$uploadFolderPath$baseFileName-$latestIndex.$originalFileExt") or
            ImageModel::where('original_file', '=', "$baseFileName-$latestIndex.$originalFileExt")->first() !== null
        ) {
            ++$latestIndex;
        }

        $uniqueFileName = "$baseFileName-$latestIndex.$originalFileExt";
        $file->move($uploadFolderPath, $uniqueFileName);

        return $uniqueFileName;
    }
}
