<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Protocol\Version1;

use Spiral\Goridge\Exception\ProtocolException;
use Spiral\Goridge\Protocol\Decoder as BaseDecoder;
use Spiral\Goridge\Stream\FactoryInterface;

final class Decoder extends BaseDecoder
{
    /**
     * @var string
     */
    private const ERROR_BAD_HEADER = 'An error occurred while decoding the message header';

    /**
     * @var string
     */
    private const ERROR_BAD_CHECKSUM = 'An error occurred while checking the message checksum';

    /**
     * {@inheritDoc}
     */
    public function decode(FactoryInterface $factory): \Generator
    {
        /**
         * Decode header to string buffer (17 bytes)
         */
        yield from $header = $this->streamToString(Payload::HEADER_SIZE);

        [$size, $type] = $this->decodeHeader($header->getReturn());

        /**
         * Initialize new buffer
         */
        $buffer = $factory->create($size + Payload::HEADER_SIZE, $this->chunkSize);

        /**
         * Write stream to buffer
         */
        yield from $this->stream($size, fn (string $chunk) => $buffer->write($chunk));

        return [$buffer, $type];
    }

    /**
     * Decode the message's header and returns the further message length and
     * flags in array format "[0 => $length, 1 => $flags]".
     *
     * @param string $header
     * @return array
     */
    private function decodeHeader(string $header): array
    {
        /** @psalm-var array{flags: int, size: int, revs: int}|false $result */
        $result = @\unpack('Cflags/Psize/Jrevs', $header);

        if (! \is_array($result)) {
            throw $this->errorHeaderFormat();
        }

        if ($result['size'] !== $result['revs']) {
            throw $this->errorHeaderChecksum();
        }

        return [$result['size'], Payload::unpack($result['flags'])];
    }

    /**
     * @return ProtocolException
     */
    private function errorHeaderFormat(): ProtocolException
    {
        return new ProtocolException(self::ERROR_BAD_HEADER, 0x01);
    }

    /**
     * @return ProtocolException
     */
    private function errorHeaderChecksum(): ProtocolException
    {
        return new ProtocolException(self::ERROR_BAD_CHECKSUM, 0x02);
    }
}
