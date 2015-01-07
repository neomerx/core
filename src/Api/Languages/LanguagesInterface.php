<?php namespace Neomerx\Core\Api\Languages;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Api\CrudInterface;
use \Illuminate\Database\Eloquent\Collection;

interface LanguagesInterface extends CrudInterface
{
    const PARAM_NAME     = Language::FIELD_NAME;
    const PARAM_ISO_CODE = Language::FIELD_ISO_CODE;

    /**
     * Create language.
     *
     * @param array $input
     *
     * @return Language
     */
    public function create(array $input);

    /**
     * Read language by identifier.
     *
     * @param string $isoCode
     *
     * @return Language
     */
    public function read($isoCode);

    /**
     * Get all languages in the system.
     *
     * @return Collection
     */
    public function all();
}
