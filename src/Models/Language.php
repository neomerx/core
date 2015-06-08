<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;

/**
 * @property int         id_language
 * @property string      name
 * @property string      iso_code
 * @property-read Carbon created_at
 * @property-read Carbon updated_at
 *
 * @package Neomerx\Core
 */
class Language extends BaseModel implements SelectByCodeInterface
{
    /** Model table name */
    const TABLE_NAME = 'languages';

    /** Model field length */
    const NAME_MAX_LENGTH     = 50;
    /** Model field length */
    const ISO_CODE_MAX_LENGTH = 3;

    /** Model field name */
    const FIELD_ID       = 'id_language';
    /** Model field name */
    const FIELD_NAME     = 'name';
    /** Model field name */
    const FIELD_ISO_CODE = 'iso_code';
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
        self::FIELD_NAME,
        self::FIELD_ISO_CODE,
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
            self::FIELD_NAME     => 'required|min:1|max:'.self::NAME_MAX_LENGTH,
            self::FIELD_ISO_CODE => 'required|alpha|min:2|max:'.self::ISO_CODE_MAX_LENGTH.
                '|unique:'.self::TABLE_NAME,
        ];
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function selectByCode($isoCode)
    {
        return $this->newQuery()->where(self::FIELD_ISO_CODE, '=', $isoCode);
    }
}
