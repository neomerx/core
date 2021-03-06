<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;
use \Illuminate\Database\Eloquent\Collection;
use \Illuminate\Foundation\Bus\DispatchesJobs;
use \Neomerx\Core\Commands\DeleteImageFilesCommand;
use \Neomerx\Core\Commands\CreateImagesByImageCommand;

/**
 * @property      int          id_image
 * @property      string       original_ext
 * @property-read Carbon       created_at
 * @property-read Carbon       updated_at
 * @property      Collection   paths
 * @property      Collection   properties
 * @property      ProductImage productImage
 *
 * @package Neomerx\Core
 */
class Image extends BaseModel
{
    use DispatchesJobs;

    /** Model table name */
    const TABLE_NAME = 'images';

    /** Model field length */
    const ORIGINAL_FILE_EXT_MAX_LENGTH = 10;

    /** Model field name */
    const FIELD_ID            = 'id_image';
    /** Model field name */
    const FIELD_ORIGINAL_EXT  = 'original_ext';
    /** Model field name */
    const FIELD_PROPERTIES    = 'properties';
    /** Model field name */
    const FIELD_PRODUCT_IMAGE = 'productImage';
    /** Model field name */
    const FIELD_PATHS         = 'paths';
    /** Model field name */
    const FIELD_CREATED_AT    = 'created_at';
    /** Model field name */
    const FIELD_UPDATED_AT    = 'updated_at';

    /**
     * @inheritdoc
     */
    protected $table = self::TABLE_NAME;

    /**
     * @inheritdoc
     */
    protected $primaryKey = self::FIELD_ID;

    /**
     * @inheritdoc
     */
    public $incrementing = true;

    /**
     * @inheritdoc
     */
    public $timestamps = true;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        self::FIELD_ORIGINAL_EXT,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ORIGINAL_EXT => 'required|alpha_dash|min:1|max:'.self::ORIGINAL_FILE_EXT_MAX_LENGTH,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ORIGINAL_EXT => 'sometimes|required|forbidden',
        ];
    }

    /**
     * @return string
     */
    public static function withProperties()
    {
        return self::FIELD_PROPERTIES.'.'.ImageProperty::FIELD_LANGUAGE;
    }

    /**
     * @return string
     */
    public static function withPaths()
    {
        return self::FIELD_PATHS.'.'.ImagePath::FIELD_FORMAT;
    }

    /**
     * Relation to image paths with image formats.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paths()
    {
        return $this->hasMany(ImagePath::class, ImagePath::FIELD_ID_IMAGE, self::FIELD_ID);
    }

    /**
     * Relation to image language properties (alt translations).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(ImageProperty::class, ImageProperty::FIELD_ID_IMAGE, self::FIELD_ID);
    }

    /**
     * Relation to product images.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function productImage()
    {
        return $this->hasOne(ProductImage::class, ProductImage::FIELD_ID_IMAGE, self::FIELD_ID);
    }

    /**
     * @inheritdoc
     *
     * When model saved we should generate the image in required formats.
     */
    protected function onCreated()
    {
        $parentOnCreate = parent::onCreated();

        $command = app()->make(
            CreateImagesByImageCommand::class,
            [CreateImagesByImageCommand::PARAM_ID_IMAGE => $this->{self::FIELD_ID}]
        );
        $this->dispatch($command);

        return $parentOnCreate;
    }

    /**
     * @inheritdoc
     */
    protected function onDeleting()
    {
        $parentOnDeleted = parent::onDeleting();

        // collect all files to delete (original + in various formats) then send to delete them

        /** @var array<string> $pathFileNames */
        $pathFileNames = [];
        foreach ($this->{self::FIELD_PATHS} as $path) {
            /** @var ImagePath $path */
            $pathFileNames[] = $path->{ImagePath::FIELD_PATH};
        }
        $pathFileNames[] = $this->getKey() . '.' . $this->{self::FIELD_ORIGINAL_EXT};

        $command = app()->make(
            DeleteImageFilesCommand::class,
            [DeleteImageFilesCommand::PARAM_FILE_NAMES => $pathFileNames]
        );
        $this->dispatch($command);

        return $parentOnDeleted;
    }
}
