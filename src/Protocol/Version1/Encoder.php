<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Protocol\Version1;

use Psr\Http\Message\StreamInterface;
use Spiral\Goridge\Exception\ProtocolException;
use Spiral\Goridge\Protocol\Encoder as BaseEncoder;
use Spiral\Goridge\Protocol\Stream\ReadableStreamInterface;
use Spiral\Goridge\Protocol\Type;

final class Encoder extends BaseEncoder
{
    /**
     * @var string
     */
    private const ERROR_ENCODE = 'Unable to pack message data';

    /**
     * @param string $message
     * @param int $type
     * @return string[]
     */
    private function encodeString(string $message, int $type): iterable
    {
        return [$this->packHeader($type, $size = \strlen($message)) . $message];
    }

    /**
     * @param ReadableStreamInterface $stream
     * @param int $type
     * @return string[]
     */
    private function encodeStream(ReadableStreamInterface $stream, int $type): iterable
    {
        yield $this->packHeader($type, $size = $stream->getSize());

        yield from $this->stream($size, static function (int $chunk) use ($stream): string {
            return $stream->read($chunk);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function encode($message, int $type = Type::TYPE_MESSAGE): iterable
    {
        $this->assertIsMessage($message);

        if (\is_string($message)) {
            return $this->encodeString($message, $type);
        }

        return $this->encodeStream($message, $type);
    }

    /**
     * @param int $type
     * @param int $size
     * @return string
     */
    private function packHeader(int $type, int $size): string
    {
        $flags = Payload::pack($type);

        // Special case for empty messages
        if ($size === 0) {
            $flags |= Payload::PAYLOAD_EMPTY;
        }

        $body = @\pack('CPJ', $flags, $size, $size);

        if (! \is_string($body)) {
            throw new ProtocolException(self::ERROR_ENCODE, 0x02);
        }

        return $body;
    }
}
