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
use Da\ApiClientBundle\Logger\HttpLoggerInterface;

/**
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
class ApiClientDataCollector extends DataCollector
{
    protected $logger;

    /**
     * Constructor
     *
     * @param HttpLoggerInterface $logger
     */
    public function __construct(HttpLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Get Logger
     *
     * @return HttpLoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Get API queries
     *
     * @return array
     */
    public function getApiQueries()
    {
        return $this->data['api_queries'];
    }

    /**
     * Count API queries
     *
     * @return integer
     */
    public function countApiQueries()
    {
        return count($this->getApiQueries());
    }

    /**
     * Sum the queries execution times
     *
     * @return float The execution time in ms
     */
    public function getSumExecutionMS()
    {
        $sum = 0;
        foreach($this->getApiQueries() as $query) {
            $sum += $query['executionMS'];
        }

        return $sum;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data['api_queries'] = $this->getLogger()->getQueries();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'api';
    }
}
