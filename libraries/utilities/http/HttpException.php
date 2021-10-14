<?php

namespace App\Utilities\Http;

class HttpException extends \RuntimeException
{
    /**
     * The request that triggered the exception.
     *
     * @var HttpRequest
     */
    protected $request;

    /**
     * Constructor.
     *
     * @param HttpRequest|null   $request
     * @param string         $message
     * @param integer        $code
     */
    public function __construct(HttpRequest $request, $message = "", $code = 0)
    {
        parent::__construct($message, $code);
        $this->request = $request;
    }

    /**
     * Get the request that triggered the exception.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
