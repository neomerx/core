<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;
use \Neomerx\Core\Support as S;
use \Illuminate\Database\Eloquent\Collection;
use \Illuminate\Foundation\Bus\DispatchesJobs;
use \Neomerx\Core\Commands\DeleteImageFilesCommand;
use \Neomerx\Core\Commands\CreateImagesByFormatCommand;
use \Neomerx\Core\Commands\UpdateImagesByFormatCommand;

/**
 * @property      int        id_image_format
 * @property      string     code
 * @property      int        width
 * @property      int        height
 * @property-read Carbon     created_at
 * @property-read Carbon     updated_at
 * @property      Collection paths
 * @property      Collection images
 *
 * @package Neomerx\Core
 */
class ImageFormat extends BaseModel
{
    use DispatchesJobs;

    /** Model table name */
    const TABLE_NAME = 'image_formats';

    /** Model field length */
    const NAME_MAX_LENGTH = 50;

    /** Model field name */
    const FIELD_ID     = 'id_image_format';
    /** Model field name */
    const FIELD_CODE   = 'code';
    /** Model field name */
    const FIELD_WIDTH  = 'width';
    /** Model field name */
    const FIELD_HEIGHT = 'height';
    /** Model field name */
    const FIELD_PATHS  = 'paths';
    /** Model field name */
    const FIELD_IMAGES = 'images';
    /** Model field name */
    const FIELD_CREATED_AT = 'created_at';
    /** Model field name */
    const FIELD_UPDATED_AT = 'updated_at';

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
        self::FIELD_CODE,
        self::FIELD_WIDTH,
        self::FIELD_HEIGHT,
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
            self::FIELD_CODE   => 'required|min:1|max:'.self::NAME_MAX_LENGTH.'|unique:'.self::TABLE_NAME,
            self::FIELD_WIDTH  => 'required|integer|min:1|max:4096',
            self::FIELD_HEIGHT => 'required|integer|min:1|max:4096',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_CODE   => 'sometimes|required|forbidden',
            self::FIELD_WIDTH  => 'sometimes|required|integer|min:1|max:4096',
            self::FIELD_HEIGHT => 'sometimes|required|integer|min:1|max:4096',
        ];
    }

    /**
     * Relation to image paths.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paths()
    {
        return $this->hasMany(ImagePath::class, ImagePath::FIELD_ID_IMAGE_FORMAT, self::FIELD_ID);
    }

    /**
     * @inheritdoc
     *
     * Generates image paths for new format.
     */
    protected function onCreated()
    {
        $onCreated = parent::onCreated();

        $command = app()->make(
            CreateImagesByFormatCommand::class,
            [CreateImagesByFormatCommand::PARAM_FORMAT_ID => $this->{self::FIELD_ID}]
        );
        $this->dispatch($command);

        return $onCreated;
    }

    /**
     * @inheritdoc
     *
     * Regenerates all image paths with new format parameters.
     */
    protected function onUpdated()
    {
        $onUpdated = parent::onUpdated();

        $command = app()->make(
            UpdateImagesByFormatCommand::class,
            [UpdateImagesByFormatCommand::PARAM_FORMAT_ID => $this->{self::FIELD_ID}]
        );
        $this->dispatch($command);

        return $onUpdated;
    }

    /**
     * @inheritdoc
     *
     * Delete all image paths for the format.
     */
    protected function onDeleting()
    {
        $onDeleting = parent::onDeleting();

        $fileNames = [];
        foreach ($this->paths as $path) {
            /** @var ImagePath $path */
            $fileNames[] = $path->{ImagePath::FIELD_PATH};
        }

        $command = app()->make(
            DeleteImageFilesCommand::class,
            [DeleteImageFilesCommand::PARAM_FILE_NAMES => $fileNames]
        );
        $this->dispatch($command);

        return $onDeleting;
    }
}
