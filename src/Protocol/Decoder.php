<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Protocol;

abstract class Decoder extends Protocol implements DecoderInterface
{
    /**
     * @param \Generator $decoder
     * @param \Closure $onRead
     * @param \Closure $onResolve
     */
    public static function reduce(\Generator $decoder, \Closure $onRead, \Closure $onResolve): void
    {
        if ($decoder->valid()) {
            $length = $decoder->current();

            $decoder->send( $onRead($length));
        }

        if (! $decoder->valid()) {
            ($onResolve)($decoder->getReturn());
        }
    }
}
