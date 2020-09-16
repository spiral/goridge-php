<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Protocol\Version1;

use Spiral\Goridge\Exception\ProtocolException;
use Spiral\Goridge\Protocol\Type;

final class Payload
{
    /**
     * Goridge v1 header size (17 bytes):
     *
     *  [ flag       ][ message length|LE ][ message length|BE ]
     *      |
     *      |- following size
     *      ↓
     *  [ 1 byte     ][ 8 bytes           ][ 8 bytes           ]
     *
     * @var int
     */
    public const HEADER_SIZE = 1 + 8 + 8;

    /**
     * @var string
     */
    private const ERROR_INVALID_TYPE = 'Payload type identified by #%d is not supported by the GoridgeV2 protocol';

    /**
     * @var string
     */
    private const ERROR_INVALID_FORMAT = 'Payload format identified by #%d is not supported by the Goridge v1 protocol';

    /**
     * Must be set when data is json (default value).
     *
     * @var int
     */
    public const PAYLOAD_JSON = 0;

    /**
     * Must be set when no data to be sent.
     *
     * @var int
     */
    public const PAYLOAD_EMPTY = 2;

    /**
     * Must be set when data binary data.
     *
     * @var int
     */
    public const PAYLOAD_RAW = 4;

    /**
     * Must be set when data is error string or structure.
     *
     * @var int
     */
    public const PAYLOAD_ERROR = 8;

    /**
     * Defines that associated data must be treated as control data.
     *
     * @var int
     */
    public const PAYLOAD_CONTROL = 16;

    /**
     * @param int $type
     * @return int
     */
    public static function pack(int $type): int
    {
        switch ($type) {
            case Type::TYPE_MESSAGE:
                return 0;

            case Type::TYPE_ERROR:
                return self::PAYLOAD_ERROR;

            case Type::TYPE_COMMAND:
                return self::PAYLOAD_CONTROL;

            default:
                throw new ProtocolException(\sprintf(self::ERROR_INVALID_TYPE, $type));
        }
    }

    /**
     * @param int $flags
     * @return int
     */
    public static function unpack(int $flags): int
    {
        $type = 0;

        if ($flags & self::PAYLOAD_CONTROL) {
            $type = Type::TYPE_COMMAND;
        } elseif ($flags & self::PAYLOAD_ERROR) {
            $type = Type::TYPE_ERROR;
        }

        return $type;
    }
}
