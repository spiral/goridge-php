<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Transport\Connector;

interface ConnectorInterface
{
    /**
     * @param string $uri
     * @param bool $blocking
     * @return resource
     */
    public static function create(string $uri, bool $blocking = false);
}
