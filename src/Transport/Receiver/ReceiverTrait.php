<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Transport\Receiver;

use Spiral\Goridge\Exception\TransportException;
use Spiral\Goridge\Protocol\Stream\ReadableStreamInterface;
use Spiral\Goridge\Protocol\Type;
use Spiral\Goridge\Transport\ReceiverInterface;

/**
 * @mixin ReceiverInterface
 */
trait ReceiverTrait
{
    /**
     * @var \Closure[]
     */
    private array $listeners = [];

    /**
     * @param ReadableStreamInterface $message
     * @param int $type
     * @return MessageInterface
     */
    protected function toMessage(ReadableStreamInterface $message, int $type): MessageInterface
    {
        switch ($type) {
            case Type::TYPE_MESSAGE:
                return new Message($message);

            case Type::TYPE_ERROR:
                return new Error($message);

            case Type::TYPE_COMMAND:
                return new Command($message);

            default:
                throw new TransportException('Invalid received message type');
        }
    }

    /**
     * @param MessageInterface $message
     */
    protected function emit(MessageInterface $message): void
    {
        foreach ($this->listeners as $closure) {
            $closure($message);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function receive(\Closure $onMessage): void
    {
        $this->listeners[] = $onMessage;
    }
}
