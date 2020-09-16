<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Protocol\Stream;

class Factory implements FactoryInterface
{
    /**
     * @param int $requiredSize
     * @param int $chunkSize
     * @return DuplexStreamInterface
     */
    public function create(int $requiredSize, int $chunkSize): DuplexStreamInterface
    {
        return new BufferStream('', $requiredSize);
    }
}
