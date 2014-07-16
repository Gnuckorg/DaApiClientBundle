<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiClientBundle\Http\Rest;

use Doctrine\Common\Cache\Cache;
use Da\ApiClientBundle\Logger\HttpLoggerInterface;

/**
 * RestApiClientBridge is the abstraction class of a bridge pattern allowing
 * to dynamically change the implementation for the REST API client.
 *
 * @author Thomas Prelot <tprelot@gmail.com>
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
class RestApiClientBridge implements RestApiClientInterface
{
    /**
     * The implementor.
     *
     * @var RestApiClientImplementorInterface
     */
    protected $implementor;

    /**
     * Constructor.
     *
     * @param RestApiClientImplementorInterface $implementor
     * @param array                             $configuration
     */
    public function __construct(
        RestApiClientImplementorInterface $implementor,
        array $configuration
    )
    {
        $this->implementor = $implementor;
        $this->implementor
            ->setEndpointRoot($configuration['endpoint_root'])
            ->setSecurityToken($configuration['security_token'])
            ->setCacheEnabled($configuration['cache_enabled'])
            ->setLogEnabled($configuration['log_enabled'])
        ;
    }

    /**
     * Set the cacher to cache rest api request
     *
     * @param Cache $cacher The cacher to cache rest api
     *
     * @return RestApiClientInterface.
     */
    public function setCacher($cacher)
    {
        $this->implementor->setCacher($cacher);

        return $this;
    }

    /**
     * Set the logger to log rest api request
     *
     * @param HttpLoggerInterface $logger The logger to log rest api
     *
     * @return RestApiClientInterface.
     */
    public function setLogger($logger)
    {
        $this->implementor->setLogger($logger);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEndpointRoot()
    {
        return $this->implementor->getEndpointRoot();
    }

    /**
     * {@inheritdoc}
     */
    public function get($path, array $queryString = array(), array $headers = array(), $noCache = false, $absolutePath = false)
    {
        return $this->implementor->get($path, $queryString, $headers, $noCache, $absolutePath);
    }

    /**
     * {@inheritdoc}
     */
    public function post($path, array $queryString = array(), array $headers = array())
    {
        return $this->implementor->post($path, $queryString, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function put($path, array $queryString = array(), array $headers = array())
    {
        return $this->implementor->put($path, $queryString, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function patch($path, array $queryString = array(), array $headers = array())
    {
        return $this->implementor->patch($path, $queryString, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($path, array $queryString = array(), array $headers = array())
    {
        return $this->implementor->delete($path, $queryString, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function link($path, array $links, array $headers = array())
    {
        return $this->implementor->link($path, $links, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function unlink($path, array $links, array $headers = array())
    {
        return $this->implementor->unlink($path, $links, $headers);
    }

}
