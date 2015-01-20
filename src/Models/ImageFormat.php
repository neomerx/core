<?php namespace Neomerx\Core\Models;

use \Neomerx\Core\Support as S;
use \Illuminate\Support\Facades\Queue;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Commands\GenerateImageCommand;

/**
 * @property int        id_image_format
 * @property string     code
 * @property int        width
 * @property int        height
 * @property Collection paths
 */
class ImageFormat extends BaseModel implements SelectByCodeInterface
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'image_formats';

    const NAME_MAX_LENGTH = 50;

    const FIELD_ID     = 'id_image_format';
    const FIELD_CODE   = 'code';
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
        self::FIELD_CODE,
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
    protected $guarded = [
        self::FIELD_ID,
    ];

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
        return $this->hasMany(ImagePath::BIND_NAME, ImagePath::FIELD_ID_IMAGE_FORMAT, self::FIELD_ID);
    }

    /**
     * Select format by code.
     *
     * @param string $code
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function selectByCode($code)
    {
        return $this->newQuery()->where(self::FIELD_CODE, '=', $code);
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
        Queue::push(GenerateImageCommand::class.'@byFormat', [self::FIELD_CODE => $this->{self::FIELD_CODE}]);

        return $onUpdated;
    }
}
