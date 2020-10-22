<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Stream;

/**
 * Provides a buffer stream that can be written to to fill a buffer, and read
 * from to remove bytes from the buffer.
 */
final class BufferStream extends ResourceStream
{
    /**
     * @param string $message
     */
    public function __construct(string $message = '')
    {
        $stream = \fopen('php://memory', 'ab+');

        \fwrite($stream, $message);
        \rewind($stream);

        parent::__construct($stream, \strlen($message));
    }
}
