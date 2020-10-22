<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge;

use Spiral\Goridge\Exception\ProtocolException;
use Spiral\Goridge\Protocol\DecoderInterface;
use Spiral\Goridge\Protocol\EncoderInterface;
use Spiral\Goridge\Protocol\Type;
use Spiral\Goridge\Protocol\Version;
use Spiral\Goridge\Protocol\Version1\Decoder;
use Spiral\Goridge\Protocol\Version1\Encoder;
use Spiral\Goridge\Stream\FactoryInterface;

/**
 * Communicates with remote server/client using byte payload
 */
final class Protocol implements DecoderInterface, EncoderInterface
{
    /**
     * @var string
     */
    private const ERROR_UNSUPPORTED_VERSION = 'Unsupported protocol version (%d.%d.%d)';

    /**
     * @var int
     */
    public const DEFAULT_CHUNK_SIZE = 65536;

    /**
     * @var DecoderInterface
     */
    private DecoderInterface $decoder;

    /**
     * @var EncoderInterface
     */
    private EncoderInterface $encoder;

    /**
     * @param int $version
     * @param int $chunkSize
     */
    public function __construct(int $version = Version::VERSION_1, int $chunkSize = self::DEFAULT_CHUNK_SIZE)
    {
        $this->boot($version, $chunkSize);
    }

    /**
     * @param int $version
     * @param int $chunkSize
     */
    private function boot(int $version, int $chunkSize): void
    {
        if ($version === Version::VERSION_1) {
            $this->bootVersion1($chunkSize);

            return;
        }

        if ($version === Version::VERSION_2) {
            // Not yet =)
        }

        $message = \vsprintf(self::ERROR_UNSUPPORTED_VERSION, [
            Version::major($version),
            Version::minor($version),
            Version::patch($version),
        ]);

        throw new ProtocolException($message);
    }

    /**
     * @param int $chunkSize
     */
    private function bootVersion1(int $chunkSize): void
    {
        $this->decoder = new Decoder($chunkSize);
        $this->encoder = new Encoder($chunkSize);
    }

    /**
     * {@inheritDoc}
     */
    public function decode(FactoryInterface $factory): \Generator
    {
        return $this->decoder->decode($factory);
    }

    /**
     * {@inheritDoc}
     */
    public function encode($message, int $type = Type::TYPE_MESSAGE): iterable
    {
        return $this->encoder->encode($message, $type);
    }
}
