<?php namespace Neomerx\Core\Api\Taxes;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Cache\TagTrait;
use \Neomerx\Core\Models\Variant;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Cache\FormatItemInterface;
use \Neomerx\Core\Cache\TagProviderInterface;
use \Neomerx\Core\Converters\ConverterInterface;
use \Neomerx\Core\Api\Products\VariantTagProvider;
use \Neomerx\Core\Converters\VariantConverterGeneric;

class VariantFormatter implements FormatItemInterface
{
    use TagTrait;

    const BIND_NAME    = __CLASS__;
    const UID          = 'Variant_Tax';
    const CACHE_PREFIX = 'nm_var_f_tax__';

    /**
     * @var TagProviderInterface
     */
    private $tagProvider;

    /**
     * @var ConverterInterface
     */
    private $converter;

    /**
     * @param ConverterInterface   $converter
     * @param TagProviderInterface $tagProvider
     */
    public function __construct(ConverterInterface $converter = null, TagProviderInterface $tagProvider = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->converter = $converter ? $converter : App::make(VariantConverterGeneric::BIND_NAME);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->tagProvider = $tagProvider ? $tagProvider : App::make(VariantTagProvider::BIND_NAME);
    }

    /**
     * Get unique ID of the formatter.
     *
     * @return string
     */
    public function getUId()
    {
        return self::UID;
    }

    /**
     * Format object to a defined format.
     *
     * @param Variant $variant
     *
     * @return array
     */
    public function format($variant)
    {
        /** @var \Neomerx\Core\Models\Variant $variant */

        return [(object)$this->converter->convert($variant), $this->tagProvider->getTags($variant)];
    }

    /**
     * Get a key to be used to store a formatted object in cache.
     * The key could be used by external systems to work with an underlying cache engine.
     *
     * @param string $objectId
     *
     * @return string
     */
    public function getKey($objectId)
    {
        return self::CACHE_PREFIX . $objectId;
    }
}
