<?php

namespace App\Utilities\Http;

class Http
{
    /**
     * The cURL resource.
     */
    protected $ch;

    /**
     * The request class to use.
     *
     * @var string
     */
    protected string $requestClass = HttpRequest::class;

    /**
     * The response class to use.
     *
     * @var string
     */
    protected string $responseClass = HttpResponse::class;

    /**
     * The default headers.
     *
     * @var array
     */
    protected array $defaultHeaders = [];

    /**
     * The default curl options.
     *
     * @var array
     */
    protected array $defaultOptions = [];

    /**
     * Get allowed methods.
     *
     * @return array
     */
    public function getAllowedMethods(): array
    {
        return HttpRequest::$methods;
    }

    /**
     * Set the request class.
     *
     * @param string $class
     */
    public function setRequestClass(string $class): void
    {
        $this->requestClass = $class;
    }

    /**
     * Set the response class.
     *
     * @param string $class
     */
    public function setResponseClass(string $class): void
    {
        $this->responseClass = $class;
    }

    /**
     * Set the default headers for every request.
     *
     * @param array $headers
     */
    public function setDefaultHeaders(array $headers): void
    {
        $this->defaultHeaders = $headers;
    }

    /**
     * Get the default headers.
     *
     * @return array
     */
    public function getDefaultHeaders(): array
    {
        return $this->defaultHeaders;
    }

    /**
     * Set the default curl options for every request.
     *
     * @param array $options
     */
    public function setDefaultOptions(array $options): void
    {
        $this->defaultOptions = $options;
    }

    /**
     * Get the default options.
     *
     * @return array
     */
    public function getDefaultOptions(): array
    {
        return $this->defaultOptions;
    }

    /**
     * Build an URL with an optional query string.
     *
     * @param  string $url the base URL without any query string
     * @param  array $query array of GET parameters
     *
     * @return string
     */
    public function buildUrl(string $url, array $query): string
    {
        if ( empty($query) )
        {
            return $url;
        }

        $parts = parse_url($url);

        $queryString = '';
        if ( isset($parts['query']) && $parts['query'] )
        {
            $queryString .= $parts['query'] . '&' . http_build_query($query);
        }
        else
        {
            $queryString .= http_build_query($query);
        }

        $retUrl = $parts['scheme'] . '://' . $parts['host'];
        if ( isset($parts['port']) )
        {
            $retUrl .= ':' . $parts['port'];
        }

        if ( isset($parts['path']) )
        {
            $retUrl .= $parts['path'];
        }

        if ( $queryString )
        {
            $retUrl .= '?' . $queryString;
        }

        return $retUrl;
    }

    /**
     * Create a new get response object and set its values.
     *
     * @param  string $url
     * @param  mixed $data POST data
     * @param int $encoding Request::ENCODING_* constant specifying how to process the POST data
     *
     * @return HttpRequest
     */
    public function get(string $url, int $encoding = HttpRequest::ENCODING_QUERY): HttpRequest
    {
        return $this->newRequest("get", $url, [], $encoding);
    }

    /**
     * Create a new post response object and set its values.
     *
     * @param  string $url
     * @param  mixed $data POST data
     * @param int $encoding Request::ENCODING_* constant specifying how to process the POST data
     *
     * @return HttpRequest
     */
    public function post(string $url, $data = [], int $encoding = HttpRequest::ENCODING_QUERY): HttpRequest
    {
        return $this->newRequest("post", $url, $data, $encoding);
    }

    /**
     * Create a new response object and set its values.
     *
     * @param  string $method get, post, etc
     * @param  string $url
     * @param  mixed $data POST data
     * @param int $encoding Request::ENCODING_* constant specifying how to process the POST data
     *
     * @return HttpRequest
     */
    public function newRequest(string $method, string $url, $data = [], int $encoding = HttpRequest::ENCODING_QUERY): HttpRequest
    {
        $class   = $this->requestClass;
        $request = new $class($this);

        if ( $this->defaultHeaders ) {
            $request->setHeaders($this->defaultHeaders);
        }

        if ( $this->defaultOptions ) {
            $request->setOptions($this->defaultOptions);
        }

        $request->setMethod($method);
        $request->setUrl($url);
        $request->setData($data);
        $request->setEncoding($encoding);

        return $request;
    }

