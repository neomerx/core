<?php namespace Neomerx\Core\Support;

use \Neomerx\Core\Exceptions\InvalidArgumentException;

class SearchParser
{
    /**
     * This operation will be used by default.
     */
    const DEFAULT_OPERATION = 'eq';

    /**
     * This signs all search operation are allowed.
     */
    const ALLOWED_OPERATIONS_ALL = '*';

    private $operationsMap = [
        'eq'      => 'Equals',
        'equals'  => 'Equals',
        'ne'      => 'NotEquals',
        'not'     => 'NotEquals',
        'gt'      => 'Greater',
        'greater' => 'Greater',
        'lt'      => 'Less',
        'less'    => 'Less',
        'ge'      => 'From',
        'from'    => 'From',
        'le'      => 'To',
        'to'      => 'To',
        'like'    => 'Like',
        'unlike'  => 'NotLike',
    ];

    /**
     * @var SearchGrammar
     */
    private $grammar;

    /**
     * @var array
     */
    private $rules;

    /**
     * Rules format [
     *      parameterName => type
     *          -- OR --
     *      parameterName => [type]
     *          -- OR --
     *      parameterName => [type, databaseColumn]
     *          -- OR --
     *      parameterName => [type, databaseColumn, allowedOperations]
     * ]
     * where
     *    - parameterName     (string, required) - name of the parameter in search
     *    - type              (string, required) - type of parameter ('bool', 'date', 'float', 'int', 'string')
     *    - databaseColumn    (string, optional, default $parameter) - corresponding column name
     *    - allowedOperations (string|array, optional, default '*')  - allowed search operations ('equals', 'less', etc)
     *
     * @param SearchGrammar $grammar
     * @param array         $rules
     */
    public function __construct(SearchGrammar $grammar, array $rules)
    {
        $this->grammar = $grammar;
        $this->parseRules($rules);
    }

    /**
     * Build search query with $searchParameters
     *
     * @param array $searchParameters
     *
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @throws \Neomerx\Core\Exceptions\InvalidArgumentException
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function buildQuery(array $searchParameters)
    {
        foreach ($searchParameters as $name => $value) {
            list($parameter, $operation) = $this->parseParameterAndOperation($name);
            array_key_exists($parameter, $this->rules) === true ?: throwEx(new InvalidArgumentException($name));

            list($type, $column, $allowedOperations) = $this->rules[$parameter];

            array_key_exists($operation, $this->operationsMap) === true ?: throwEx(new InvalidArgumentException($name));
            $operation = $this->operationsMap[$operation];

            // check operation is allowed
            if ($allowedOperations !== self::ALLOWED_OPERATIONS_ALL &&
                in_array($operation, $allowedOperations) === false) {
                throw new InvalidArgumentException($name);
            }

            // we have passed all checks and can add search condition
            $conditionFunction = 'add'.$type.$operation;

            // check this combination of type and operation exists
            method_exists($this->grammar, $conditionFunction) === true ?: throwEx(new InvalidArgumentException($name));
            $this->grammar->{$conditionFunction}($column, $value);
        }

        return $this->grammar->getBuilder();
    }

    /**
     * @param array $rules
     */
    private function parseRules(array $rules)
    {
        $parsedRules = [];
        foreach ($rules as $paramName => $rule) {
            // check parameter name is specified (not int)
            is_string($paramName) === true ?: throwEx(new InvalidArgumentException($paramName));

            // parse possible rule formats
            if (is_array($rule) === true) {
                // we are here if rule description is given in array format
                $numberOfRuleParams = count($rule);

                switch ($numberOfRuleParams)
                {
                    case 1:
                        // only one parameter - 'type'
                        $parsedRules[$paramName] =
                            $this->newRule($rule[0], $paramName, self::ALLOWED_OPERATIONS_ALL);
                        break;
                    case 2:
                        // two parameters - 'type' and 'databaseColumn'
                        $parsedRules[$paramName] = $this->newRule($rule[0], $rule[1], self::ALLOWED_OPERATIONS_ALL);
                        break;
                    case 3:
                        // all three - 'type', 'databaseColumn' and 'allowedOperations'
                        $parsedRules[$paramName] = $this->newRule($rule[0], $rule[1], $rule[2]);
                        break;
                    default:
                        // rule has zero or more than 3 description parameters. not supported.
                        throwEx(new InvalidArgumentException($paramName));
                }

            } elseif (is_string($rule) === true) {
                $parsedRules[$paramName] = $this->newRule($rule, $paramName, self::ALLOWED_OPERATIONS_ALL);
            } else {
                // if not string and array
                throwEx(new InvalidArgumentException($paramName));
            }
        }
        $this->rules = $parsedRules;
    }

    /**
     * @param string       $type
     * @param string       $columnName
     * @param string|array $allowedOperations
     *
     * @return array<string|array>
     */
    private function newRule($type, $columnName, $allowedOperations)
    {
        // sanity check for input params
        $this->grammar->isTypeSupported($type) === true ?: throwEx(new InvalidArgumentException('type'));
        is_string($columnName) === true                 ?: throwEx(new InvalidArgumentException('columnName'));

        // parse $allowedOperations which could be '*', something like 'equals' or an array
        if ($allowedOperations === self::ALLOWED_OPERATIONS_ALL) {
            $allowedOperations = self::ALLOWED_OPERATIONS_ALL;
        } elseif (is_array($allowedOperations) === true) {
            $allowedOperations = array_map(
                function ($operation) {
                    return $this->mapOperation($operation);
                },
                $allowedOperations
            );
        } elseif (is_string($allowedOperations) === true) {
            $allowedOperations = [$this->mapOperation($allowedOperations)];
        } else {
            throwEx(new InvalidArgumentException('allowedOperations'));
        }

        return [ucwords($type), $columnName, $allowedOperations];
    }

    /**
     * @param string $name
     *
     * @throws \Neomerx\Core\Exceptions\InvalidArgumentException
     *
     * @return string[]
     */
    private function parseParameterAndOperation($name)
    {
        // if only name provided then we should return it with default operation
        if (array_key_exists($name, $this->rules) === true) {
            return [$name, self::DEFAULT_OPERATION];
        }

        // if $name not found in rules then we try to parse it
        $separatorPos = strrpos($name, '_', -1);
        if ($separatorPos === false) {
            // if name not found in rules it must have format "$parameter_$operation"
            // most likely parameter was not described in search rules.
            throw new InvalidArgumentException($name);
        }

        $parameter = substr($name, 0, $separatorPos);
        $operation = mb_strtolower(substr($name, $separatorPos + 1));

        return [$parameter, $operation];
    }

    /**
     * Maps short notation (which can have multiple variants) to long one (which is unique).
     *
     * @param string $operation
     *
     * @return string
     */
    private function mapOperation($operation)
    {
        $lcOperation     = mb_strtolower($operation);
        $operationExists = array_key_exists($lcOperation, $this->operationsMap);
        $operationExists === true ?: throwEx(new InvalidArgumentException($operation));
        return $this->operationsMap[$lcOperation];
    }
}
