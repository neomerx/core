<?php namespace Neomerx\Core\Api\Images;

use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\ImageFormat as Model;

class ImageFormats implements ImageFormatsInterface
{
    const EVENT_PREFIX = 'Api.ImageFormat.';
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

            /** @var Model $format */
            $format = $this->model->createOrFailResource($input);
            Permissions::check($format, Permission::create());

            $allExecutedOk = true;

        } finally {

            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();

        }

        Event::fire(new ImageFormatArgs(self::EVENT_PREFIX . 'created', $format));

        return $format;
    }

    /**
     * {@inheritdoc}
     */
    public function read($name)
    {
        /** @var Model $format */
        $format = $this->model->selectByName($name)->firstOrFail();
        Permissions::check($format, Permission::view());
        return $format;
    }

    /**
     * {@inheritdoc}
     */
    public function update($name, array $input)
    {
        /** @var Model $format */
        $format = $this->model->selectByName($name)->firstOrFail();
        Permissions::check($format, Permission::edit());
        empty($input) ?: $format->updateOrFail($input);

        Event::fire(new ImageFormatArgs(self::EVENT_PREFIX . 'updated', $format));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($name)
    {
        /** @var Model $format */
        $format = $this->model->selectByName($name)->firstOrFail();
        Permissions::check($format, Permission::delete());
        $format->deleteOrFail();

        Event::fire(new ImageFormatArgs(self::EVENT_PREFIX . 'deleted', $format));
    }

    /**
     * {@inheritdoc}
     */
    public function all(array $parameters = [])
    {
        $formats = $this->model->all();

        foreach ($formats as $format) {
            Permissions::check($format, Permission::view());
        }

        return $formats;
    }
}
