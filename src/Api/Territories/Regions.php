<?php namespace Neomerx\Core\Api\Territories;

use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Support\SearchParser;
use \Neomerx\Core\Support\SearchGrammar;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\Region as Model;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\Country as CountryModel;

class Regions implements RegionsInterface
{
    const EVENT_PREFIX = 'Api.Region.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var Model
     */
    private $model;

    /**
     * @var CountryModel
     */
    private $countryModel;

    /**
     * @var array
     */
    private static $regionRelations = [
        Model::FIELD_COUNTRY,
    ];

    /**
     * Searchable fields of the resource.
     * Could be used as parameters in search function.
     *
     * @var array
     */
    private static $searchRules = [
        Model::FIELD_CODE         => SearchGrammar::TYPE_STRING,
        Model::FIELD_NAME         => SearchGrammar::TYPE_STRING,
        SearchGrammar::LIMIT_SKIP => SearchGrammar::TYPE_LIMIT,
        SearchGrammar::LIMIT_TAKE => SearchGrammar::TYPE_LIMIT,
    ];

    /**
     * @param Model        $model
     * @param CountryModel $country
     */
    public function __construct(Model $model, CountryModel $country)
    {
        $this->model        = $model;
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

            /** @var Model $region */
            $region = $this->model->createOrFailResource($input);
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
        /** @var Model $region */
        /** @noinspection PhpParamsInspection */
        $region = $this->model->selectByCode($code)->with(static::$regionRelations)->firstOrFail();
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

                /** @var Model $region */
                $region = $this->model->selectByCode($code)->firstOrFail();
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
        /** @var Model $region */
        $region = $this->model->selectByCode($code)->firstOrFail();
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
        $builder = $this->model->newQuery()->with(static::$regionRelations);

        // add search parameters if required
        if (!empty($parameters)) {
            $parser  = new SearchParser(new SearchGrammar($builder), self::$searchRules);
            $builder = $parser->buildQuery($parameters);
        }

        $regions = $builder->get();

        foreach ($regions as $region) {
            /** @var Model $region */
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
        if (isset($input[self::PARAM_COUNTRY_CODE])) {
            $countryId = $this->countryModel->selectByCode($input[self::PARAM_COUNTRY_CODE])
                ->firstOrFail([CountryModel::FIELD_ID])->{CountryModel::FIELD_ID};
            $input = array_add($input, CountryModel::FIELD_ID, $countryId);
            unset($input[self::PARAM_COUNTRY_CODE]);
        }
        return $input;
    }
}
