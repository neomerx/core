<?php namespace Neomerx\Core\Api\Languages;

use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Models\Language;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Auth\Facades\Permissions;

class Languages implements LanguagesInterface
{
    const EVENT_PREFIX = 'Api.Language.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var Language
     */
    private $language;

    /**
     * Constructor.
     *
     * @param Language $language
     */
    public function __construct(Language $language)
    {
        $this->language = $language;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $input)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var \Neomerx\Core\Models\Language $language */
            $language = $this->language->createOrFailResource($input);
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
        /** @var \Neomerx\Core\Models\Language $language */
        $language = $this->language->selectByCode($isoCode)->firstOrFail();
        Permissions::check($language, Permission::view());
        return $language;
    }

    /**
     * {@inheritdoc}
     */
    public function update($isoCode, array $input)
    {
        /** @var \Neomerx\Core\Models\Language $language */
        $language = $this->language->selectByCode($isoCode)->firstOrFail();
        Permissions::check($language, Permission::edit());
        empty($input) ?: $language->updateOrFail($input);

        Event::fire(new LanguageArgs(self::EVENT_PREFIX . 'updated', $language));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($isoCode)
    {
        /** @var \Neomerx\Core\Models\Language $language */
        $language = $this->language->selectByCode($isoCode)->firstOrFail();
        Permissions::check($language, Permission::delete());
        $language->deleteOrFail();

        Event::fire(new LanguageArgs(self::EVENT_PREFIX . 'deleted', $language));
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $languages = $this->language->all();

        foreach ($languages as $language) {
            /** @var \Neomerx\Core\Models\Language $language */
            Permissions::check($language, Permission::view());
        }

        return $languages;
    }
}
