<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Transport\Receiver;

use Spiral\Goridge\Protocol\DecoderInterface;
use Spiral\Goridge\Stream\FactoryInterface;

trait ResourceStreamReceiverTrait
{
    use ReceiverTrait;

    /**
     * @var \Generator|null
     */
    private ?\Generator $state = null;

    /**
     * @param FactoryInterface $factory
     * @param DecoderInterface $decoder
     * @param resource $stream
     */
    protected function push(FactoryInterface $factory, DecoderInterface $decoder, $stream): void
    {
        $this->state ??= $decoder->decode($factory);

        if ($this->state->valid()) {
            $length = $this->state->current();

            $chunk = \fread($stream, $length);

            $this->state->send($chunk);
        }

        if (! $this->state->valid()) {
            try {
                $response = $this->toMessage(...$this->state->getReturn());

                $this->emit($response);
            } finally {
                $this->state = null;
            }
        }
    }
}
