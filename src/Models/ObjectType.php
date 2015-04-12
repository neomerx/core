<?php namespace Neomerx\Core\Models;

use \Illuminate\Database\Eloquent\Collection;

/**
 * @property int        id_object_type
 * @property string     code
 * @property Collection roles
 *
 * @package Neomerx\Core
 */
class ObjectType extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'object_types';

    /** Model field length */
    const TYPE_MAX_LENGTH = 150;

    /** Model field name */
    const FIELD_ID    = 'id_object_type';
    /** Model field name */
    const FIELD_TYPE  = 'type';
    /** Model field name */
    const FIELD_ROLES = 'roles';

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
        self::FIELD_TYPE,
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
            self::FIELD_TYPE => 'required|min:1|max:'.self::TYPE_MAX_LENGTH.'|unique:'.self::TABLE_NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_TYPE => 'sometimes|required|forbidden',
        ];
    }

    /**
     * Relation to roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            RoleObjectType::TABLE_NAME,
            RoleObjectType::FIELD_ID_ROLE,
            RoleObjectType::FIELD_ID_TYPE
        );
    }
}
