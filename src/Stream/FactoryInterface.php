<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Stream;

interface FactoryInterface
{
    /**
     * @param int $size
     * @return StreamInterface
     */
    public function create(int $size): StreamInterface;
}
