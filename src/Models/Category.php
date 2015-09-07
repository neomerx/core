<?php namespace Neomerx\Core\Models;

use \Carbon\Carbon;
use \Illuminate\Support\Facades\DB;
use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

/**
 * @property      int        id_category
 * @property      int        id_ancestor
 * @property      string     code
 * @property      string     link
 * @property      bool       enabled
 * @property      int        lft
 * @property      int        rgt
 * @property-read string     ancestor_code
 * @property-read int        number_of_descendants
 * @property-read Carbon     created_at
 * @property-read Carbon     updated_at
 * @property      Category   ancestor
 * @property      Collection properties
 * @property      Collection products
 * @property      Collection assignedProducts
 * @property      Collection productCategories
 *
 * @package Neomerx\Core
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Category extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'categories';

    /** Root category code */
    const ROOT_CODE       = '-';
    /** Model field length */
    const CODE_MAX_LENGTH = 50;
    /** Model field length */
    const LINK_MAX_LENGTH = 50;

    /** Model field name */
    const FIELD_ID                    = 'id_category';
    /** Model field name */
    const FIELD_CODE                  = 'code';
    /** Model field name */
    const FIELD_LINK                  = 'link';
    /** Model field name */
    const FIELD_LFT                   = 'lft';
    /** Model field name */
    const FIELD_RGT                   = 'rgt';
    /** Model field name */
    const FIELD_ENABLED               = 'enabled';
    /** Model field name */
    const FIELD_ID_ANCESTOR           = 'id_ancestor';
    /** Model field name */
    const FIELD_ANCESTOR_CODE         = 'ancestor_code';
    /** Model field name */
    const FIELD_NUMBER_OF_DESCENDANTS = 'number_of_descendants';
    /** Model field name */
    const FIELD_ANCESTOR              = 'ancestor';
    /** Model field name */
    const FIELD_PROPERTIES            = 'properties';
    /** Model field name */
    const FIELD_PRODUCTS              = 'products';
    /** Model field name */
    const FIELD_ASSIGNED_PRODUCTS     = 'assignedProducts';
    /** Model field name */
    const FIELD_PRODUCT_CATEGORIES    = 'productCategories';
    /** Model field name */
    const FIELD_CREATED_AT            = 'created_at';
    /** Model field name */
    const FIELD_UPDATED_AT            = 'updated_at';

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
     * @var string Stores ancestor code of the category.
     */
    private $ancestorCode;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        self::FIELD_CODE,
        self::FIELD_LINK,
        self::FIELD_ANCESTOR_CODE,
        self::FIELD_ENABLED,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID,
        self::FIELD_ID_ANCESTOR,
        self::FIELD_LFT,
        self::FIELD_RGT,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_ANCESTOR,
        self::FIELD_LFT,
        self::FIELD_RGT,
    ];

    /**
     * @inheritdoc
     */
    protected $appends = [
        self::FIELD_NUMBER_OF_DESCENDANTS,
    ];

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_ANCESTOR   => 'required|integer|min:1|max:4294967295|exists:'.
                self::TABLE_NAME.','.self::FIELD_ID,

            self::FIELD_CODE => 'required|code|min:1|max:'.
                self::CODE_MAX_LENGTH.'|unique:'.self::TABLE_NAME,

            self::FIELD_LINK => 'required|min:1|max:'.self::LINK_MAX_LENGTH.'|unique:'.self::TABLE_NAME,

            self::FIELD_ANCESTOR_CODE => 'sometimes|required|exists:'.self::TABLE_NAME.','.self::FIELD_CODE.
                '|exists:'.self::TABLE_NAME.','.self::FIELD_CODE,

            self::FIELD_ENABLED => 'required|boolean',

            self::FIELD_LFT  => 'required|integer|min:0|max:4294967295|different:'.self::FIELD_RGT.'|unique:'.
                self::TABLE_NAME.','.self::FIELD_LFT.'|unique:'.self::TABLE_NAME.','.self::FIELD_RGT,

            self::FIELD_RGT  => 'required|integer|min:0|max:4294967295|different:'.self::FIELD_LFT.'|unique:'.
                self::TABLE_NAME.','.self::FIELD_LFT.'|unique:'.self::TABLE_NAME.','.self::FIELD_RGT,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_ANCESTOR   => 'sometimes|required|integer|min:1|max:4294967295|exists:'.
                self::TABLE_NAME.','.self::FIELD_ID,

            self::FIELD_CODE => 'sometimes|required|forbidden',

            self::FIELD_LINK => 'sometimes|required|min:1|max:'.self::LINK_MAX_LENGTH.'|unique:'.self::TABLE_NAME,

            self::FIELD_ENABLED => 'sometimes|required|boolean',

            self::FIELD_LFT => 'required_with:'.self::FIELD_RGT.'|integer|min:0|max:4294967295|different:'.
                self::FIELD_RGT.'|unique:'.self::TABLE_NAME.','.self::FIELD_LFT.'|'.'unique:'.
                self::TABLE_NAME.','.self::FIELD_RGT,

            self::FIELD_RGT => 'required_with:'.self::FIELD_LFT.'|integer|min:0|max:4294967295|different:'.
                self::FIELD_LFT.'|unique:'.self::TABLE_NAME.','.self::FIELD_LFT.'|'.'unique:'.
                self::TABLE_NAME.','.self::FIELD_RGT,
        ];
    }

    /**
     * Relation to properties.
     *
     * @return string
     */
    public static function withProperties()
    {
        return self::FIELD_PROPERTIES.'.'.CategoryProperty::FIELD_LANGUAGE;
    }

    /**
     * @param string $value
     */
    public function setAncestorCodeAttribute($value)
    {
        $this->ancestorCode = $value;
    }

    /**
     * @return string
     */
    public function getAncestorCodeAttribute()
    {
        return $this->ancestorCode;
    }

    /**
     * @return integer
     */
    public function getNumberOfDescendantsAttribute()
    {
        return (int)(($this->rgt - $this->lft - 1) / 2);
    }

    /**
     * Relation to categories language properties (name translations).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(CategoryProperty::class, CategoryProperty::FIELD_ID_CATEGORY, self::FIELD_ID);
    }

    /**
     * Relation to products.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function assignedProducts()
    {
        return $this->belongsToMany(
            Product::class,
            ProductCategory::TABLE_NAME,
            ProductCategory::FIELD_ID_CATEGORY,
            ProductCategory::FIELD_ID_PRODUCT
        )->withPivot(ProductCategory::FIELD_POSITION);
    }

    /**
     * Relation to product categories.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productCategories()
    {
        return $this->hasMany(ProductCategory::class, ProductCategory::FIELD_ID_CATEGORY, self::FIELD_ID);
    }

    /**
     * Relation to ancestor category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function ancestor()
    {
        return $this->hasOne(self::class, self::FIELD_ID, self::FIELD_ID_ANCESTOR);
    }
    /**
     * @param mixed $value
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getEnabledAttribute($value)
    {
        return (bool)$value;
    }

    /**
     * @inheritdoc
     */
    protected function onCreating()
    {
        /** @var Category $parentCategory */
        $parentCategory = $this
            ->selectByCode($this->ancestor_code !== null ? $this->ancestor_code : self::ROOT_CODE)
            ->firstOrFail();
        $parentRight = $parentCategory->rgt;

        $this->lft = $parentRight;
        $this->rgt = $parentRight + 1;
        $this->id_ancestor = $parentCategory->{self::FIELD_ID};
        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {
            // orderBy clause covers this MySQL bug http://bugs.mysql.com/bug.php?id=18913
            /** @noinspection PhpUndefinedMethodInspection */
            $this->newQuery()->where(self::FIELD_RGT, '>=', $parentRight)
                ->orderBy(self::FIELD_RGT, 'desc')->increment(self::FIELD_RGT, 2);
            /** @noinspection PhpUndefinedMethodInspection */
            $this->newQuery()->where(self::FIELD_LFT, '>', $parentRight)
                ->orderBy(self::FIELD_LFT, 'desc')->increment(self::FIELD_LFT, 2);

            // if all executes OK and parent returns 'true' we will commit changes in 'finally'.
            $allExecutedOk = parent::onCreating();
        } finally {

            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) === true ? DB::commit() : DB::rollBack();

        }
        return $allExecutedOk;
    }

    /**
     * @inheritdoc
     */
    protected function onUpdating()
    {
        // root category can't be changed
        $allowUpdate = $this->code !== self::ROOT_CODE;

        // if ancestor code has been changed then we update the model in database
        if ($allowUpdate === true && $this->ancestorCode !== null) {
            /** @var Category $parentCategory */
            $parentCategory = $this->selectByCode($this->ancestorCode)->firstOrFail();
            $this->attachToCategory($parentCategory);
        }

        return parent::onUpdating() && $allowUpdate;
    }

    /**
     * @inheritdoc
     */
    protected function onDeleting()
    {
        // root category can't be deleted
        if ($this->code === self::ROOT_CODE) {
            return false;
        }

        $left  = $this->lft;
        $right = $this->rgt;
        $shift = $right - $left + 1;

        /** @noinspection PhpUnusedLocalVariableInspection */
        $allExecutedOk = false;
        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->newQuery()->whereBetween(self::FIELD_LFT, [$left, $right])->delete();
            // remove a gap after the move, orderBy clause covers this MySQL bug http://bugs.mysql.com/bug.php?id=18913
            /** @noinspection PhpUndefinedMethodInspection */
            $this->newQuery()->where(self::FIELD_LFT, '>', $right)
                ->orderBy(self::FIELD_LFT, 'asc')->decrement(self::FIELD_LFT, $shift);
            /** @noinspection PhpUndefinedMethodInspection */
            $this->newQuery()->where(self::FIELD_RGT, '>', $right)
                ->orderBy(self::FIELD_RGT, 'asc')->decrement(self::FIELD_RGT, $shift);

            // if all executes OK and parent returns 'true' we will commit changes in 'finally'.
            $allExecutedOk = parent::onDeleting();
        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            $allExecutedOk === true ? DB::commit() : DB::rollBack();
        }

        return $allExecutedOk;
    }

    /**
     * Switch the subtree node with a node on the left if there are any with the same parent.
     *
     * @return void
     */
    public function moveLeft()
    {
        /** @var Category $nodeOnTheLeft */
        $nodeOnTheLeft = $this->newQuery()->where(self::FIELD_RGT, '=', $this->lft - 1)->first();
        if ($nodeOnTheLeft !== null) {
            $this->moveToPosition($nodeOnTheLeft->lft, [$this->{self::FIELD_ID}, $nodeOnTheLeft->{self::FIELD_ID}]);
        }
    }

    /**
     * Switch the subtree node with a node on the right if there are any with the same parent.
     *
     * @return void
     */
    public function moveRight()
    {
        /** @var Category $nodeOnTheRight */
        $nodeOnTheRight = $this->newQuery()->where(self::FIELD_LFT, '=', $this->rgt + 1)->first();
        if ($nodeOnTheRight !== null) {
            $this->moveToPosition(
                $nodeOnTheRight->rgt + 1,
                [$this->{self::FIELD_ID}, $nodeOnTheRight->{self::FIELD_ID}]
            );
        }
    }

    /**
     * Moves th subtree as a child of a node with code $code.
     *
     * @param $code
     *
     * @return void
     *
     * @throws \Neomerx\Core\Exceptions\InvalidArgumentException
     */
    public function attachTo($code)
    {
        /** @var Category $newParent */
        $newParent = $this->selectByCode($code)->firstOrFail();
        $this->attachToCategory($newParent);
    }


    /**
     * @param Category $newParent
     *
     * @throws \Neomerx\Core\Exceptions\InvalidArgumentException
     */
    public function attachToCategory(Category $newParent)
    {
        // check 'new parent' is not actually a it's own child
        $newParentLeft  = $newParent->lft;
        $newParentRight = $newParent->rgt;
        $left = $this->lft;
        $right = $this->rgt;
        if (($left <= $newParentLeft && $newParentLeft <= $right) ||
            ($left <= $newParentRight && $newParentRight <= $right)
        ) {
            throw new InvalidArgumentException(self::FIELD_CODE);
        }

        $this->moveToPosition(
            $newParent->rgt,
            [$this->{self::FIELD_ID}, $this->id_ancestor, $newParent->{self::FIELD_ID}],
            $newParent->{self::FIELD_ID}
        );
    }

    /**
     * @param int      $newLft
     * @param array    $touchIds
     * @param int|null $updateAncestorIdTo
     *
     * @return void
     */
    private function moveToPosition($newLft, array $touchIds, $updateAncestorIdTo = null)
    {
        $left     = (int)$this->lft;
        $right    = (int)$this->rgt;
        $shift    = $right - $left + 1;
        $distance = $newLft - $left;
        $tmpPos   = $left;

        if ($distance < 0) {
            $distance -= $shift;
            $tmpPos   += $shift;
        }

        // during update we will update timestamps manually. Functions 'increment' and 'decrement' change
        // timestamps for every row they change. In most cases we don't want that as even simple move may
        // affect almost every record in the database when only a couple of changes are significant for user.
        $usesTimestamps = $this->timestamps;

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {
            // turn off auto timestamp update and update required timestamps
            if ($usesTimestamps === true) {
                $this->timestamps = false;

                // update 'updated_at' timestamp
                /** @noinspection PhpUndefinedMethodInspection */
                $this->newQuery()->whereIn(self::FIELD_ID, $touchIds)->update([Model::UPDATED_AT => Carbon::now()]);
            }

            // add some space for subtree, orderBy clause covers this MySQL bug http://bugs.mysql.com/bug.php?id=18913
            /** @noinspection PhpUndefinedMethodInspection */
            $this->newQuery()->where(self::FIELD_LFT, '>=', $newLft)->orderBy(self::FIELD_LFT, 'desc')
                ->increment(self::FIELD_LFT, $shift);
            /** @noinspection PhpUndefinedMethodInspection */
            $this->newQuery()->where(self::FIELD_RGT, '>=', $newLft)->orderBy(self::FIELD_RGT, 'desc')
                ->increment(self::FIELD_RGT, $shift);

            // Move the subtree. We basically need to update all left and right numbers in [$tmpPos, $tmpPos + $shift)
            // range however as we move left first we have to change the second update to check only 'right' parts as
            // 'left' numbers are already out of this range.
            $this->newQuery()->where(self::FIELD_LFT, '>=', $tmpPos)->where(self::FIELD_RGT, '<', $tmpPos + $shift)
                ->increment(self::FIELD_LFT, $distance);
            $this->newQuery()->where(self::FIELD_RGT, '>=', $tmpPos)->where(self::FIELD_RGT, '<', $tmpPos + $shift)
                ->increment(self::FIELD_RGT, $distance);

            // remove a gap after the move
            /** @noinspection PhpUndefinedMethodInspection */
            $this->newQuery()->where(self::FIELD_LFT, '>', $right)->orderBy(self::FIELD_LFT, 'asc')
                ->decrement(self::FIELD_LFT, $shift);
            /** @noinspection PhpUndefinedMethodInspection */
            $this->newQuery()->where(self::FIELD_RGT, '>', $right)->orderBy(self::FIELD_RGT, 'asc')
                ->decrement(self::FIELD_RGT, $shift);

            // update ancestor if requested
            if ($updateAncestorIdTo !== null) {
                $this->newQuery()->where(self::FIELD_ID, '=', $this->{self::FIELD_ID})
                    ->update([self::FIELD_ID_ANCESTOR => $updateAncestorIdTo]);
            }

            $allExecutedOk = true;

        } finally {
            // change timestamps mode back
            $this->timestamps = $usesTimestamps;

            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) === true ? DB::commit() : DB::rollBack();
        }
    }

    /**
     * @param string $code
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function selectByCode($code)
    {
        return $this->newQuery()->where(self::FIELD_CODE, '=', $code);
    }

    /**
     * @param string|null $code
     *
     * @return Collection
     */
    public function findDescendants($code = null)
    {
        /** @var Category $parent */
        $parent = $this->selectByCode($code !== null ? $code : self::ROOT_CODE)->firstOrFail([self::FIELD_ID]);
        $descendants = $parent->getDescendants();

        return $descendants;
    }

    /**
     * @return Collection
     */
    public function getDescendants()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $descendants = $this->newQuery()
            ->where(self::FIELD_ID_ANCESTOR, '=', $this->{self::FIELD_ID})
            ->where(self::FIELD_CODE, '<>', self::ROOT_CODE)
            ->orderBy(self::FIELD_LFT, 'asc')
            ->with(self::FIELD_PROPERTIES.'.'.CategoryProperty::FIELD_LANGUAGE)
            ->get();
        return $descendants;
    }
}
