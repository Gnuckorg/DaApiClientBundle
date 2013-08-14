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
class DebugStack implements RestLogger
{
    /**
     * @var array $queries Executed REST API queries.
     */
    public $queries = array();

    /** 
     * @var boolean $enabled If Debug Stack is enabled (log queries) or not.
     */
    public $enabled = true;

    public $start = null;
    public $currentQuery = 0;

    /**
     * {@inheritdoc}
     */
    public function startQuery($endpoint, $method = null, array $params = null)
    {
        if ($this->enabled) {
            $this->start = microtime(true);
            $this->queries[++$this->currentQuery] = array(
                'endpoint'      => $endpoint,
                'method'        => $method,
                'params'        => $params,
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
