<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Transport;

use Amp\Loop;
use Spiral\Goridge\Protocol\DecoderInterface;
use Spiral\Goridge\Stream\FactoryInterface;

class AmpReceiver extends StreamReceiver
{
    /**
     * @var string
     */
    private string $watcher;

    /**
     * @param resource $stream
     * @param DecoderInterface $decoder
     * @param FactoryInterface|null $factory
     */
    public function __construct($stream, DecoderInterface $decoder, FactoryInterface $factory = null)
    {
        parent::__construct($stream, $decoder, $factory);

        $this->watcher = Loop::onReadable($stream, function ($_, $stream) {
            $this->push($this->factory, $this->decoder, $stream);
        });
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        Loop::cancel($this->watcher);
    }
}
