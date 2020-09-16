<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Protocol;

use Spiral\Goridge\Protocol\Stream\ReadableStreamInterface;

interface EncoderInterface
{
    /**
     * @param string|ReadableStreamInterface $message
     * @param int $type
     * @return string[]
     */
    public function encode($message, int $type = Type::TYPE_MESSAGE): iterable;
}
