<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiClientBundle\Http\Logger;

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
    public function startQuery($requestUrl, $requestMethod, $requestHeaders = array(), array $requestQueryString = null)
    {
        $id = $this->currentQuery;
        $this->start = microtime(true);
        $this->queries[$id] = array(
            'requestUrl'         => $requestUrl,
            'requestMethod'      => $requestMethod,
            'requestHeaders'     => $requestHeaders,
            'requestQueryString' => json_encode($requestQueryString),
            'executionMS'        => 0,
            'responseCode'       => null,
            'responseHeaders'    => null,
            'responseContent'    => null
        );
        $this->currentQuery++;

        return $id;
    }

    /**
     * {@inheritdoc}
     */
    public function stopQuery($id, $responseCode, $responseHeaders, $responseContent)
    {
        $this->queries[$id]['executionMS'] = microtime(true) - $this->start;
        $this->queries[$id]['responseCode'] = $responseCode;
        $this->queries[$id]['responseHeaders'] = $responseHeaders;
        $this->queries[$id]['responseContent'] = $responseContent;
    }
}
