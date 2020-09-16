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

abstract class Protocol implements ProtocolInterface
{
    /**
     * @var string
     */
    private const ERROR_CHUNK_TYPE = 'Can not read input stream, chunk must a type of string, but %s given';

    /**
     * @var int
     */
    public const DEFAULT_CHUNK_SIZE = 65536;

    /**
     * @var int
     */
    protected int $chunkSize;

    /**
     * @param int $chunkSize
     */
    public function __construct(int $chunkSize = self::DEFAULT_CHUNK_SIZE)
    {
        $this->chunkSize = $chunkSize;
    }

    /**
     * @return int
     */
    public function getChunkSize(): int
    {
        return $this->chunkSize;
    }

    /**
     * @param int $bytesLeft
     * @return \Generator
     */
    protected function streamToString(int $bytesLeft): \Generator
    {
        $buffer = '';

        yield from $this->stream($bytesLeft, static function (string $chunk) use (&$buffer): void {
            $buffer .= $chunk;
        });

        return $buffer;
    }

    /**
     * @param int $bytesLeft
     * @param \Closure $onRead
     * @return \Generator
     */
    protected function stream(int $bytesLeft, \Closure $onRead): \Generator
    {
        if ($bytesLeft === 0) {
            return;
        }

        while ($bytesLeft > 0) {
            $chunk = yield $length = $this->chunkSize($bytesLeft);

            if (! \is_string($chunk)) {
                throw $this->errorChunkType($chunk);
            }

            $onRead($chunk);
            $bytesLeft -= \strlen($chunk);
        }
    }

    /**
     * @param int $bytesLeft
     * @return int
     */
    protected function chunkSize(int $bytesLeft): int
    {
        return \min($this->chunkSize, $bytesLeft);
    }

    /**
     * @param mixed $given
     * @return ProtocolException
     */
    private function errorChunkType($given): ProtocolException
    {
        $message = \sprintf(self::ERROR_CHUNK_TYPE, \get_debug_type($given));

        return new ProtocolException($message, 0x01);
    }
}
