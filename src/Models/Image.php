<?php namespace Neomerx\Core\Models;

use \Neomerx\Core\Config;
use \Neomerx\Core\Exceptions\Exception;
use \Illuminate\Support\Facades\App;
use \Illuminate\Support\Facades\File;
use \Neomerx\Core\Exceptions\LogicException;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Exceptions\ConfigurationException;
use \Intervention\Image\Facades\Image as ImageProcessor;
use \Intervention\Image\Exception\NotSupportedException;

/**
 * @property int          id_image
 * @property string       original_file
 * @property Collection   paths
 * @property Collection   properties
 * @property ProductImage product_image
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Image extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'images';

    const ORIGINAL_FILE_NAME_MAX_LENGTH = 255;

    const FIELD_ID            = 'id_image';
    const FIELD_ORIGINAL_FILE = 'original_file';
    const FIELD_PROPERTIES    = 'properties';
    const FIELD_PRODUCT_IMAGE = 'product_image';
    const FIELD_PATHS         = 'paths';

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
    protected $fillable = [
        self::FIELD_ORIGINAL_FILE,
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        self::FIELD_ID,
    ];

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

    /**
     * {@inheritdoc}
     */
    public static function getInputOnCreateRules()
    {
        return [
            self::FIELD_ORIGINAL_FILE => 'required|alpha_dash_dot_space|min:1|max:' .
                self::ORIGINAL_FILE_NAME_MAX_LENGTH,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDataOnCreateRules()
    {
        return [
            self::FIELD_ORIGINAL_FILE => 'required|alpha_dash_dot_space|min:1|max:' .
                self::ORIGINAL_FILE_NAME_MAX_LENGTH . '|unique:' . self::TABLE_NAME,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getInputOnUpdateRules()
    {
        return [
            self::FIELD_ORIGINAL_FILE => 'required|alpha_dash_dot_space|min:1|max:' .
                self::ORIGINAL_FILE_NAME_MAX_LENGTH,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ORIGINAL_FILE => 'required|alpha_dash_dot_space|min:1|max:' .
                self::ORIGINAL_FILE_NAME_MAX_LENGTH . '|unique:' . self::TABLE_NAME,
        ];
    }

    /**
     * Relation to image paths with image formats.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function paths()
    {
        return $this->hasMany(ImagePath::BIND_NAME, ImagePath::FIELD_ID_IMAGE, self::FIELD_ID);
    }

    /**
     * Relation to image language properties (alt translations).
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function properties()
    {
        return $this->hasMany(ImageProperties::BIND_NAME, ImageProperties::FIELD_ID_IMAGE, self::FIELD_ID);
    }

    /**
     * Relation to product images.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function productImage()
    {
        return $this->hasOne(ProductImage::BIND_NAME, ProductImage::FIELD_ID_IMAGE, self::FIELD_ID);
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
     * {@inheritdoc}
     *
     * Check if uploaded file is valid image.
     */
    protected function onCreating()
    {
        if (!parent::onCreating()) {
            return false;
        }

        $imageFileIsValid = true;
        try {
            $realPath = Image::getUploadFolderPath($this->original_file);
            /** @noinspection PhpUndefinedMethodInspection */
            ImageProcessor::make($realPath);
        } catch (NotSupportedException $e) {
            $imageFileIsValid = false;
        }

        return $imageFileIsValid;
    }

    /**
     * {@inheritdoc}
     *
     * When model saved we should generate the image in required formats.
     */
    protected function onCreated()
    {
        $parentOnCreate = parent::onCreated();

        $this->createImagePathsForAllFormats();

        return $parentOnCreate;
    }

    /**
     * {@inheritdoc}
     */
    protected function onUpdating()
    {
        $parentOnUpdating = parent::onUpdating();
        $generateImagesIsOK = true;
        try {
            $this->generateImages();
        } catch (Exception $e) { // \Neomerx\Exceptions\Exception
            $generateImagesIsOK = false;
        }
        return $parentOnUpdating and $generateImagesIsOK;
    }

    /**
     * {@inheritdoc}
     */
    protected function onDeleting()
    {
        $imageCouldBeDeleted = true;

        try {
            $originalFileFullPath = Image::getUploadFolderPath($this->original_file);
            /** @noinspection PhpUndefinedMethodInspection */
            if (File::exists($originalFileFullPath) and !File::isWritable($originalFileFullPath)) {
                throw new LogicException();
            }

            $this->deleteImagePaths();

            /** @noinspection PhpUndefinedMethodInspection */
            if (!File::delete($originalFileFullPath)) {
                throw new LogicException();
            }

        } catch (LogicException $e) {
            $imageCouldBeDeleted = false;
        }

        return parent::onDeleting() and $imageCouldBeDeleted;
    }

    /**
     * Create image paths for all image formats.
     *
     * @throws \Neomerx\Core\Exceptions\Exception
     */
    private function createImagePathsForAllFormats()
    {
        $formats = ImageFormat::all();
        $background = $this->getBackground();
        foreach ($formats as $format) {
            /** @var ImagePath $imagePath */
            /** @noinspection PhpUndefinedMethodInspection */
            $imagePath = App::make(ImagePath::BIND_NAME);
            $imagePath->fill([ImagePath::FIELD_ID_IMAGE_FORMAT => $format->{ImageFormat::FIELD_ID}]);
            $imagePath->setBackground($background);
            /** @noinspection PhpUndefinedMethodInspection */
            if (!$this->paths()->save($imagePath)) {
                $this->deleteOrFail();
                throw new Exception('Create image failed.');
            }
        }
    }

    /**
     * Delete all children images (image paths) and re-create them.
     */
    public function generateImages()
    {
        $this->deleteImagePaths();
        $this->createImagePathsForAllFormats();
    }

    /**
     * Get full path to upload (writable) folder.
     *
     * @param string $fileName
     *
     * @return string
     * @throws \Neomerx\Core\Exceptions\ConfigurationException
     */
    public static function getUploadFolderPath($fileName = null)
    {
        settype($fileName, 'string');

        $path = trim(Config::get(Config::FILE_APP, Config::KEY_IMAGE_FOLDER));

        if (substr($path, -1) !== DIRECTORY_SEPARATOR) {
            throw new ConfigurationException(Config::KEY_IMAGE_FOLDER);
        }

        return $fileName === null ? $path : $path . $fileName;
    }

    /**
     * Delete all children images (image paths).
     *
     * @throws \Neomerx\Core\Exceptions\LogicException
     */
    private function deleteImagePaths()
    {
        foreach ($this->paths as $imagePath) {
            /** @noinspection PhpUndefinedMethodInspection */
            $imagePath->deleteOrFail();
        }
    }
}
