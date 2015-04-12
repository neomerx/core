<?php namespace Neomerx\Core\Models;

use \Illuminate\Foundation\Bus\DispatchesCommands;
use \Neomerx\Core\Commands\DeleteImageFilesCommand;

/**
 * @property int         id_image_path
 * @property int         id_image
 * @property int         id_image_format
 * @property string      path
 * @property Image       image
 * @property ImageFormat format
 *
 * @package Neomerx\Core
 */
class ImagePath extends BaseModel
{
    use DispatchesCommands;

    /** Model table name */
    const TABLE_NAME = 'image_paths';

    /** Model field length */
    const PATH_MAX_LENGTH = 255;

    /** Model field name */
    const FIELD_ID              = 'id_image_path';
    /** Model field name */
    const FIELD_ID_IMAGE        = Image::FIELD_ID;
    /** Model field name */
    const FIELD_ID_IMAGE_FORMAT = ImageFormat::FIELD_ID;
    /** Model field name */
    const FIELD_PATH            = 'path';
    /** Model field name */
    const FIELD_IMAGE           = 'image';
    /** Model field name */
    const FIELD_FORMAT          = 'format';

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
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        self::FIELD_PATH,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID_IMAGE,
        self::FIELD_ID_IMAGE_FORMAT,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_IMAGE,
        self::FIELD_ID_IMAGE_FORMAT,
    ];

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_IMAGE        => 'sometimes|required|forbidden',
            self::FIELD_ID_IMAGE_FORMAT => 'sometimes|required|forbidden',
            self::FIELD_PATH            => 'sometimes|required|forbidden',
        ];
    }

    /**
     * Relation to image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function image()
    {
        return $this->belongsTo(Image::class, self::FIELD_ID_IMAGE, Image::FIELD_ID);
    }

    /**
     * Relation to image format.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function format()
    {
        return $this->belongsTo(ImageFormat::class, self::FIELD_ID_IMAGE_FORMAT, ImageFormat::FIELD_ID);
    }

    /**
     * @inheritdoc
     *
     * Delete all image paths for the format.
     */
    protected function onDeleting()
    {
        $onDeleting = parent::onDeleting();

        $command = app()->make(
            DeleteImageFilesCommand::class,
            [DeleteImageFilesCommand::PARAM_FILE_NAMES => [$this->{self::FIELD_PATH}]]
        );
        $this->dispatch($command);

        return $onDeleting;
    }
}
