<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;

/**
 * @property int         id_reset
 * @property string      email
 * @property string      token
 * @property-read Carbon created_at
 * @property-read Carbon updated_at
 */
class CustomerPasswordReset extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'customer_password_resets';

    const EMAIL_MAX_LENGTH = Customer::EMAIL_MAX_LENGTH;
    const TOKEN_MAX_LENGTH = 255;

    const FIELD_ID    = 'id_reset';
    const FIELD_EMAIL = 'email';
    const FIELD_TOKEN = 'token';

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
    public $timestamps = true;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        self::FIELD_EMAIL,
        self::FIELD_TOKEN,
    ];

    /**
     * {@inheritdoc}
     */
    public $guarded = [
        self::FIELD_ID,
    ];

    /**
     * {@inheritdoc}
     */
    public $hidden = [
        self::FIELD_TOKEN,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_EMAIL => 'required|email|max:'.self::EMAIL_MAX_LENGTH.'|unique:'.self::TABLE_NAME,
            self::FIELD_TOKEN => 'sometimes|required|max:'.self::TOKEN_MAX_LENGTH,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_EMAIL => 'sometimes|required|email|max:'.self::EMAIL_MAX_LENGTH.'|unique:'.self::TABLE_NAME,
            self::FIELD_TOKEN => 'sometimes|required|max:'.self::TOKEN_MAX_LENGTH,
        ];
    }
}
