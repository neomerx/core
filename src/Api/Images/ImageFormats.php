<?php namespace Neomerx\Core\Api\Images;

use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\ImageFormat;
use \Neomerx\Core\Auth\Facades\Permissions;

class ImageFormats implements ImageFormatsInterface
{
    const EVENT_PREFIX = 'Api.ImageFormat.';
    const BIND_NAME    = __CLASS__;

    /**
     * @var ImageFormat
     */
    private $imageFormat;

    /**
     * Constructor.
     *
     * @param ImageFormat $imageFormat
     */
    public function __construct(ImageFormat $imageFormat)
    {
        $this->imageFormat = $imageFormat;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $input)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            /** @var \Neomerx\Core\Models\ImageFormat $format */
            $format = $this->imageFormat->createOrFailResource($input);
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
        /** @var \Neomerx\Core\Models\ImageFormat $format */
        $format = $this->imageFormat->selectByName($name)->firstOrFail();
        Permissions::check($format, Permission::view());
        return $format;
    }

    /**
     * {@inheritdoc}
     */
    public function update($name, array $input)
    {
        /** @var \Neomerx\Core\Models\ImageFormat $format */
        $format = $this->imageFormat->selectByName($name)->firstOrFail();
        Permissions::check($format, Permission::edit());
        empty($input) ?: $format->updateOrFail($input);

        Event::fire(new ImageFormatArgs(self::EVENT_PREFIX . 'updated', $format));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($name)
    {
        /** @var \Neomerx\Core\Models\ImageFormat $format */
        $format = $this->imageFormat->selectByName($name)->firstOrFail();
        Permissions::check($format, Permission::delete());
        $format->deleteOrFail();

        Event::fire(new ImageFormatArgs(self::EVENT_PREFIX . 'deleted', $format));
    }

    /**
     * {@inheritdoc}
     */
    public function all(array $parameters = [])
    {
        $formats = $this->imageFormat->all();

        foreach ($formats as $format) {
            /** @var \Neomerx\Core\Models\ImageFormat $format */
            Permissions::check($format, Permission::view());
        }

        return $formats;
    }
}
