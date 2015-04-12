<?php namespace Neomerx\Core\Models;

/**
 * @property int      id_product_property
 * @property int      id_product
 * @property int      id_language
 * @property string   name
 * @property string   description_short
 * @property string   description
 * @property string   meta_title
 * @property string   meta_keywords
 * @property string   meta_description
 * @property Product  product
 * @property Language language
 *
 * @package Neomerx\Core
 */
class ProductProperties extends BaseModel
{
    /** Model table name */
    const TABLE_NAME = 'product_properties';

    /** Model field length */
    const NAME_MAX_LENGTH              = 50;
    /** Model field length */
    const DESCRIPTION_SHORT_MAX_LENGTH = 100;
    /** Model field length */
    const DESCRIPTION_MAX_LENGTH       = 300;
    /** Model field length */
    const META_TITLE_MAX_LENGTH        = 100;
    /** Model field length */
    const META_KEYWORDS_MAX_LENGTH     = 150;
    /** Model field length */
    const META_DESCRIPTION_MAX_LENGTH  = 300;

    /** Model field name */
    const FIELD_ID                = 'id_product_property';
    /** Model field name */
    const FIELD_ID_PRODUCT        = Product::FIELD_ID;
    /** Model field name */
    const FIELD_ID_LANGUAGE       = Language::FIELD_ID;
    /** Model field name */
    const FIELD_NAME              = 'name';
    /** Model field name */
    const FIELD_DESCRIPTION       = 'description';
    /** Model field name */
    const FIELD_DESCRIPTION_SHORT = 'description_short';
    /** Model field name */
    const FIELD_META_TITLE        = 'meta_title';
    /** Model field name */
    const FIELD_META_KEYWORDS     = 'meta_keywords';
    /** Model field name */
    const FIELD_META_DESCRIPTION  = 'meta_description';
    /** Model field name */
    const FIELD_PRODUCT           = 'product';
    /** Model field name */
    const FIELD_LANGUAGE          = 'language';

    /**
     * @inheritdoc
     */
    protected $table = self::TABLE_NAME;

    /**
     * @inheritdoc
     */
    protected $primaryKey = self::FIELD_ID;

    /**
     * @inheritdoc
     */
    public $incrementing = true;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        self::FIELD_NAME,
        self::FIELD_DESCRIPTION_SHORT,
        self::FIELD_DESCRIPTION,
        self::FIELD_META_TITLE,
        self::FIELD_META_KEYWORDS,
        self::FIELD_META_DESCRIPTION,
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        self::FIELD_ID_PRODUCT,
        self::FIELD_ID_LANGUAGE,
    ];

    /**
     * @inheritdoc
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_PRODUCT,
        self::FIELD_ID_LANGUAGE,
    ];

    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_PRODUCT        => 'required|integer|min:1|max:4294967295|exists:'.Product::TABLE_NAME,
            self::FIELD_ID_LANGUAGE       => 'required|integer|min:1|max:4294967295|exists:'.Language::TABLE_NAME,
            self::FIELD_NAME              => 'required|min:1|max:'.self::NAME_MAX_LENGTH,
            self::FIELD_DESCRIPTION_SHORT => 'required|min:1|max:'.self::DESCRIPTION_SHORT_MAX_LENGTH,
            self::FIELD_DESCRIPTION       => 'required|min:1|max:'.self::DESCRIPTION_MAX_LENGTH,
            self::FIELD_META_TITLE        => 'required|min:1|max:'.self::META_TITLE_MAX_LENGTH,
            self::FIELD_META_KEYWORDS     => 'required|min:1|max:'.self::META_KEYWORDS_MAX_LENGTH,
            self::FIELD_META_DESCRIPTION  => 'required|min:1|max:'.self::META_DESCRIPTION_MAX_LENGTH,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_PRODUCT  => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Product::TABLE_NAME,
            self::FIELD_ID_LANGUAGE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Language::TABLE_NAME,

            self::FIELD_NAME              => 'sometimes|required|min:1|max:'.self::NAME_MAX_LENGTH,
            self::FIELD_DESCRIPTION_SHORT => 'sometimes|required|min:1|max:'.self::DESCRIPTION_SHORT_MAX_LENGTH,
            self::FIELD_DESCRIPTION       => 'sometimes|required|min:1|max:'.self::DESCRIPTION_MAX_LENGTH,
            self::FIELD_META_TITLE        => 'sometimes|required|min:1|max:'.self::META_TITLE_MAX_LENGTH,
            self::FIELD_META_KEYWORDS     => 'sometimes|required|min:1|max:'.self::META_KEYWORDS_MAX_LENGTH,
            self::FIELD_META_DESCRIPTION  => 'sometimes|required|min:1|max:'.self::META_DESCRIPTION_MAX_LENGTH,
        ];
    }

    /**
     * Relation to product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class, self::FIELD_ID_PRODUCT, Product::FIELD_ID);
    }

    /**
     * Relation to language.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo(Language::class, self::FIELD_ID_LANGUAGE, Language::FIELD_ID);
    }
}
