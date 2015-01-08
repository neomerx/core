<?php namespace Neomerx\Core\Api\Territories;

use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Models\Region;
use \Neomerx\Core\Models\Country;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Support\SearchParser;
use \Neomerx\Core\Support\SearchGrammar;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Api\Traits\InputParserTrait;

class Regions implements RegionsInterface
{
    use InputParserTrait;

    const EVENT_PREFIX = 'Api.Region.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var Region
     */
    private $regionModel;

    /**
     * @var Country
     */
    private $countryModel;

    /**
     * @var array
     */
    protected static $regionRelations = [
        Region::FIELD_COUNTRY,
    ];

    /**
     * Searchable fields of the resource.
     * Could be used as parameters in search function.
     *
     * @var array
     */
    protected static $searchRules = [
        Region::FIELD_CODE        => SearchGrammar::TYPE_STRING,
        Region::FIELD_NAME        => SearchGrammar::TYPE_STRING,
        SearchGrammar::LIMIT_SKIP => SearchGrammar::TYPE_LIMIT,
        SearchGrammar::LIMIT_TAKE => SearchGrammar::TYPE_LIMIT,
    ];

    /**
     * @param Region  $region
     * @param Country $country
     */
    public function __construct(Region $region, Country $country)
    {
        $this->regionModel  = $region;
        $this->countryModel = $country;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $input)
    {
        $input = $this->replaceCountryCodeToId($input);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var \Neomerx\Core\Models\Region $region */
            $region = $this->regionModel->createOrFailResource($input);
            Permissions::check($region, Permission::create());

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new RegionArgs(self::EVENT_PREFIX . 'created', $region));

        return $region;
    }

    /**
     * {@inheritdoc}
     */
    public function read($code)
    {
        /** @noinspection PhpParamsInspection */
        /** @var \Neomerx\Core\Models\Region $region */
        $region = $this->regionModel->selectByCode($code)->with(static::$regionRelations)->firstOrFail();
        Permissions::check($region, Permission::view());

        return $region;
    }

    /**
     * {@inheritdoc}
     */
    public function update($code, array $input)
    {
        $input = $this->replaceCountryCodeToId($input);

        if (!empty($input)) {

            /** @noinspection PhpUndefinedMethodInspection */
            DB::beginTransaction();
            try {

                /** @var \Neomerx\Core\Models\Region $region */
                $region = $this->regionModel->selectByCode($code)->firstOrFail();
                Permissions::check($region, Permission::edit());
                $region->updateOrFail($input);

                $allExecutedOk = true;

            } finally {
                /** @noinspection PhpUndefinedMethodInspection */
                isset($allExecutedOk) ? DB::commit() : DB::rollBack();
            }

            Event::fire(new RegionArgs(self::EVENT_PREFIX . 'updated', $region));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete($code)
    {
        /** @var \Neomerx\Core\Models\Region $region */
        $region = $this->regionModel->selectByCode($code)->firstOrFail();
        Permissions::check($region, Permission::delete());
        $region->deleteOrFail();

        Event::fire(new RegionArgs(self::EVENT_PREFIX . 'deleted', $region));
    }

    /**
     * {@inheritdoc}
     */
    public function search(array $parameters = [])
    {
        /** @noinspection PhpParamsInspection */
        $builder = $this->regionModel->newQuery()->with(static::$regionRelations);

        // add search parameters if required
        if (!empty($parameters)) {
            $parser  = new SearchParser(new SearchGrammar($builder), static::$searchRules);
            $builder = $parser->buildQuery($parameters);
        }

        $regions = $builder->get();

        foreach ($regions as $region) {
            /** @var \Neomerx\Core\Models\Region $region */
            Permissions::check($region, Permission::view());
        }

        return $regions;
    }

    /**
     * @param array $input
     *
     * @return array
     */
    private function replaceCountryCodeToId(array $input)
    {
        $this->replaceInputCodeWithId(
            $input,
            self::PARAM_COUNTRY_CODE,
            $this->countryModel,
            Country::FIELD_ID,
            Region::FIELD_ID_COUNTRY
        );
        return $input;
    }
}
