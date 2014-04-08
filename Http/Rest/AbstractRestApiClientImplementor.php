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
 * AbstractRestApiClientImplementor is an abstract class helping to 
 * define your own implementor with a basic implementation for some methods
 * of the RestApiClientImplementorInterface interface.
 *
 * @author Thomas Prelot
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
abstract class AbstractRestApiClientImplementor implements RestApiClientImplementorInterface
{
    /**
     * The endpoint root.
     *
     * @var string
     */
    protected $endpointRoot;

    /**
     * The security token.
     *
     * @var string
     */ 
    protected $securityToken;

    /**
     * The Cacher.
     *
     * @var Cache
     */
    protected $cacher;

    /**
     * The flag to use or not the cacher.
     *
     * @var boolean
     */
    protected $cacheEnabled;

    /**
     * The logger.
     *
     * @var HttpLoggerInterface
     */
    protected $logger;

    /**
     * The flag to use or not the logger.
     *
     * @var boolean
     */
    protected $logEnabled;

    /**
     * {@inheritdoc}
     */
    public function setEndpointRoot($endpointRoot)
    {
        $this->endpointRoot = $endpointRoot;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEndpointRoot()
    {
        return $this->endpointRoot;
    }

    /**
     * {@inheritdoc}
     */
    public function setSecurityToken($securityToken)
    {
        $this->securityToken = $securityToken;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSecurityToken()
    {
        return $this->securityToken;
    }

    /**
     * {@inheritdoc}
     */
    public function hasSecurityToken()
    {
        return null !== $this->getSecurityToken();
    }

    /**
     * {@inheritdoc}
     */
    public function getCacher()
    {
        if (!$this->isCacheEnabled()) {
            return null;
        }

        return $this->cacher;
    }

    /**
     * {@inheritdoc}
     */
    public function setCacher(Cache $cacher)
    {
        $this->cacher = $cacher;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isCacheEnabled()
    {
        return $this->cacheEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setCacheEnabled($cacheEnabled)
    {
        $this->cacheEnabled = $cacheEnabled;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogger()
    {
        if (!$this->isLogEnabled()) {
            return null;
        }

        return $this->logger;
    }

    /**
     * {@inheritdoc}
     */
    public function setLogger(HttpLoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isLogEnabled()
    {
        return $this->logEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setLogEnabled($logEnabled)
    {
        $this->logEnabled = $logEnabled;

        return $this;
    }

    /**
     * Init headers
     *
     * @param array $headers
     * @return array
     */
    protected function initHeaders(array $headers = array())
    {
        // API token.
        if ($this->hasSecurityToken()) {
            $headers['X-API-Security-Token'] = $this->getSecurityToken();
        }

        // Access token (oauth).
        $accessToken = $this->getAccessToken();
        if ($accessToken) {
            $headers['Authorization'] = sprintf('Bearer %s', $accessToken);
        }

        return $headers;
    }

    /**
     * Get the access token if exist, null otherwise.
     *
     * @return string|null The access token.
     */
    abstract protected function getAccessToken();
}
