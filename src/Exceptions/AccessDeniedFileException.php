<?php namespace Neomerx\Core\Exceptions;

class AccessDeniedFileException extends FileException
{
    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string $fileName
     * {@inheritDoc}
     */
    public function __construct($fileName, $message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($this->loadIfEmpty($message, 'access_denied_file_exception'), $code, $previous);
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
