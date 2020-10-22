<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Stream;

class TempFileStream extends ResourceStream
{
    /**
     * @var string
     */
    private string $temp;

    /**
     * TempFileStream constructor.
     */
    public function __construct()
    {
        $temp = self::open($this->temp = $this->createTempFile(), 'ab+');

        parent::__construct($temp, \filesize($this->temp));
    }

    /**
     * @return string
     */
    protected function createTempFile(): string
    {
        $temp = \tempnam(\sys_get_temp_dir(), 'rr');

        if (! \is_file($temp)) {
            \file_put_contents($temp, '');
        }

        return $temp;
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        parent::__destruct();

        @\unlink($this->temp);
    }
}
