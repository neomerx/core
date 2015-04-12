<?php namespace Neomerx\Core\Models;

/**
 * @property int      id_employee_role
 * @property int      id_employee
 * @property int      id_role
 * @property Employee employee
 * @property Role     role
 *
 * @package Neomerx\Core
 */
class EmployeeRole extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'employees_roles';

    /** Model field name */
    const FIELD_ID          = 'id_employee_role';
    /** Model field name */
    const FIELD_ID_EMPLOYEE = Employee::FIELD_ID;
    /** Model field name */
    const FIELD_ID_ROLE     = Role::FIELD_ID;
    /** Model field name */
    const FIELD_EMPLOYEE    = 'employee';
    /** Model field name */
    const FIELD_ROLE        = 'role';

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
        '', // fillable must have at least 1 element otherwise it's ignored completely by Laravel
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID_EMPLOYEE,
        self::FIELD_ID_ROLE,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_EMPLOYEE,
        self::FIELD_ID_ROLE,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_EMPLOYEE => 'required|integer|min:1|max:4294967295|exists:'.Employee::TABLE_NAME,
            self::FIELD_ID_ROLE     => 'required|integer|min:1|max:4294967295|exists:'.Role::TABLE_NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_EMPLOYEE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Employee::TABLE_NAME,
            self::FIELD_ID_ROLE     => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Role::TABLE_NAME,
        ];
    }

    /**
     * @return string
     */
    public static function withEmployee()
    {
        return self::FIELD_EMPLOYEE;
    }

    /**
     * @return string
     */
    public static function withRole()
    {
        return self::FIELD_ROLE;
    }

    /**
     * Relation to employee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, self::FIELD_ID_EMPLOYEE, Employee::FIELD_ID);
    }

    /**
     * Relation to role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class, self::FIELD_ID_ROLE, Role::FIELD_ID);
    }
}
