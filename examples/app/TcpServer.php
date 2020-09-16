<?php

declare(strict_types=1);

namespace App;

use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;
use React\Socket\ConnectionInterface;
use React\Socket\ServerInterface;
use React\Socket\TcpServer as ReactTcpServer;
use Spiral\Goridge\Protocol\EncoderInterface;
use Spiral\Goridge\Protocol\Type;

class TcpServer
{
    /**
     * @var EncoderInterface
     */
    private EncoderInterface $encoder;

    /**
     * @var LoopInterface
     */
    private LoopInterface $loop;

    /**
     * @param EncoderInterface $encoder
     * @param LoopInterface|null $loop
     */
    public function __construct(EncoderInterface $encoder, LoopInterface $loop = null)
    {
        $this->encoder = $encoder;
        $this->loop = $loop ?? Factory::create();
    }

    /**
     * @param string $body
     * @param int $type
     * @return string
     */
    private function pack(string $body, int $type): string
    {
        $stream = $this->encoder->encode($body, $type);

        $buffer = '';

        foreach ($stream as $chunk) {
            $buffer .= $chunk;
        }

        return $buffer;
    }

    /**
     * @param ConnectionInterface $connection
     * @param string $message
     */
    private function log(ConnectionInterface $connection, string $message): void
    {
        $addr = $connection->getRemoteAddress();

        echo "[$addr] $message\n";

        \flush();
    }

    /**
     * @return string
     */
    private function error(): string
    {
        return 'Something Went Ok';
    }

    /**
     * @return string
     */
    private function command(): string
    {
        return 'Please enjoy the process further ♥';
    }

    /**
     * @return string
     * @throws \JsonException
     */
    private function message(): string
    {
        return \json_encode(['random_int' => \random_int(\PHP_INT_MIN, \PHP_INT_MAX)], \JSON_THROW_ON_ERROR);
    }

    /**
     * @param ConnectionInterface $connection
     */
    private function attachEcho(ConnectionInterface $connection): void
    {
        $connection->on('data', function ($data) use ($connection) {
            $this->log($connection, '<<< ' . $data);

            $connection->write($data);

            $this->log($connection, '>>> ' . $data);
        });
    }

    /**
     * @param ConnectionInterface $connection
     * @param float $interval
     * @return TimerInterface
     */
    private function attachMessageTimer(ConnectionInterface $connection, float $interval): TimerInterface
    {
        return $this->loop->addPeriodicTimer($interval, function () use ($connection) {
            $connection->write($this->pack($response = $this->message(), Type::TYPE_MESSAGE));

            $this->log($connection, '+message ' . $response);
        });
    }

    /**
     * @param ConnectionInterface $connection
     * @param float $interval
     * @return TimerInterface
     */
    private function attachErrorTimer(ConnectionInterface $connection, float $interval): TimerInterface
    {
        return $this->loop->addPeriodicTimer($interval, function () use ($connection) {
            $connection->write($this->pack($response = $this->error(), Type::TYPE_ERROR));

            $this->log($connection, '+error ' . $response);
        });
    }

    /**
     * @param ConnectionInterface $connection
     * @param float $interval
     * @return TimerInterface
     */
    private function attachCommandTimer(ConnectionInterface $connection, float $interval): TimerInterface
    {
        return $this->loop->addPeriodicTimer($interval, function () use ($connection) {
            $connection->write($this->pack($response = $this->command(), Type::TYPE_COMMAND));

            $this->log($connection, '+command ' . $response);
        });
    }

    /**
     * @param string|int $uri
     * @return ServerInterface
     */
    public function listen($uri): ServerInterface
    {
        $server = new ReactTcpServer($uri, $this->loop);

        $server->on('connection', function (ConnectionInterface $connection) {
            $this->log($connection, 'Connection Establish');

            $this->attachEcho($connection);

            $timers = [
                $this->attachMessageTimer($connection, 3),
                $this->attachErrorTimer($connection, 6),
                $this->attachCommandTimer($connection, 9),
            ];

            $connection->on('close', function () use ($timers) {
                foreach ($timers as $timer) {
                    $this->loop->cancelTimer($timer);
                }
            });
        });

        return $server;
    }

    /**
     * @return void
     */
    public function run(): void
    {
        $this->loop->run();
    }
}
