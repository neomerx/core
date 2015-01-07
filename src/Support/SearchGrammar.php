<?php namespace Neomerx\Core\Support;

use \DateTimeZone;
use \Carbon\Carbon;
use \Illuminate\Database\Eloquent\Builder;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class SearchGrammar
{
    const TYPE_BOOL   = 'bool';
    const TYPE_DATE   = 'date';
    const TYPE_FLOAT  = 'float';
    const TYPE_INT    = 'int';
    const TYPE_STRING = 'string';
    const TYPE_LIMIT  = 'limit';

    const LIMIT_SKIP = 'skip';
    const LIMIT_TAKE = 'take';

    /**
     * Max number of elements to be selected by default.
     */
    const DEFAULT_SELECT_LIMIT = 250;

    /**
     * @var int
     */
    private $limitFrom;

    /**
     * @var int
     */
    private $limitTake;

    /**
     * @var array
     */
    private $supportedTypes = [
        self::TYPE_BOOL,
        self::TYPE_DATE,
        self::TYPE_FLOAT,
        self::TYPE_INT,
        self::TYPE_STRING,
        self::TYPE_LIMIT,
    ];

    /**
     * @var Builder
     */
    private $builder;

    /**
     * @param Builder $builder
     * @param int     $limitFrom
     * @param int     $limitTake
     */
    public function __construct(Builder $builder, $limitFrom = 0, $limitTake = self::DEFAULT_SELECT_LIMIT)
    {
        $this->builder   = $builder;
        $this->limitFrom = $limitFrom;
        $this->limitTake = $limitTake;
    }

    /**
     * Add 'equals' condition for integer.
     *
     * @param string $column
     * @param int    $value
     *
     * @return $this
     */
    public function addIntEquals($column, $value)
    {
        settype($value, 'int');
        $this->builder->where($column, '=', $value);
        return $this;
    }

    /**
     * Add 'not equals' condition for integer.
     *
     * @param string $column
     * @param int    $value
     *
     * @return $this
     */
    public function addIntNotEquals($column, $value)
    {
        settype($value, 'int');
        $this->builder->where($column, '!=', $value);
        return $this;
    }

    /**
     * Add 'less than' condition for integer.
     *
     * @param string $column
     * @param int    $value
     *
     * @return $this
     */
    public function addIntLess($column, $value)
    {
        settype($value, 'int');
        $this->builder->where($column, '<', $value);
        return $this;
    }

    /**
     * Add 'greater than' condition for integer.
     *
     * @param string $column
     * @param int    $value
     *
     * @return $this
     */
    public function addIntGreater($column, $value)
    {
        settype($value, 'int');
        $this->builder->where($column, '>', $value);
        return $this;
    }

    /**
     * Add 'less or equal to' condition for integer.
     *
     * @param string $column
     * @param int    $value
     *
     * @return $this
     */
    public function addIntTo($column, $value)
    {
        settype($value, 'int');
        $this->builder->where($column, '<=', $value);
        return $this;
    }

    /**
     * Add 'greater or equal to' condition for integer.
     *
     * @param string $column
     * @param int    $value
     *
     * @return $this
     */
    public function addIntFrom($column, $value)
    {
        settype($value, 'int');
        $this->builder->where($column, '>=', $value);
        return $this;
    }

    /**
     * Add 'equals' condition for float.
     *
     * @param string $column
     * @param float  $value
     *
     * @return $this
     */
    public function addFloatEquals($column, $value)
    {
        settype($value, 'float');
        $this->builder->where($column, '=', $value);
        return $this;
    }

    /**
     * Add 'not equals' condition for float.
     *
     * @param string $column
     * @param float  $value
     *
     * @return $this
     */
    public function addFloatNotEquals($column, $value)
    {
        settype($value, 'float');
        $this->builder->where($column, '!=', $value);
        return $this;
    }

    /**
     * Add 'less than' condition for float.
     *
     * @param string $column
     * @param float  $value
     *
     * @return $this
     */
    public function addFloatLess($column, $value)
    {
        settype($value, 'float');
        $this->builder->where($column, '<', $value);
        return $this;
    }

    /**
     * Add 'greater than' condition for float.
     *
     * @param string $column
     * @param float  $value
     *
     * @return $this
     */
    public function addFloatGreater($column, $value)
    {
        settype($value, 'float');
        $this->builder->where($column, '>', $value);
        return $this;
    }

    /**
     * Add 'less or equal to' condition for float.
     *
     * @param string $column
     * @param float  $value
     *
     * @return $this
     */
    public function addFloatTo($column, $value)
    {
        settype($value, 'float');
        $this->builder->where($column, '<=', $value);
        return $this;
    }

    /**
     * Add 'greater or equal to' condition for float.
     *
     * @param string $column
     * @param float  $value
     *
     * @return $this
     */
    public function addFloatFrom($column, $value)
    {
        settype($value, 'float');
        $this->builder->where($column, '>=', $value);
        return $this;
    }

    /**
     * Add 'equals' condition for date.
     *
     * @param string $column
     * @param string $value
     *
     * @return $this
     */
    public function addDateEquals($column, $value)
    {
        $value = $this->prepareDate($value);
        $this->builder->where($column, '=', $value);
        return $this;
    }

    /**
     * Add 'not equals' condition for date.
     *
     * @param string $column
     * @param string $value
     *
     * @return $this
     */
    public function addDateNotEquals($column, $value)
    {
        $value = $this->prepareDate($value);
        $this->builder->where($column, '!=', $value);
        return $this;
    }

    /**
     * Add 'less than' condition for date.
     *
     * @param string $column
     * @param string $value
     *
     * @return $this
     */
    public function addDateLess($column, $value)
    {
        $value = $this->prepareDate($value);
        $this->builder->where($column, '<', $value);
        return $this;
    }

    /**
     * Add 'greater than' condition for date.
     *
     * @param string $column
     * @param string $value
     *
     * @return $this
     */
    public function addDateGreater($column, $value)
    {
        $value = $this->prepareDate($value);
        $this->builder->where($column, '>', $value);
        return $this;
    }

    /**
     * Add 'less or equal to' condition for date.
     *
     * @param string $column
     * @param string $value
     *
     * @return $this
     */
    public function addDateTo($column, $value)
    {
        $value = $this->prepareDate($value);
        $this->builder->where($column, '<=', $value);
        return $this;
    }

    /**
     * Add 'greater or equal to' condition for date.
     *
     * @param string $column
     * @param string $value
     *
     * @return $this
     */
    public function addDateFrom($column, $value)
    {
        $value = $this->prepareDate($value);
        $this->builder->where($column, '>=', $value);
        return $this;
    }

    /**
     * Add 'equals' condition for bool.
     *
     * @param string $column
     * @param string $value
     *
     * @return $this
     */
    public function addBoolEquals($column, $value)
    {
        settype($value, 'bool');
        $this->builder->where($column, '=', $value);
        return $this;
    }

    /**
     * Add 'equals' condition for string.
     *
     * @param string $column
     * @param string $value
     *
     * @return $this
     */
    public function addStringEquals($column, $value)
    {
        settype($value, 'string');
        $this->builder->where($column, '=', $value);
        return $this;
    }

    /**
     * Add 'not equals' condition for string.
     *
     * @param string $column
     * @param string $value
     *
     * @return $this
     */
    public function addStringNotEquals($column, $value)
    {
        settype($value, 'string');
        $this->builder->where($column, '!=', $value);
        return $this;
    }

    /**
     * Add 'like' condition for string.
     *
     * @param string $column
     * @param string $value
     *
     * @return $this
     */
    public function addStringLike($column, $value)
    {
        settype($value, 'string');
        $this->builder->where($column, 'like', $value);
        return $this;
    }

    /**
     * Add 'not like' condition for string.
     *
     * @param string $column
     * @param string $value
     *
     * @return $this
     */
    public function addStringNotLike($column, $value)
    {
        settype($value, 'string');
        $this->builder->where($column, 'not like', $value);
        return $this;
    }

    /**
     * Add paging limits for select.
     *
     * @param string $operator Operators 'skip' and 'take' are supported.
     * @param int    $value
     *
     * @return $this
     */
    public function addLimitEquals($operator, $value)
    {
        settype($value, 'int');
        switch ($operator)
        {
            case self::LIMIT_SKIP:
                $value = $value >= 0 ? $value : 0;
                $this->limitFrom = $value;
                break;
            case self::LIMIT_TAKE:
                $value = (0 < $value and $value <= $this->limitTake) ? $value : self::DEFAULT_SELECT_LIMIT;
                $this->limitTake = $value;
                break;
        }
        return $this;
    }

    /**
     * Get builder.
     *
     * @return Builder
     */
    public function getBuilder()
    {
        $this->builder->getQuery()->skip($this->limitFrom)->take($this->limitTake);
        return $this->builder;
    }

    /**
     * If $type is supported. Supported types are
     *     - 'bool'
     *     - 'date'
     *     - 'float'
     *     - 'int'
     *     - 'string'
     *
     * @param string $type
     *
     * @return bool
     */
    public function isTypeSupported($type)
    {
        return in_array($type, $this->supportedTypes);
    }

    /**
     * Parse $date and return in 'Y-m-d' format.
     *
     * @param string $date
     *
     * @return string
     */
    private function prepareDate($date)
    {
        $dateTime = new Carbon($date, new DateTimeZone('UTC'));
        return $dateTime->toDateString();
    }
}
