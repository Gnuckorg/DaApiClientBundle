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

use Doctrine\Common\Cache\Cache;
use Da\ApiClientBundle\Logger\HttpLoggerInterface;

/**
 * HttpTransportInterface.
 *
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
interface HttpTransportInterface
{
    /**
     * Constructor
     *
     * @param Cache $cache
     * @param HttpLoggerInterface $logger
     */
    public function __construct(Cache $cacher = null, HttpLoggerInterface $logger = null);

    /**
     * Get the cacher
     *
     * @return Cache
     */
    public function getCacher();

    /**
     * Get the logger
     *
     * @return HttpLoggerInterface
     */
    public function getLogger();

    /**
     * Get the request user agent
     *
     * @return string
     */
    public function getUserAgent();

    /**
     * Get the http request method
     *
     * @return string
     */
    public function getMethod();

    /**
     * Set the http request method
     *
     * @param string $method
     * @return HttpTransportInterface
     */
    public function setMethod($method);

    /**
     * Get the http request path
     *
     * @return string
     */
    public function getPath();

    /**
     * Set the http request path
     *
     * @param string $path
     * @return HttpTransportInterface
     */
    public function setPath($path);

    /**
     * Get http query strings
     *
     * @return array
     */
    public function getQueryStrings();

    /**
     * Set http query strings
     *
     * @param array $queryStrings
     * @return HttpTransportInterface
     */
    public function setQueryStrings($queryStrings);

    /**
     * Add a http query string to the request
     *
     * @param string $name
     * @param string $value
     *
     * @return HttpTransportInterface
     */
    public function addQueryString($name, $value);

    /**
     * Get http headers
     *
     * @return array
     */
    public function getHeaders();

    /**
     * Set http headers
     *
     * @param array $headers
     * @return HttpTransportInterface
     */
    public function setHeaders($headers);

    /**
     * Add a http header
     *
     * @param string $name
     * @param string $value
     *
     * @return HttpTransportInterface
     */
    public function addHeader($name, $value);

    /**
     * Send the http request
     *
     * @param boolean $noCache
     * @return Da\ApiClientBundle\Http\Response
     * @throw ApiHttpResponseException
     */
    public function send($noCache = false);
}
