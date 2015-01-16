<?php namespace Neomerx\Core\Models;

/**
 * @property int      id_employee_role
 * @property int      id_employee
 * @property int      id_role
 * @property Employee employee
 * @property Role     role
 */
class EmployeeRole extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'employees_roles';

    const FIELD_ID          = 'id_employee_role';
    const FIELD_ID_EMPLOYEE = Employee::FIELD_ID;
    const FIELD_ID_ROLE     = Role::FIELD_ID;
    const FIELD_EMPLOYEE    = 'employee';
    const FIELD_ROLE        = 'role';

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
        self::FIELD_ID_EMPLOYEE,
        self::FIELD_ID_ROLE,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_EMPLOYEE,
        self::FIELD_ID_ROLE,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_EMPLOYEE => 'required|integer|min:1|max:4294967295|exists:'.Employee::TABLE_NAME,
            self::FIELD_ID_ROLE     => 'required|integer|min:1|max:4294967295|exists:'.Role::TABLE_NAME,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_EMPLOYEE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Employee::TABLE_NAME,
            self::FIELD_ID_ROLE     => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Role::TABLE_NAME,
        ];
    }

    /**
     * Relation to employee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::BIND_NAME, self::FIELD_ID_EMPLOYEE, Employee::FIELD_ID);
    }

    /**
     * Relation to role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::BIND_NAME, self::FIELD_ID_ROLE, Role::FIELD_ID);
    }
}
