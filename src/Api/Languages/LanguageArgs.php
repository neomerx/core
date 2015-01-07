<?php namespace Neomerx\Core\Api\Languages;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Events\EventArgs;

class LanguageArgs extends EventArgs
{
    /**
     * @var Language
     */
    private $language;

    /**
     * @param string    $name
     * @param Language  $language
     * @param EventArgs $args
     */
    public function __construct($name, Language $language, EventArgs $args = null)
    {
        parent::__construct($name, $args);
        $this->language = $language;
    }

    /**
     * @return Language
     */
    public function getModel()
    {
        return $this->language;
    }
}
