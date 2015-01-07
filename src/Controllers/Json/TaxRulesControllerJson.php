<?php namespace Neomerx\Core\Controllers\Json;

use \Neomerx\Core\Models\TaxRule;
use \Neomerx\Core\Api\Facades\TaxRules;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Converters\TaxRuleConverterGeneric;
use \Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class TaxRulesControllerJson extends BaseControllerJson
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        parent::__construct(TaxRules::INTERFACE_BIND_NAME, App::make(TaxRuleConverterGeneric::BIND_NAME));
    }

    /**
     * Get all customer types.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function index()
    {
        return $this->tryAndCatchWrapper('readAll', []);
    }

    /**
     * @return array
     */
    protected function readAll()
    {
        $result = [];
        foreach ($this->getApiFacade()->all() as $resource) {
            $result[] = $this->getConverter()->convert($resource);
        }

        return [$result, null];
    }

    /**
     * @param array $input
     *
     * @return array
     */
    protected function createResource(array $input)
    {
        $taxRule = $this->getApiFacade()->create($input);
        return [['id' => $taxRule->{TaxRule::FIELD_ID}], SymfonyResponse::HTTP_CREATED];
    }
}
