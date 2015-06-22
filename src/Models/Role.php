<?php namespace Neomerx\Core\Models;

use \Illuminate\Database\Eloquent\Collection;

/**
 * @property int        id_role
 * @property string     code
 * @property Collection employees
 * @property Collection roleObjectTypes
 * @property Collection objectTypes
 *
 * @package Neomerx\Core
 */
class Role extends BaseModel implements SelectByCodeInterface
{
    /** Model table name */
    const TABLE_NAME = 'roles';

    /** Model field length */
    const CODE_MAX_LENGTH        = 50;
    /** Model field length */
    const DESCRIPTION_MAX_LENGTH = 300;

    /** Model field name */
    const FIELD_ID           = 'id_role';
    /** Model field name */
    const FIELD_CODE         = 'code';
    /** Model field name */
    const FIELD_DESCRIPTION  = 'description';
    /** Model field name */
    const FIELD_EMPLOYEES    = 'employees';
    /** Model field name */
    const FIELD_ROLE_OBJECT_TYPES = 'roleObjectTypes';
    /** Model field name */
    const FIELD_OBJECT_TYPES = 'objectTypes';

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
        self::FIELD_CODE,
        self::FIELD_DESCRIPTION,
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
            self::FIELD_CODE        => 'required|code|min:1|max:'.self::CODE_MAX_LENGTH.
                '|unique:'.self::TABLE_NAME,
            self::FIELD_DESCRIPTION => 'sometimes|required|alpha_dash_dot_space|min:1|max:'.
                self::DESCRIPTION_MAX_LENGTH,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_CODE        => 'sometimes|required|forbidden',
            self::FIELD_DESCRIPTION => 'sometimes|required|alpha_dash_dot_space|min:1|max:'.
                self::DESCRIPTION_MAX_LENGTH,
        ];
    }

    /**
     * @inheritdoc
     */
    public function selectByCode($code)
    {
        return $this->newQuery()->where(self::FIELD_CODE, '=', $code);
    }

    /**
     * Relation to employees.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function employees()
    {
        return $this->belongsToMany(
            Employee::class,
            EmployeeRole::TABLE_NAME,
            EmployeeRole::FIELD_ID_ROLE,
            EmployeeRole::FIELD_ID_EMPLOYEE
        );
    }

    /**
     * Relation to role object types.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roleObjectTypes()
    {
        return $this->hasMany(
            RoleObjectType::class,
            RoleObjectType::FIELD_ID_ROLE,
            self::FIELD_ID
        );
    }

    /**
     * Relation to object types.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function objectTypes()
    {
        return $this
            ->belongsToMany(
                ObjectType::class,
                RoleObjectType::TABLE_NAME,
                RoleObjectType::FIELD_ID_ROLE,
                RoleObjectType::FIELD_ID_TYPE
            )->withPivot([RoleObjectType::FIELD_ALLOW_MASK, RoleObjectType::FIELD_DENY_MASK]);
    }
}
