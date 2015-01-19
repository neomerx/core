<?php namespace Neomerx\Core\Models;

/**
 * @property int      id_category_property
 * @property int      id_category
 * @property int      id_language
 * @property string   name
 * @property string   description
 * @property string   meta_title
 * @property string   meta_keywords
 * @property string   meta_description
 * @property Category category
 * @property Language language
 */
class CategoryProperties extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'category_properties';

    const NAME_MAX_LENGTH             = 50;
    const DESCRIPTION_MAX_LENGTH      = 300;
    const META_TITLE_MAX_LENGTH       = 100;
    const META_KEYWORDS_MAX_LENGTH    = 150;
    const META_DESCRIPTION_MAX_LENGTH = 300;

    const FIELD_ID               = 'id_category_property';
    const FIELD_ID_CATEGORY      = Category::FIELD_ID;
    const FIELD_ID_LANGUAGE      = Language::FIELD_ID;
    const FIELD_NAME             = 'name';
    const FIELD_DESCRIPTION      = 'description';
    const FIELD_META_TITLE       = 'meta_title';
    const FIELD_META_KEYWORDS    = 'meta_keywords';
    const FIELD_META_DESCRIPTION = 'meta_description';
    const FIELD_CATEGORY         = 'category';
    const FIELD_LANGUAGE         = 'language';

    /**
     * {@inheritdoc}
     */
    protected $table = self::TABLE_NAME;

    /**
     * {@inheritdoc}
     */
    protected $primaryKey = self::FIELD_ID;

    /**
     * {@inheritdoc}
     */
    public $incrementing = true;

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        self::FIELD_NAME,
        self::FIELD_DESCRIPTION,
        self::FIELD_META_TITLE,
        self::FIELD_META_KEYWORDS,
        self::FIELD_META_DESCRIPTION,
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        self::FIELD_ID_CATEGORY,
        self::FIELD_ID_LANGUAGE,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_CATEGORY,
        self::FIELD_ID_LANGUAGE,
    ];

    /**
     * {@inheritdoc}
     */
    protected $touches = [
        self::FIELD_CATEGORY,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_CATEGORY      => 'required|integer|min:1|max:4294967295|exists:'.Category::TABLE_NAME,
            self::FIELD_ID_LANGUAGE      => 'required|integer|min:1|max:4294967295|exists:'.Language::TABLE_NAME,
            self::FIELD_NAME             => 'required|alpha_dash_dot_space|min:1|max:'.self::NAME_MAX_LENGTH,

            self::FIELD_DESCRIPTION => 'sometimes|required|alpha_dash_dot_space|min:1|max:'.
                self::DESCRIPTION_MAX_LENGTH,

            self::FIELD_META_TITLE       => 'required|alpha_dash_dot_space|min:1|max:'.self::META_TITLE_MAX_LENGTH,
            self::FIELD_META_KEYWORDS    => 'required|alpha_dash_dot_space|min:1|max:'.self::META_KEYWORDS_MAX_LENGTH,

            self::FIELD_META_DESCRIPTION => 'required|alpha_dash_dot_space|min:1|max:'.
                self::META_DESCRIPTION_MAX_LENGTH,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_CATEGORY => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Category::TABLE_NAME,
            self::FIELD_ID_LANGUAGE => 'sometimes|required|integer|min:1|max:4294967295|exists:'.Language::TABLE_NAME,
            self::FIELD_NAME        => 'sometimes|required|alpha_dash_dot_space|min:1|max:'.self::NAME_MAX_LENGTH,
            self::FIELD_DESCRIPTION => 'sometimes|alpha_dash_dot_space|min:1|max:'.self::DESCRIPTION_MAX_LENGTH,

            self::FIELD_META_TITLE => 'sometimes|required|alpha_dash_dot_space|min:1|max:'.
                self::META_TITLE_MAX_LENGTH,

            self::FIELD_META_KEYWORDS => 'sometimes|required|alpha_dash_dot_space|min:1|max:'.
                self::META_KEYWORDS_MAX_LENGTH,

            self::FIELD_META_DESCRIPTION => 'sometimes|required|alpha_dash_dot_space|min:1|max:'.
                self::META_DESCRIPTION_MAX_LENGTH,
        ];
    }

    /**
     * Relation to category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::BIND_NAME, self::FIELD_ID_CATEGORY, Category::FIELD_ID);
    }

    /**
     * Relation to language.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo(Language::BIND_NAME, self::FIELD_ID_LANGUAGE, Language::FIELD_ID);
    }
}
