<?php namespace Neomerx\Core\Models;

use \Illuminate\Database\Eloquent\Collection;

/**
 * @property int        id_role
 * @property string     code
 * @property Collection employees
 */
class Role extends BaseModel implements SelectByCodeInterface
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'roles';

    const CODE_MAX_LENGTH        = 50;
    const DESCRIPTION_MAX_LENGTH = 300;

    const FIELD_ID          = 'id_role';
    const FIELD_CODE        = 'code';
    const FIELD_DESCRIPTION = 'description';
    const FIELD_EMPLOYEES   = 'employees';

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
        self::FIELD_DESCRIPTION,
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
            self::FIELD_CODE        => 'required|code|min:1|max:'.self::CODE_MAX_LENGTH.
                '|unique:'.self::TABLE_NAME,
            self::FIELD_DESCRIPTION => 'sometimes|required|alpha_dash_dot_space|min:1|max:'.
                self::DESCRIPTION_MAX_LENGTH,
        ];
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
            Employee::BIND_NAME,
            EmployeeRole::TABLE_NAME,
            EmployeeRole::FIELD_ID_ROLE,
            EmployeeRole::FIELD_ID_EMPLOYEE
        );
    }
}
