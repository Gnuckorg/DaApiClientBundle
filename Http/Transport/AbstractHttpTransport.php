<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiClientBundle\Http\Transport;

use Da\ApiClientBundle\Http\logger\RestLoggerInterface;

/**
 * AbstractHttpTransport
 *
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
abstract class AbstractHttpTransport implements HttpTransportInterface
{
    const USER_AGENT_NAME = "DaApiClient php/REST-UA";

    protected $method;
    protected $path;
    protected $queryStrings;
    protected $links;
    protected $headers;
    protected $logger;

    /**
     * {@inheritdoc}
     */
    public __construct(RestLoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserAgent()
    {
        return self::USER_AGENT_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryStrings()
    {
        return $this->queryStrings;
    }

    /**
     * {@inheritdoc}
     */
    public function setQueryStrings($queryStrings)
    {
        $this->queryStrings = queryStrings;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * {@inheritdoc}
     */
    public function setLinks($links)
    {
        $this->links = $links;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addLink($name, $value)
    {
        $this->links[$name] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * {@inheritdoc}
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addHeader($name, $value)
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function send()
    {
        $response = $this
            ->buildRequest()
            ->executeRequest()
        ;

        if ($response->getStatusCode() >= 400) {
            throw new ApiHttpResponseException(
                $response->getUrl(),
                $response->getCode(),
                $response->getContent()
            );
        }

        return $response->getContent();
    }

    /**
     * Build the http request
     */
    abstract protected function buildRequest();

    /**
     * Execute the http Request
     *
     * @return Da\ApiClientBundle\Http\Response
     */
    abstract protected function executeRequest();

}
