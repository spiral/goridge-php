<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Transport;

use Spiral\Goridge\Protocol\Stream\ReadableStreamInterface;
use Spiral\Goridge\Protocol\Type;

interface ResponderInterface
{
    /**
     * @param string|ReadableStreamInterface $message
     * @param int $type
     * @return void
     */
    public function send($message, int $type = Type::TYPE_MESSAGE): void;
}
