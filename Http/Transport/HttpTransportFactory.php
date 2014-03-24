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

use Da\ApiClientBundle\Logger\HttpLoggerInterface;

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
     * @param string              $transportName
     * @param HttpLoggerInterface $logger
     * @return HttpTransportInterface
     */
    public static function build($transportName, HttpLoggerInterface $logger = null)
    {
        $className = sprintf(
            'Da\ApiClientBundle\Http\Transport\%sHttpTransport',
            ucfirst(strtolower($transportName))
        );

        return new $className($logger);
    }
}
