<?php namespace Neomerx\Core\Models;

use \Illuminate\Support\Facades\File;
use \Intervention\Image\Exception\NotWritableException;
use \Intervention\Image\Facades\Image as ImageProcessor;

/**
 * @property int         id_image_path
 * @property int         id_image
 * @property int         id_image_format
 * @property string      path
 * @property Image       image
 * @property ImageFormat format
 */
class ImagePath extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'image_paths';

    const PATH_MAX_LENGTH = 255;

    const FIELD_ID              = 'id_image_path';
    const FIELD_ID_IMAGE        = Image::FIELD_ID;
    const FIELD_ID_IMAGE_FORMAT = ImageFormat::FIELD_ID;
    const FIELD_PATH            = 'path';
    const FIELD_IMAGE           = 'image';
    const FIELD_FORMAT          = 'format';

    /**
     * Background used when converting images to specified formats.
     *
     * @var string
     */
    private $background = 'rgba(255, 255, 255, 0)';

    /**
     * {@inheritdoc}
     */
    protected $table = self::TABLE_NAME;

    /**
     * {@inheritdoc}
     */
    protected $primaryKey = self::FIELD_ID;

    /**
     * {@inheritdoc}
     */
    public $incrementing = true;

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        self::FIELD_PATH,
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        self::FIELD_ID_IMAGE,
        self::FIELD_ID_IMAGE_FORMAT,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_IMAGE,
        self::FIELD_ID_IMAGE_FORMAT,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_IMAGE        => 'required|integer|min:1|max:4294967295|exists:'.Image::TABLE_NAME,
            self::FIELD_ID_IMAGE_FORMAT => 'required|integer|min:1|max:4294967295|exists:'.ImageFormat::TABLE_NAME,

            self::FIELD_PATH => 'required|alpha_dash_dot_space|min:1|max:'.
                self::PATH_MAX_LENGTH.'|unique:'.self::TABLE_NAME,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_IMAGE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Image::TABLE_NAME,

            self::FIELD_ID_IMAGE_FORMAT => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                ImageFormat::TABLE_NAME,

            self::FIELD_PATH => 'sometimes|required|alpha_dash_dot_space|min:1|max:'.self::PATH_MAX_LENGTH.
                '|unique:'.self::TABLE_NAME,
        ];
    }

    /**
     * Relation to image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function image()
    {
        return $this->belongsTo(Image::BIND_NAME, self::FIELD_ID_IMAGE, Image::FIELD_ID);
    }

    /**
     * Relation to image format.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function format()
    {
        return $this->belongsTo(ImageFormat::BIND_NAME, self::FIELD_ID_IMAGE_FORMAT, ImageFormat::FIELD_ID);
    }

    /**
     * {@inheritdoc}
     */
    protected function onCreating()
    {
        // As we modify 'path' attribute in generateImage() we call parent::onCreating() after.
        // parent::onCreating() checks attributes with validation rules specified in getDataOnCreateRules().
        $imagePathFileCreated = $this->generateImage();
        return parent::onCreating() and $imagePathFileCreated;
    }

    /**
     * {@inheritdoc}
     */
    protected function onUpdating()
    {
        // As we modify 'path' attribute in generateImage() we call parent::onUpdating() after.
        // parent::onUpdating() checks attributes with validation rules specified in getDataOnUpdateRules().
        $imagePathFileUpdated = $this->generateImage();
        return parent::onUpdating() and $imagePathFileUpdated;
    }

    /**
     * Check if underlying file could be deleted.
     *
     * @return bool
     */
    protected function onDeleting()
    {
        $fullPathToFile = Image::getUploadFolderPath($this->path);
        /** @noinspection PhpUndefinedMethodInspection */
        return (parent::onDeleting() and
            (File::exists($fullPathToFile) === true ? File::isWritable($fullPathToFile) : true));
    }

    /**
     * Delete underlying file.
     *
     * @return bool
     */
    protected function onDeleted()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return parent::onDeleted() and File::delete(Image::getUploadFolderPath($this->path));
    }

    /**
     * @param string $background
     */
    public function setBackground($background)
    {
        $this->background = $background;
    }

    /**
     * @return string
     */
    public function getBackground()
    {
        return $this->background;
    }

    /**
     * Generate image file based on parent image and image format.
     *
     * The method modifies 'path' attribute of the model instance.
     *
     * @return bool If image file was created successfully.
     */
    public function generateImage()
    {
        $format           = $this->format;
        $originalFileName = $this->image->original_file;

        $destinationFolder   = Image::getUploadFolderPath();
        $destinationFileName = pathinfo($originalFileName, PATHINFO_FILENAME);
        $destinationFileExt  = pathinfo($originalFileName, PATHINFO_EXTENSION);

        $resizedFileName = $destinationFileName.'-'.$format->name.'.'.$destinationFileExt;
        $resizedFilePath = $destinationFolder.$resizedFileName;

        // if we update an existing model...
        if ($this->exists === true and isset($this->path) === true) {
            //... and we don't have enough permissions to delete old one and create new file...
            /** @noinspection PhpUndefinedMethodInspection */
            if (File::isWritable(Image::getUploadFolderPath($this->path)) === false or
                File::isWritable($resizedFilePath) === false
            ) {
                //... we cancel such change
                return false;
            } else {
                //... we generate image and delete the old file.
                $fullPathToOldFile = Image::getUploadFolderPath($this->path);
                if (true === $this->generateImageAndSetPathAttribute(
                    $destinationFolder,
                    $originalFileName,
                    $format,
                    $resizedFilePath,
                    $resizedFileName
                )) {
                    /** @noinspection PhpUndefinedMethodInspection */
                    return File::delete($fullPathToOldFile);
                } else {
                    //... if we can't generate new image we cancel the change
                    return false;
                }
            }
        } else {
            // we generate image for 'new'/'not yet saved' model.
            return $this->generateImageAndSetPathAttribute(
                $destinationFolder,
                $originalFileName,
                $format,
                $resizedFilePath,
                $resizedFileName
            );
        }
    }

    /**
     * @param string      $destinationFolder
     * @param string      $originalFileName
     * @param ImageFormat $format
     * @param string      $resizedFilePath
     * @param string      $resizedFileName
     *
     * @return bool
     */
    private function generateImageAndSetPathAttribute(
        $destinationFolder,
        $originalFileName,
        ImageFormat $format,
        $resizedFilePath,
        $resizedFileName
    ) {
        try {
            /** @noinspection PhpUndefinedMethodInspection */
            ImageProcessor::make($destinationFolder.'/'.$originalFileName)
                ->resize($format->width, $format->height, true, true)
                ->resizeCanvas($format->width, $format->height, 'center', false, $this->getBackground())
                ->save($resizedFilePath);

            $this->path = $resizedFileName;
        } catch (NotWritableException $e) {
            return false;
        }
        return true;
    }
}
