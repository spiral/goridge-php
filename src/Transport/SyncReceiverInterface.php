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

interface SyncReceiverInterface extends ReceiverInterface
{
    /**
     * @return MessageInterface
     */
    public function waitForResponse(): MessageInterface;
}
