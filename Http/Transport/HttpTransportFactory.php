<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiClientBundle\Http\Transport;

use Doctrine\Common\Cache\Cache;
use Da\ApiClientBundle\Logger\HttpLoggerInterface;
use Da\ApiClientBundle\Exception\UndefinedTransportException;

/**
 * HttpTransportFactory
 *
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
abstract class HttpTransportFactory
{
    /**
     * Build
     *
     * @param  string                 $transportName
     * @param  Cache                  $cache
     * @param  HttpLoggerInterface    $logger
     * @return HttpTransportInterface
     */
    public static function build($transportName, Cache $cache = null, HttpLoggerInterface $logger = null)
    {
        $className = sprintf(
            'Da\ApiClientBundle\Http\Transport\%sHttpTransport',
            ucfirst(strtolower($transportName))
        );

        if (!class_exists($className)) {
            throw new UndefinedTransportException($className);
        }

        return new $className($cache, $logger);
    }
}
