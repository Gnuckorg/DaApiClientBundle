<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiClientBundle\HttpClient;

/**
 * RestApiClientBridge is the abstraction class of a bridge pattern allowing
 * to dynamically change the implementation for the REST API client.
 *
 * @author Thomas Prelot
 */
class RestApiClientBridge implements RestApiClientInterface
{
    /**
     * The implementor.
     *
     * @var RestApiClientImplementorInterface
     */
    private $implementor;
    
    /**
     * Constructor.
     *
     * @param RestApiClientImplementorInterface $implementor
     * @param array                             $config
     */
    public function __construct(RestApiClientImplementorInterface $implementor, array $config)
    {
        $this->implementor = $implementor;
        $this->implementor->setBaseUrl($config['base_url']);
        $this->implementor->setApiToken($config['api_token']);
        $this->implementor->enableCache($config['cache_enabled']);
    }

    /**
     * {@inheritdoc}
     */
    public function get($path, $queryString = null)
    {
        return $this->implementor->get($path, $queryString);
    }

    /**
     * {@inheritdoc}
     */
    public function post($path, $queryString = null)
    {
        return $this->implementor->post($path, $queryString);
    }

    /**
     * {@inheritdoc}
     */
    public function put($path, $queryString = null)
    {
        return $this->implementor->put($path, $queryString);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($path, $queryString = null)
    {
        return $this->implementor->delete($path, $queryString);
    }
}
