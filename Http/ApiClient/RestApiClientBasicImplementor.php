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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Da\ApiClientBundle\Logging\RestLoggerInterface;
use Da\AuthCommonBundle\Exception\ApiHttpResponseException;

/**
 * RestApiClientBasicImplementor is a basic implementation for a REST API client.
 *
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
class RestApiClientBasicImplementor extends AbstractRestApiClientImplementor
{
    protected $logger;
    protected $container;
    protected $environment;
    protected $isFirstTry;

    /**
     * Constructor.
     */
    public function __construct(RestLoggerInterface $logger, ContainerInterface $container, $environment)
    {
        $this->isFirstTry = true;
        $this->logger = $logger;
        // Use container to remove annoying circular dependencies.
        $this->container = $container;
        $this->environment = $environment;
    }

    /**
     * Get the access token if it exist or null otherwise.
     *
     * @return string|null The access token.
     */
    protected function getAccessToken()
    {
        $accessToken = null;

        // Use container to remove annoying circular dependencies.
        if ($this->container->has('security.context')) {
            $securityContext = $this->container->get('security.context');
            if (($token = $securityContext->getToken())) {
                $class = new \ReflectionClass($token);

                if ($class->hasMethod('getAccessToken')) {
                    $accessToken = $token->getAccessToken();
                }
            }
        }

        return $accessToken;
    }

    /**
     * Get Logger
     *
     * @return Da\ApiClientBundle\Logging\RestLogger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Add query string
     *
     * @param string $path
     * @param array  $queryString
     *
     * @return string
     */
    public static function addQueryString($path, array $queryString = array())
    {
        if(null === $queryString) {
            return $path;
        }

        return sprintf("%s%s%s",
            $path,
            preg_match("#\?#", $path) ? (
                preg_match("#\?$#", $path) ?
                '' : '&'
            ) : '?',
            http_build_query($queryString)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function get($path, array $queryString = array(), array $headers = array())
    {
        $path = self::addQueryString($path, $queryString);
        $logger = null;
        $transport = HttpTransportFactory::build('curl', $logger);

        return $transport
            ->setMethod('GET')
            ->setPath($path)
            ->setQueryString($queryString);
            ->setHeaders($headers);
            ->send()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function post($path, array $queryString = array(), array $headers = array())
    {
        return CurlTransport::getInstance(
            'POST',
            $path,
            $queryString,
            $headers
        )->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function put($path, array $queryString = array(), array $headers = array())
    {
        return CurlTransport::getInstance(
            'PUT',
            $path,
            $queryString,
            $headers
        )->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($path, array $queryString = array(), array $headers = array())
    {
        return CurlTransport::getInstance(
            'DELETE',
            $path,
            $queryString,
            $headers
        )->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function link($path, array $links, array $headers = array())
    {
        return CurlTransport::getInstance(
            'LINK',
            $path,
            $links,
            $headers
        )->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function unlink($path, array $links, array $headers = array())
    {
        return CurlTransport::getInstance(
            'UNLINK',
            $path,
            $links,
            $headers
        )->execute();
    }

    /**
     * Get the api endpoint path
     *
     * @param string $path
     * @return string The api path (url)
     */
    protected function getApiEndpointPath($path)
    {
        return sprintf('%s%s',
            $this->getEndpointRoot(),
            $path
        );
    }

    /**
     * Add the security tokens
     */
    protected function addSecurityTokens()
    {
        // API token.
        if($this->hasSecurityToken()) {
            $this->setHeader('X-API-Security-Token', $this->getSecurityToken());
        }

        // Access token (oauth).
        $accessToken = $this->getAccessToken();
        if ($accessToken) {
            $this->setHeader('Authorization', sprintf('Bearer %s', $accessToken));
        }
    }
}
