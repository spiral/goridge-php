<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Stream;

class Factory implements FactoryInterface
{
    /**
     * The size of the buffer available for storage in memory.
     *
     * In the case that the required volume for the stream exceeds the specified
     * size, then instead of storing the stream in memory, you will need to
     * create a temporary file in the file system.
     *
     * @var int|null
     */
    private ?int $buffer;

    /**
     * @param int|null $buffer
     */
    public function __construct(int $buffer = null)
    {
        $this->buffer = $buffer;
    }

    /**
     * @return int
     */
    protected function getBufferSize(): int
    {
        return $this->buffer ?? $this->getApproximateBufferSize();
    }

    /**
     * Returns the approximate amount of RAM available for allocation.
     *
     * @return int
     */
    private function getApproximateBufferSize(): int
    {
        $available = $this->getMemoryLimit() - \memory_get_usage();

        return ($available / 2) >> 0;
    }

    /**
     * Returns the allowed amount of RAM for the script in bytes.
     *
     * @return int
     */
    private function getMemoryLimit(): int
    {
        $limit = \ini_get('memory_limit');

        $bytes  = (int)\substr($limit, 0, -1);

        switch (\strtolower(\substr($limit, -1))) {
            case 'k':
                return $bytes * 1000;

            case 'm':
                return $bytes * 1000 * 1000;

            case 'g':
                return $bytes * 1000 * 1000 * 1000;

            default:
                return $bytes;
        }
    }

    /**
     * @param int $size
     * @return StreamInterface
     */
    public function create(int $size): StreamInterface
    {
        if ($size > $this->getBufferSize()) {
            return new TempFileStream();
        }

        return new BufferStream('');
    }
}
