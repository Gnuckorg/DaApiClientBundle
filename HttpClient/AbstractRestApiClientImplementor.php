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
 */
abstract class AbstractRestApiClientImplementor implements RestApiClientImplementorInterface
{
    /**
     * The base URL.
     *
     * @var string
     */
    protected $baseUrl = '';
    
    /**
     * The API token.
     *
     * @var string
     */ 
    protected $apiToken = '';

    /**
     * The flag to enable the cache.
     *
     * @var boolean
     */
    protected $cacheEnabled = true;

    /**
     * {@inheritdoc}
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;
    }

    /**
     * {@inheritdoc}
     */
    public function enableCache($cacheEnabled)
    {
        if ($cacheEnabled) {
            $this->cacheEnabled = true; 
        }
        else {
            $this->cacheEnabled = false; 
        }
    }
}