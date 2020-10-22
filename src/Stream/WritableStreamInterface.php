<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Stream;

interface WritableStreamInterface
{
    /**
     * Returns whether or not the stream is writable.
     *
     * @see https://www.php-fig.org/psr/psr-7/#34-psrhttpmessagestreaminterface
     * @return bool
     */
    public function isWritable(): bool;

    /**
     * Write data to the stream.
     *
     * @see https://www.php-fig.org/psr/psr-7/#34-psrhttpmessagestreaminterface
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    public function write(string $string): int;
}
