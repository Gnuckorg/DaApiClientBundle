<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiClientBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
class RestApiClientDataCollector extends DataCollector
{
    protected $restLogger;

    /**
     * Constructor
     *
     * @param \Da\ApiClientBundle\Logging\RestLoggerInterface $restLogger
     */
    public function __construct(\Da\ApiClientBundle\Logging\RestLoggerInterface $restLogger)
    {
        $this->restLogger = $restLogger;
    }

    /**
     * Get Rest Logger
     *
     * @return \Da\ApiClientBundle\Logging\RestLoggerInterface
     */
    public function getRestLogger()
    {
        return $this->restLogger;
    }

    /**
     * Get Rest API queries
     *
     * @return array
     */
    public function getRestApiQueries()
    {
        return $this->data['rest_api_queries'];
    }

    /**
     * Count Rest API queries
     *
     * @return integer
     */
    public function countRestApiQueries()
    {
        return count($this->getRestApiQueries());
    }

    /**
     * Sum the queries execution times
     *
     * @return float The execution time in ms
     */
    public function getSumExecutionMS()
    {
        $sum = 0;
        foreach($this->getRestApiQueries() as $query) {
            $sum += $query['executionMS'];
        }

        return $sum;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data['rest_api_queries'] = $this->getRestLogger()->getQueries();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rest_api';
    }
}
