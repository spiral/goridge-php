<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Transport;

use Spiral\Goridge\Transport\Receiver\MessageInterface;

class SyncStreamReceiver extends StreamReceiver implements SyncReceiverInterface
{
    /**
     * @return MessageInterface
     */
    public function waitForResponse(): MessageInterface
    {
        $message = $this->reduce(function (int $length) {
            return \fread($this->stream, $length);
        });

        try {
            $this->emit($message);
        } finally {
            return $message;
        }
    }

    /**
     * @param \Closure $reader
     * @return MessageInterface
     */
    private function reduce(\Closure $reader): MessageInterface
    {
        $stream = $this->decoder->decode($this->factory);

        while ($stream->valid()) {
            $chunk = $reader($stream->current());

            $stream->send($chunk);
        }

        return $this->toMessage(...$stream->getReturn());
    }
}
