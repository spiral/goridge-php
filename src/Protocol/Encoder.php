<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Protocol;

use Spiral\Goridge\Exception\ProtocolException;
use Spiral\Goridge\Protocol\Stream\ReadableStreamInterface;

abstract class Encoder extends Protocol implements EncoderInterface
{
    /**
     * @var string
     */
    protected const ERROR_INVALID_MESSAGE_ARGUMENT = 'Proceed message must be type of string or %s, but %s passed';

    /**
     * @param string|ReadableStreamInterface $message
     */
    protected function assertIsMessage($message): void
    {
        if (! $this->isMessage($message)) {
            $error = \vsprintf(static::ERROR_INVALID_MESSAGE_ARGUMENT, [
                ReadableStreamInterface::class,
                \get_debug_type($message),
            ]);

            throw new ProtocolException($error);
        }
    }

    /**
     * @param string|ReadableStreamInterface $message
     * @return bool
     */
    protected function isMessage($message): bool
    {
        return \is_string($message) || $message instanceof ReadableStreamInterface;
    }
}