    /**
     * Create a new JSON request and set its values.
     *
     * @param  string $method get, post etc
     * @param  string $url
     * @param  mixed $data POST data
     *
     * @return HttpRequest
     */
    public function newJsonRequest($method, $url, $data = array())
    {
        return $this->newRequest($method, $url, $data, HttpRequest::ENCODING_JSON);
    }

    /**
     * Create a new raw request and set its values.
     *
     * @param  string $method get, post etc
     * @param  string $url
     * @param  mixed $data request body
     *
     * @return HttpRequest
     */
    public function newFormRequest(string $method, string $url, $data = ''): HttpRequest
    {
        return $this->newRequest($method, $url, $data, HttpRequest::ENCODING_FORM_DATA);
    }

    /**
     * Create a new raw request and set its values.
     *
     * @param  string $method get, post etc
     * @param  string $url
     * @param  mixed $data request body
     *
     * @return HttpRequest
     */
    public function newRawRequest(string $method, string $url, $data = ''): HttpRequest
    {
        return $this->newRequest($method, $url, $data, HttpRequest::ENCODING_RAW);
    }

    /**
     * Prepare the curl resource for sending a request.
     *
     * @param  HttpRequest $request
     *
     * @return void
     */
    public function prepareRequest(HttpRequest $request) : void
    {
        $this->ch = \curl_init();
        \curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($this->ch, CURLOPT_HEADER, true);

        if ( $auth = $request->getUserAndPass()) {
            \curl_setopt($this->ch, CURLOPT_USERPWD, $auth);
        }

        \curl_setopt($this->ch, CURLOPT_URL, $request->getUrl());

        $options = $request->getOptions();

        if ( !empty($options) ) {
            curl_setopt_array($this->ch, $options);
        }

        $method = $request->getMethod();

        \curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        \curl_setopt($this->ch, CURLOPT_HTTPHEADER, $request->formatHeaders());

        if ( $request->hasData() ) {
            \curl_setopt($this->ch, CURLOPT_POSTFIELDS, $request->encodeData());
        }

        if ( $method === 'head' ) {
            \curl_setopt($this->ch, CURLOPT_NOBODY, true);
        }
    }

    /**
     * Send a request.
     *
     * @param  HttpRequest $request
     *
     * @return HttpResponse
     */
    public function sendRequest(HttpRequest $request) : HttpResponse
    {
        $this->prepareRequest($request);
        $result = curl_exec($this->ch);

        if ($result === false) {
            $errno  = curl_errno($this->ch);
            $errmsg = curl_error($this->ch);
            $msg    = "cURL request failed with error [$errno]: $errmsg";
            logText("Slim.SaveFile.Process.log", "curl::msg - " . $msg);
            curl_close($this->ch);
            throw new HttpException($request, $msg, $errno);
        }

        $response = $this->createResponseObject($result);

        curl_close($this->ch);

        return $response;
    }

    /**
     * Extract the response info, header and body from a cURL response. Saves
     * the data in variables stored on the object.
     *
     * @param  string $response
     *
     * @return HttpResponse
     */
    protected function createResponseObject($response) : HttpResponse
    {
        $info       = curl_getinfo($this->ch);
        $headerSize = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);

        $headers = substr($response, 0, $headerSize);
        $body    = substr($response, $headerSize);

        $class = $this->responseClass;

        return new $class($body, $headers, $info);
    }

    /**
     * Handle dynamic calls to the class.
     *
     * @param  string $func
     * @param  array $args
     *
     * @return mixed
     */
    public function __call ($func, $args)
    {
        $method = strtolower($func);

        $encoding = HttpRequest::ENCODING_QUERY;

        if ( substr($method, 0, 4) === 'json' )
        {
            $encoding = HttpRequest::ENCODING_JSON;
            $method   = substr($method, 4);
        }
        elseif ( substr($method, 0, 3) === 'raw' )
        {
            $encoding = HttpRequest::ENCODING_RAW;
            $method   = substr($method, 3);
        }

        if ( !array_key_exists($method, HttpRequest::$methods) )
        {
            throw new \BadMethodCallException("Method [$method] not a valid HTTP method.");
        }

        if ( !isset($args[0]) )
        {
            throw new \BadMethodCallException('Missing argument 1 ($url) for ' . __CLASS__ . '::' . $func);
        }
        $url = $args[0];

        if ( isset($args[1]) )
        {
            $data = $args[1];
        }
        else
        {
            $data = null;
        }

        $request = $this->newRequest($method, $url, $data, $encoding);

        return $this->sendRequest($request);
    }
}
