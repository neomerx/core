<?php namespace Neomerx\Core\Models;

use \Illuminate\Database\Eloquent\Collection;

/**
 * @property int        id_action
 * @property string     code
 * @property Collection roles
 */
class Action extends BaseModel implements SelectByCodeInterface
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'actions';

    const CODE_MAX_LENGTH = 50;

    const FIELD_ID    = 'id_action';
    const FIELD_CODE  = 'code';
    const FIELD_ROLES = 'roles';

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
    protected $guarded = [
        self::FIELD_ID,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_CODE => 'required|code|min:1|max:'.self::CODE_MAX_LENGTH.
                '|unique:'.self::TABLE_NAME,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
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
     * Relation to roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(
            Role::BIND_NAME,
            RoleAction::TABLE_NAME,
            RoleAction::FIELD_ID_ROLE,
            RoleAction::FIELD_ID_ACTION
        );
    }
}
