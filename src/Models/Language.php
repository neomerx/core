<?php namespace Neomerx\Core\Models;

/**
 * @property int    id_language
 * @property string name
 * @property string iso_code
 */
class Language extends BaseModel implements SelectByCodeInterface
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'languages';

    const NAME_MAX_LENGTH     = 50;
    const ISO_CODE_MAX_LENGTH = 3;

    const FIELD_ID       = 'id_language';
    const FIELD_NAME     = 'name';
    const FIELD_ISO_CODE = 'iso_code';

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
            self::FIELD_NAME     => 'required|min:1|max:'.self::NAME_MAX_LENGTH,
            self::FIELD_ISO_CODE => 'required|alpha|min:2|max:'.self::ISO_CODE_MAX_LENGTH.
                '|unique:'.self::TABLE_NAME,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_NAME     => 'sometimes|required|min:1|max:'.self::NAME_MAX_LENGTH,
            self::FIELD_ISO_CODE => 'sometimes|required|alpha|min:2|max:'.self::ISO_CODE_MAX_LENGTH.
                '|unique:'.self::TABLE_NAME,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function selectByCode($isoCode)
    {
        return $this->newQuery()->where(self::FIELD_ISO_CODE, '=', $isoCode);
    }
}
