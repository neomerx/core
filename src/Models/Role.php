<?php namespace Neomerx\Core\Models;

use \Illuminate\Database\Eloquent\Collection;

/**
 * @property int    id_role
 * @property string code
 * @property Collection  users
 */
class Role extends BaseModel implements SelectByCodeInterface
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'roles';

    const CODE_MAX_LENGTH = 50;

    const FIELD_ID    = 'id_role';
    const FIELD_CODE  = 'code';
    const FIELD_USERS = 'users';

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
            self::FIELD_CODE => 'required|alpha_dash|min:1|max:' . self::CODE_MAX_LENGTH,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDataOnCreateRules()
    {
        return [
            self::FIELD_CODE => 'required|alpha_dash|min:1|max:' . self::CODE_MAX_LENGTH .
                '|unique:' . self::TABLE_NAME,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getInputOnUpdateRules()
    {
        return [
            self::FIELD_CODE => 'sometimes|required|forbidden',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDataOnUpdateRules()
    {
        return [
            self::FIELD_CODE => 'sometimes|required|forbidden',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function selectByCode($code)
    {
        return $this->newQuery()->where(self::FIELD_CODE, '=', $code);
    }

    /**
     * Relation to users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(
            User::BIND_NAME,
            UserRole::TABLE_NAME,
            UserRole::FIELD_ID_ROLE,
            UserRole::FIELD_ID_USER
        );
    }
}
