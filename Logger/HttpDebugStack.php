<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiClientBundle\Logger;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
class HttpDebugStack implements HttpLoggerInterface
{
    protected $queries;
    protected $start;
    protected $currentQuery;
    protected $container;
    protected $logger;
    protected $stopwatch;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->queries = array();
        $this->start = null;
        $this->currentQuery = 0;
        $this->logger = null;
        $this->stopwatch = null;

        if ($container->has('logger')) {
            $this->logger = $container->get('logger');
        }

        if ($container->has('debug.stopwatch')) {
            $this->stopwatch = $container->get('debug.stopwatch');
        }
    }

    /**
     * Get queries
     *
     * @return array logged queries
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * {@inheritdoc}
     */
    public function startQuery($requestMethod, $requestUrl, array $requestHeaders = array(), array $requestQueryString = array())
    {
        if (null !== $this->stopwatch) {
            $this->stopwatch->start('da_api_client', 'da_api_client');
        }

        $id = $this->currentQuery;
        $this->start = microtime(true);
        $this->queries[$id] = array(
            'requestUrl'         => $requestUrl,
            'requestMethod'      => $requestMethod,
            'requestHeaders'     => json_encode($requestHeaders),
            'requestQueryString' => json_encode($requestQueryString),
            'executionMS'        => 0,
            'responseCode'       => null,
            'responseHeaders'    => null,
            'responseContent'    => null,
            'isCached'           => false,
            'cacheLifetime'      => null
        );
        $this->currentQuery++;

        return $id;
    }

    /**
     * {@inheritdoc}
     */
    public function stopQuery($id, $responseCode, $responseHeaders, $responseContent, $isCached, $cacheLifetime = null)
    {
        if (null !== $this->stopwatch) {
            $this->stopwatch->stop('da_api_client');
        }

        $this->queries[$id]['executionMS']     = (microtime(true) - $this->start) * 1000;
        $this->queries[$id]['responseCode']    = $responseCode;
        $this->queries[$id]['responseHeaders'] = json_encode($responseHeaders);
        $this->queries[$id]['responseContent'] = $responseContent;
        $this->queries[$id]['isCached']        = $isCached;
        $this->queries[$id]['cacheLifetime']   = $cacheLifetime;

        if (null !== $this->logger) {
            $this->logger->info(sprintf('DaApiClient [%s] Request', $id), array(
                'requestUrl'         => $this->queries[$id]['requestUrl'],
                'requestMethod'      => $this->queries[$id]['requestMethod'],
                'requestQueryString' => $this->queries[$id]['requestQueryString']
            ));
            $this->logger->debug(sprintf('DaApiClient [%s] Request debug', $id), array(
                'requestHeaders'     => $this->queries[$id]['requestHeaders']
            ));
            $this->logger->info(sprintf('DaApiClient [%s] Response', $id), array(
                'responseCode'       => $this->queries[$id]['responseCode'],
                'isCached'           => $this->queries[$id]['isCached'],
                'cacheLifetime'      => $this->queries[$id]['cacheLifetime']
            ));
            $this->logger->debug(sprintf('DaApiClient [%s] Response debug', $id), array(
                'responseHeaders'    => $this->queries[$id]['responseHeaders'],
                'responseContent'    => $this->queries[$id]['responseContent']
            ));
            $this->logger->info(sprintf('DaApiClient [%s] Execution time', $id), array(
                'executionTime'      => sprintf('%.2f ms', $this->queries[$id]['executionMS'])
            ));
        }
    }
}
