<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Protocol;

use Spiral\Goridge\Protocol\Stream\FactoryInterface;

interface DecoderInterface
{
    /**
     * @param FactoryInterface $factory
     * @return \Generator
     */
    public function decode(FactoryInterface $factory): \Generator;
}
