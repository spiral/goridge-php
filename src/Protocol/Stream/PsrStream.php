<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Protocol\Stream;

use Psr\Http\Message\StreamInterface as PsrStreamInterface;
use Spiral\Goridge\Exception\ProtocolException;

final class PsrStream implements DuplexStreamInterface
{
    /**
     * @var PsrStreamInterface
     */
    private PsrStreamInterface $stream;

    /**
     * @param PsrStreamInterface $stream
     */
    public function __construct(PsrStreamInterface $stream)
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
        $size = $this->stream->getSize();

        if ($size === null) {
            throw new ProtocolException('PSR implementation does not provide required stream size');
        }

        return $size;
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

    /**
     * {@inheritDoc}
     */
    public function write(string $string): int
    {
        return $this->stream->write($string);
    }
}
