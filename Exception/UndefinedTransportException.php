<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiClientBundle\Exception;

/**
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
class UndefinedTransportException extends \Exception
{
    public function __construct($name)
    {
        parent::__construct(sprintf('Undefined transport: %s', $name));
    }
}
