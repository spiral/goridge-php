<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Stream;

interface ReadableStreamInterface
{
    /**
     * Returns whether or not the stream is readable.
     *
     * @see https://www.php-fig.org/psr/psr-7/#34-psrhttpmessagestreaminterface
     * @return bool
     */
    public function isReadable(): bool;

    /**
     * Read data from the stream.
     *
     * @param int $size Read up to $size bytes from the object and return them.
     *                  Fewer than $size bytes may be returned if underlying
     *                  stream call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty
     *                string if no bytes are available.
     *
     * @throws \RuntimeException if an error occurs.
     */
    public function read(int $size): string;
}
