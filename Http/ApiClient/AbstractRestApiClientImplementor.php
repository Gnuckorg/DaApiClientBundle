<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiClientBundle\Http\ApiClient;

use Da\ApiClientBundle\Http\Logger\RestLoggerInterface;

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
     * The flag to enable the cache.
     *
     * @var boolean
     */
    protected $cacheEnabled = true;

    /**
     * The logger.
     *
     * @var RestLoggerInterface
     */
    protected $logger = null;

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
    public function enableCache($cacheEnabled)
    {
        $this->cacheEnabled = (bool)$cacheEnabled;

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
    public function hasSecurityToken()
    {
        return null !== $this->getSecurityToken();
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
    public function setLogger(RestLoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this->logger;
    }
}
