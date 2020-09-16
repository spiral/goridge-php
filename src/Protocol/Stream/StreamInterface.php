<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Protocol\Stream;

interface StreamInterface extends \Stringable
{
    /**
     * Get the size of the stream.
     *
     * @return int Returns the size in bytes
     * @throws \RuntimeException if unable to read or an error occurs while reading.
     */
    public function getSize(): int;

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws \RuntimeException if unable to read or an error occurs while reading.
     */
    public function getContents(): string;
}
