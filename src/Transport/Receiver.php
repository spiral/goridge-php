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
use Spiral\Goridge\Stream\Factory;
use Spiral\Goridge\Stream\FactoryInterface;
use Spiral\Goridge\Transport\Receiver\ReceiverTrait;

abstract class Receiver implements ReceiverInterface
{
    use ReceiverTrait;

    /**
     * @var DecoderInterface
     */
    protected DecoderInterface $decoder;

    /**
     * @var FactoryInterface
     */
    protected FactoryInterface $factory;

    /**
     * @param DecoderInterface $decoder
     * @param FactoryInterface|null $factory
     */
    public function __construct(DecoderInterface $decoder, FactoryInterface $factory = null)
    {
        $this->decoder = $decoder;
        $this->factory = $factory ?? new Factory();
    }
}
