<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Models\Product;
use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Models\Language;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\VariantProperties;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Api\Traits\LanguagePropertiesTrait;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class VariantCrud
{
    use SpecificationTrait;
    use LanguagePropertiesTrait;

    /**
     * @var Product
     */
    private $productModel;

    /**
     * @var Variant
     */
    private $variantModel;

    /**
     * @var VariantProperties
     */
    private $propertiesModel;

    /**
     * @var Language
     */
    private $languageModel;

    /**
     * @param Product           $product
     * @param Variant           $variant
     * @param VariantProperties $properties
     * @param Language          $language
     */
    public function __construct(
        Product $product,
        Variant $variant,
        VariantProperties $properties,
        Language $language
    ) {
        $this->productModel    = $product;
        $this->variantModel    = $variant;
        $this->propertiesModel = $properties;
        $this->languageModel   = $language;
    }

    /**
     * Add product variants.
     *
     * @param Product $product
     * @param array $input
     *
     * @return void
     */
    public function create(Product $product, array $input)
    {
        Permissions::check($product, Permission::edit());

        list($input, $propertiesInput) = $this->extractPropertiesInput($this->languageModel, $input);

        // check language properties are not empty
        count($propertiesInput) ?: S\throwEx(new InvalidArgumentException(Products::PARAM_PROPERTIES));

        $input = array_merge($input, [Product::FIELD_ID => $product->{Product::FIELD_ID}]);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var Variant $variant */
            $variant = $this->variantModel->createOrFailResource($input);
            $variantId = $variant->{Variant::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $this->propertiesModel->createOrFail(array_merge(
                    [Variant::FIELD_ID => $variantId, Language::FIELD_ID => $languageId],
                    $propertyInput
                ));
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new ProductArgs(Products::EVENT_PREFIX . 'addedVariant', $variant));
    }

    /**
     * Update product variant.
     *
     * @param Variant $variant
     * @param array        $input
     *
     * @return void
     */
    public function update(Variant $variant, array $input)
    {
        Permissions::check($variant->product, Permission::edit());

        // get input for properties
        list($input, $propertiesInput) =
            $this->extractPropertiesInput($this->languageModel, $input);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            // update resource
            empty($input) ?: $variant->updateOrFail($input);

            // update language properties
            $variantId = $variant->{Variant::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $property = $this->propertiesModel->updateOrCreate(
                    [Variant::FIELD_ID => $variantId, Language::FIELD_ID => $languageId],
                    $propertyInput
                );
                /** @noinspection PhpUndefinedMethodInspection */
                $property->exists ?: S\throwEx(new ValidationException($property->getValidator()));
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new ProductArgs(Products::EVENT_PREFIX . 'updatedVariant', $variant));
    }

    /**
     * Remove product variants.
     *
     * @param string $variantSKU
     *
     * @return void
     */
    public function delete($variantSKU)
    {
        /** @var Variant $variant */
        $variant = $this->variantModel->selectByCode($variantSKU)->firstOrFail();
        Permissions::check($variant->product, Permission::edit());
        $variant->deleteOrFail();

        Event::fire(new ProductArgs(Products::EVENT_PREFIX . 'deletedVariant', $variant));
    }
}
