<?php namespace Neomerx\Core\Commands;

use \Neomerx\Core\Support as S;
use \Illuminate\Queue\Jobs\Job;
use \Neomerx\Core\Models\ImagePath;
use \Illuminate\Support\Facades\Log;
use \Neomerx\Core\Models\ImageFormat;

class GenerateImageCommand
{
    /**
     * @param Job   $job
     * @param array $data
     */
    public function byFormat(Job $job, array $data)
    {
        $formatCode = S\arrayGetValue($data, ImageFormat::FIELD_CODE);

        if ($formatCode !== null) {
            /** @var ImageFormat $imageFormatModel */
            $imageFormatModel = new ImageFormat();
            /** @var ImagePath $path */
            $imageFormatModel = $imageFormatModel->selectByCode($formatCode)->firstOrFail();

            foreach ($imageFormatModel->paths as $path) {
                $imageGenerated = $path->generateImage();
                /** @noinspection PhpUndefinedMethodInspection */
                $imageGenerated === true ?: Log::error(trans('nm::errors.image_generation_failed'));
            }
        }

        $job->delete();
    }
}
