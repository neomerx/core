<?php namespace Neomerx\Core\Exceptions;

use \Neomerx\Core\Support\Translate as T;

/**
 * @package Neomerx\Core
 */
class AccessDeniedFileException extends FileException implements ExceptionInterface
{
    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string $fileName
     * @inheritdoc
     */
    public function __construct($fileName, $message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($this->loadIfEmpty($message, T::KEY_EX_ACCESS_DENIED_FILE_EXCEPTION), $code, $previous);
        $this->fileName = $fileName;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }
}
