<?php namespace Neomerx\Core\Models;

/**
 * @property int      id_image_property
 * @property int      id_image
 * @property int      id_language
 * @property string   alt
 * @property Image    image
 * @property Language language
 */
class ImageProperties extends BaseModel
{
    const BIND_NAME  = __CLASS__;
    const TABLE_NAME = 'image_properties';

    const ALT_MAX_LENGTH = 100;

    const FIELD_ID          = 'id_image_property';
    const FIELD_ID_IMAGE    = Image::FIELD_ID;
    const FIELD_ID_LANGUAGE = Language::FIELD_ID;
    const FIELD_ALT         = 'alt';
    const FIELD_IMAGE       = 'image';
    const FIELD_LANGUAGE    = 'language';

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
    protected $hidden = [
        self::FIELD_ID_LANGUAGE,
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        self::FIELD_ID,
        self::FIELD_ID_IMAGE,
        self::FIELD_ID_LANGUAGE,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataOnCreateRules()
    {
        return [
            self::FIELD_ID_IMAGE    => 'required|integer|min:1|max:4294967295|exists:' . Image::TABLE_NAME,
            self::FIELD_ID_LANGUAGE => 'required|integer|min:1|max:4294967295|exists:' . Language::TABLE_NAME,
            self::FIELD_ALT         => 'required|min:1|max:' . self::ALT_MAX_LENGTH,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataOnUpdateRules()
    {
        return [
            self::FIELD_ID_IMAGE    => 'sometimes|required|integer|min:1|max:4294967295|exists:' . Image::TABLE_NAME,
            self::FIELD_ID_LANGUAGE => 'sometimes|required|integer|min:1|max:4294967295|exists:' . Language::TABLE_NAME,
            self::FIELD_ALT         => 'sometimes|required|min:1|max:' . self::ALT_MAX_LENGTH,
        ];
    }

    /**
     * Relation to image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function image()
    {
        return $this->belongsTo(Image::BIND_NAME, self::FIELD_ID_IMAGE, Image::FIELD_ID);
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

    /**
     * Fill model.
     *
     * @param Image    $image
     * @param Language $language
     * @param array    $attributes
     *
     * @return $this
     */
    public function fillModel(Image $image = null, Language $language = null, array $attributes = null)
    {
        $data = [
            self::FIELD_ID_IMAGE    => $image,
            self::FIELD_ID_LANGUAGE => $language,
        ];

        empty($attributes) ?: $this->fill($attributes);
        foreach ($data as $attribute => $model) {
            /** @var BaseModel $model */
            $model === null ?: $this->setAttribute($attribute, $model->getKey());
        }

        return $this;
    }
}
