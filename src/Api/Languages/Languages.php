<?php namespace Neomerx\Core\Api\Languages;

use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\Language as Model;

class Languages implements LanguagesInterface
{
    const EVENT_PREFIX = 'Api.Language.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var Model
     */
    private $model;

    /**
     * Constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $input)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var Model $language */
            $language = $this->model->createOrFailResource($input);
            Permissions::check($language, Permission::create());

            $allExecutedOk = true;

        } finally {

            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();

        }

        Event::fire(new LanguageArgs(self::EVENT_PREFIX . 'created', $language));

        return $language;
    }

    /**
     * {@inheritdoc}
     */
    public function read($isoCode)
    {
        /** @var Model $language */
        $language = $this->model->selectByCode($isoCode)->firstOrFail();
        Permissions::check($language, Permission::view());
        return $language;
    }

    /**
     * {@inheritdoc}
     */
    public function update($isoCode, array $input)
    {
        /** @var Model $language */
        $language = $this->model->selectByCode($isoCode)->firstOrFail();
        Permissions::check($language, Permission::edit());
        empty($input) ?: $language->updateOrFail($input);

        Event::fire(new LanguageArgs(self::EVENT_PREFIX . 'updated', $language));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($isoCode)
    {
        /** @var Model $language */
        $language = $this->model->selectByCode($isoCode)->firstOrFail();
        Permissions::check($language, Permission::delete());
        $language->deleteOrFail();

        Event::fire(new LanguageArgs(self::EVENT_PREFIX . 'deleted', $language));
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $languages = $this->model->all();

        foreach ($languages as $language) {
            Permissions::check($language, Permission::view());
        }

        return $languages;
    }
}
