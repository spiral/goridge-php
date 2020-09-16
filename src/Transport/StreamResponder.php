<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Transport;

use Spiral\Goridge\Protocol\EncoderInterface;
use Spiral\Goridge\Protocol\Stream\Stream;

abstract class StreamResponder extends Responder
{
    /**
     * @var resource
     */
    protected $stream;

    /**
     * @param resource $stream
     * @param EncoderInterface $encoder
     */
    public function __construct($stream, EncoderInterface $encoder)
    {
        parent::__construct($encoder);

        Stream::assertIsValid($stream);
        Stream::assertIsWritable($stream);

        $this->stream = $stream;
    }
}
