<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Transport;

use Spiral\Goridge\Exception\ProtocolException;
use Spiral\Goridge\Protocol\Stream\ReadableStreamInterface;
use Spiral\Goridge\Protocol\Type;

class SyncStreamResponder extends StreamResponder
{
    /**
     * @param string|ReadableStreamInterface $message
     * @param int $type
     * @return void
     */
    public function send($message, int $type = Type::TYPE_MESSAGE): void
    {
        \error_clear_last();

        foreach ($this->encoder->encode($message, $type) as $chunk) {
            @\fwrite($this->stream, $chunk);

            if ($error = \error_get_last()) {
                throw new ProtocolException($error['message']);
            }
        }
    }
}
