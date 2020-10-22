<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Transport\Receiver;

use Spiral\Goridge\Stream\ReadableStreamInterface;

class Message implements MessageInterface
{
    /**
     * @var ReadableStreamInterface
     */
    private ReadableStreamInterface $stream;

    /**
     * @param ReadableStreamInterface $stream
     */
    public function __construct(ReadableStreamInterface $stream)
    {
        $this->stream = $stream;
    }

    /**
     * {@inheritDoc}
     */
    public function read(int $size): string
    {
        return $this->stream->read($size);
    }

    /**
     * {@inheritDoc}
     */
    public function getSize(): int
    {
        return $this->stream->getSize();
    }

    /**
     * {@inheritDoc}
     */
    public function getContents(): string
    {
        return $this->stream->getContents();
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        return (string)$this->stream;
    }
}
