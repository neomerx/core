<?php namespace Neomerx\Core\Api\Products;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\Product as Model;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\Variant as VariantModel;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Models\Language as LanguageModel;
use \Neomerx\Core\Api\Traits\LanguagePropertiesTrait;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Models\VariantProperties as VariantPropertiesModel;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class VariantCrud
{
    use SpecificationTrait;
    use LanguagePropertiesTrait;

    /**
     * @var Model
     */
    private $productModel;

    /**
     * @var VariantModel
     */
    private $variantModel;

    /**
     * @var VariantPropertiesModel
     */
    private $propertiesModel;

    /**
     * @var LanguageModel
     */
    private $languageModel;

    /**
     * @param Model                  $product
     * @param VariantModel           $variant
     * @param VariantPropertiesModel $properties
     * @param LanguageModel          $language
     */
    public function __construct(
        Model $product,
        VariantModel $variant,
        VariantPropertiesModel $properties,
        LanguageModel $language
    ) {
        $this->productModel    = $product;
        $this->variantModel    = $variant;
        $this->propertiesModel = $properties;
        $this->languageModel   = $language;
    }

    /**
     * Add product variants.
     *
     * @param Model $product
     * @param array $input
     *
     * @return void
     */
    public function create(Model $product, array $input)
    {
        Permissions::check($product, Permission::edit());

        list($input, $propertiesInput) = $this->extractPropertiesInput($this->languageModel, $input);

        // check language properties are not empty
        count($propertiesInput) ?: S\throwEx(new InvalidArgumentException(Products::PARAM_PROPERTIES));

        $input = array_merge($input, [Model::FIELD_ID => $product->{Model::FIELD_ID}]);

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var VariantModel $variant */
            $variant = $this->variantModel->createOrFailResource($input);
            $variantId = $variant->{VariantModel::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $this->propertiesModel->createOrFail(array_merge(
                    [VariantModel::FIELD_ID => $variantId, LanguageModel::FIELD_ID => $languageId],
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
     * @param VariantModel $variant
     * @param array        $input
     *
     * @return void
     */
    public function update(VariantModel $variant, array $input)
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
            $variantId = $variant->{VariantModel::FIELD_ID};
            foreach ($propertiesInput as $languageId => $propertyInput) {
                $property = $this->propertiesModel->updateOrCreate(
                    [VariantModel::FIELD_ID => $variantId, LanguageModel::FIELD_ID => $languageId],
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
        /** @var VariantModel $variant */
        $variant = $this->variantModel->selectByCode($variantSKU)->firstOrFail();
        Permissions::check($variant->product, Permission::edit());
        $variant->deleteOrFail();

        Event::fire(new ProductArgs(Products::EVENT_PREFIX . 'deletedVariant', $variant));
    }
}
