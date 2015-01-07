<?php namespace Neomerx\Core\Queues;

use \Neomerx\Core\Support as S;
use \Illuminate\Queue\Jobs\Job;
use \Neomerx\Core\Models\ImagePath;
use \Neomerx\Core\Models\ImageFormat;
use \Illuminate\Support\Facades\Log;

class ImageGenerator
{
    /**
     * @param Job   $job
     * @param array $data
     */
    public function generateByFormat(Job $job, array $data)
    {
        $formatName = S\array_get_value($data, 'name');

        if ($formatName !== null) {
            /** @var ImageFormat $imageFormatModel */
            $imageFormatModel = new ImageFormat();
            /** @var ImagePath $path */
            $imageFormatModel = $imageFormatModel->selectByName($formatName)->firstOrFail();

            foreach ($imageFormatModel->paths as $path) {
                $imageUpdated = $path->generateImage();
                /** @noinspection PhpUndefinedMethodInspection */
                $imageUpdated ?: Log::error(trans('nm::errors.image_generation_failed'));
            }
        }

        $job->delete();
    }
}
