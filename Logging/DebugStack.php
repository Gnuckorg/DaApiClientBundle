<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiClientBundle\Logging;

/**
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
class DebugStack implements RestLoggerInterface
{
    /**
     * Executed REST API queries.
     *
     * @var array
     */
    protected $queries = array();

    /** 
     * If Debug Stack is enabled (log queries) or not.
     *
     * @var boolean
     */
    protected $enabled = true;

    protected $start = null;
    protected $currentQuery = 0;

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
    public function startQuery($endpoint, $method = null, array $queryString = null)
    {
        if ($this->enabled) {
            $this->start = microtime(true);
            $this->queries[++$this->currentQuery] = array(
                'endpoint'      => $endpoint,
                'method'        => $method,
                'queryString'   => json_encode($queryString),
                'executionMS'   => 0
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function stopQuery()
    {
        if ($this->enabled) {
            $this->queries[$this->currentQuery]['executionMS'] = microtime(true) - $this->start;
        }
    }
}
