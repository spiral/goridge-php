<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Transport;

use Spiral\Goridge\Protocol\DecoderInterface;
use Spiral\Goridge\Protocol\Stream\FactoryInterface;
use Spiral\Goridge\Protocol\Stream\Stream;
use Spiral\Goridge\Transport\Receiver\ResourceStreamReceiverTrait;

abstract class StreamReceiver extends Receiver
{
    use ResourceStreamReceiverTrait;

    /**
     * @var resource
     */
    protected $stream;

    /**
     * @param resource $stream
     * @param DecoderInterface $decoder
     * @param FactoryInterface|null $factory
     */
    public function __construct($stream, DecoderInterface $decoder, FactoryInterface $factory = null)
    {
        parent::__construct($decoder, $factory);

        Stream::assertIsValid($stream);
        Stream::assertIsWritable($stream);

        $this->stream = $stream;
    }
}
