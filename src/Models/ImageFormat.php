<?php namespace Neomerx\Core\Models;

use \Neomerx\Core\Support as S;
use \Illuminate\Support\Facades\Queue;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Commands\GenerateImageCommand;

/**
 * @property int        id_image_format
 * @property string     name
 * @property int        width
 * @property int        height
 * @property Collection paths
 */
class ImageFormat extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'image_formats';

    const NAME_MAX_LENGTH = 50;

    const FIELD_ID     = 'id_image_format';
    const FIELD_NAME   = 'name';
    const FIELD_WIDTH  = 'width';
    const FIELD_HEIGHT = 'height';
    const FIELD_PATHS  = 'paths';

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
        self::FIELD_NAME,
        self::FIELD_WIDTH,
        self::FIELD_HEIGHT,
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
    public static function getInputOnCreateRules()
    {
        return [
            self::FIELD_NAME   => 'required|min:1|max:' . self::NAME_MAX_LENGTH,
            self::FIELD_WIDTH  => 'required|integer|min:1|max:4096',
            self::FIELD_HEIGHT => 'required|integer|min:1|max:4096',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDataOnCreateRules()
    {
        return [
            self::FIELD_NAME   => 'required|min:1|max:' . self::NAME_MAX_LENGTH . '|unique:' . self::TABLE_NAME,
            self::FIELD_WIDTH  => 'required|integer|min:1|max:4096',
            self::FIELD_HEIGHT => 'required|integer|min:1|max:4096',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getInputOnUpdateRules()
    {
        return [
            self::FIELD_NAME   => 'sometimes|required|forbidden',
            self::FIELD_WIDTH  => 'sometimes|required|integer|min:1|max:4096',
            self::FIELD_HEIGHT => 'sometimes|required|integer|min:1|max:4096',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDataOnUpdateRules()
    {
        return [
            self::FIELD_NAME   => 'sometimes|required|forbidden',
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
        return $this->hasMany(ImagePath::BIND_NAME, ImagePath::FIELD_ID_IMAGE_FORMAT, self::FIELD_ID);
    }

    /**
     * Select format by name.
     *
     * @param string $name
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function selectByName($name)
    {
        return $this->newQuery()->where(self::FIELD_NAME, '=', $name);
    }

    /**
     * {@inheritdoc}
     *
     * Regenerates all image paths with new format parameters.
     */
    protected function onUpdated()
    {
        $onUpdated = parent::onUpdated();

        /** @noinspection PhpUndefinedMethodInspection */
        Queue::push(GenerateImageCommand::class.'@byFormat', [self::FIELD_NAME => $this->name]);

        return $onUpdated;
    }
}
