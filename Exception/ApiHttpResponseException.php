<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiClientBundle\Exception;

/**
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
class ApiHttpResponseException extends \Exception
{
    protected $url;
    protected $httpCode;
    protected $headers;
    protected $body;
    protected $method;
    protected $queryString;

    /**
     * Constructor
     *
     * @param string  $url
     * @param integer $httpCode
     * @param array   $headers
     * @param string  $body
     */
    public function __construct($url, $httpCode, $headers, $body, $method, $queryString)
    {
        $this->url         = $url;
        $this->httpCode    = $httpCode;
        $this->headers     = $headers;
        $this->body        = $body;
        $this->method      = $method;
        $this->queryString = $queryString;

        parent::__construct(sprintf('HTTP Api response error: -%s- [%s] %s (%s)',
            $httpCode,
            $method,
            $url,
            json_encode($queryString, JSON_UNESCAPED_UNICODE)
        ));
    }

    /**
     * Get request url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get response status code
     *
     * @return integer
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * Get response body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get response status code
     *
     * @return string
     */
    public function getStatusCode()
    {
        return $this->getHttpCode();
    }

    /**
     * Get response headers
     *
     * @return string
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get request method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get request query string
     *
     * @return mixed
     */
    public function getQueryString()
    {
        return $this->queryString;
    }
}
