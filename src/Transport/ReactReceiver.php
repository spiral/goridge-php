<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Transport;

use React\EventLoop\LoopInterface;
use Spiral\Goridge\Protocol\DecoderInterface;
use Spiral\Goridge\Protocol\Stream\FactoryInterface;

class ReactReceiver extends StreamReceiver
{
    /**
     * @var LoopInterface
     */
    private LoopInterface $loop;

    /**
     * @param LoopInterface $loop
     * @param resource $stream
     * @param DecoderInterface $decoder
     * @param FactoryInterface|null $factory
     * @throws \Exception
     */
    public function __construct(LoopInterface $loop, $stream, DecoderInterface $decoder, FactoryInterface $factory = null)
    {
        parent::__construct($stream, $decoder, $factory);

        $this->loop = $loop;

        $loop->addReadStream($stream, function ($stream) {
            $this->push($this->factory, $this->decoder, $stream);
        });
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        $this->loop->removeReadStream($this->stream);
    }
}
