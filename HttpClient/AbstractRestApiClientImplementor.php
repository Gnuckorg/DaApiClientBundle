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
     * Check if the security token is present
     *
     * @return boolean true if the token is set.
     */
    public function hasSecurityToken()
    {
        return null !== $this->getSecurityToken();
    }
}
