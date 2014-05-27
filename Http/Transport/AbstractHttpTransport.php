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

use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Doctrine\Common\Cache\Cache;
use Da\ApiClientBundle\Logger\HttpLoggerInterface;
use Da\ApiClientBundle\Exception\ApiHttpResponseException;
use Da\ApiClientBundle\Http\Response;

/**
 * AbstractHttpTransport
 *
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
abstract class AbstractHttpTransport implements HttpTransportInterface
{
    public static $USER_AGENT_NAME = "DaApiClient php/REST-UA";

    protected $method;
    protected $path;
    protected $queryStrings;
    protected $links;
    protected $headers;
    protected $cacher;
    protected $logger;

    /**
     * {@inheritdoc}
     */
    public function __construct(Cache $cacher = null, HttpLoggerInterface $logger = null)
    {
        $this->method       = null;
        $this->path         = null;
        $this->queryStrings = array();
        $this->links        = array();
        $this->headers      = array();
        $this->cacher       = $cacher;
        $this->logger       = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacher()
    {
        return $this->cacher;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserAgent()
    {
        return static::$USER_AGENT_NAME;
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
        $this->queryStrings = $queryStrings;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addQueryString($name, $value)
    {
        $this->queryStrings[$name] = $value;

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
    public function addLink($value)
    {
        $this->links[] = $value;

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
    public function send($noCache = false)
    {
        $isCached = false;
        $cacheLifetime = null;
        $queryId = null;
        $this->buildRequest();

        if ($this->getLogger()) {
            $queryId = $this->getLogger()->startQuery(
                $this->getMethod(),
                $this->getPath(),
                $this->getHeaders(),
                $this->getQueryStrings()
            );
        }

        $response = false;
        if (!$noCache && $this->getCacher()) {
            $response = $this->getCacher()->fetch($this->getHash());
        }

        if (!$response) {
            $response = $this->executeRequest();
        } else {
            $isCached = true;
            $cacheLifetime = $this->getCacheLifetime($response->headers);
        }

        if ($this->getCacher() &&
            $this->isRequestCachable($response) &&
            ! $this->getCacher()->contains($this->getHash())
        ) {
            $cacheLifetime = $this->getCacheLifetime($response->headers);
            $this->getCacher()->save($this->getHash(), $response, $cacheLifetime);
        }

        if (null !== $queryId) {
            $this->getLogger()->stopQuery(
                $queryId,
                $response->getStatusCode(),
                $response->headers->all(),
                $response->getContent(),
                $isCached,
                $cacheLifetime
            );
        }

        if ($response->getStatusCode() >= 400) {
            throw new ApiHttpResponseException(
                $response->getUrl(),
                $response->getStatusCode(),
                $response->headers->all(),
                $response->getContent()
            );
        }

        return $response;
    }

    /**
     * Get a Hash which identify the request
     *
     * @return string
     */
    protected function getHash()
    {
        return md5(sprintf('%s %s %s %s',
            $this->getMethod(),
            $this->getPath(),
            json_encode($this->getHeaders()),
            json_encode($this->getQueryStrings())
        ));
    }

    /**
     * Check if the request is cachable following to the RFC2616
     * https://www.ietf.org/rfc/rfc2616.txt
     *
     * @param  Response $response
     * @return boolean
     */
    protected function isRequestCachable(Response $response)
    {
        if (!in_array($this->getMethod(), array('GET', 'HEAD'))) {
            return false;
        }

        if ($response->headers->hasCacheControlDirective('no-cache') ||
            'no-cache' == $response->headers->get('Pragma')
        ) {
            return false;
        }

        return true;
    }

    /**
     * Get the cache lifetime based on the response headers
     *
     * @param  ResponseHeaderBag $headers
     * @return integer
     */
    protected function getCacheLifetime(ResponseHeaderBag $headers)
    {
        $maxAge = 0;
        if ($headers->hasCacheControlDirective('max-age')) {
            $maxAge = $headers->getCacheControlDirective('max-age');
        }

        return $maxAge;
    }

    /**
     * Build the http request
     *
     * @return AbstractHttpTransport
     */
    abstract protected function buildRequest();

    /**
     * Execute the http Request
     *
     * @return Response
     */
    abstract protected function executeRequest();
}
