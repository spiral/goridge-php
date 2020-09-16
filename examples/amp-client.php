<?php

use Amp\Loop;
use Spiral\Goridge\Protocol;
use Spiral\Goridge\Transport\AmpReceiver;
use Spiral\Goridge\Transport\Connector\TcpConnector;
use Spiral\Goridge\Transport\Receiver\Message;
use Spiral\Goridge\Transport\SyncStreamResponder;

require __DIR__ . '/vendor/autoload.php';

/**
 * Create a Transport Protocol
 */
$protocol = new Protocol();

/**
 * Create TCP Connection
 */
$connection = TcpConnector::create('127.0.0.1:6001');

/**
 * Initialize Async AMP IO
 */
$receiver = new AmpReceiver($connection, $protocol);
$responder = new SyncStreamResponder($connection, $protocol);

/**
 * Execute
 */
$responder->send('Hello, I`m new Goridge Client');

$receiver->receive(static function (Message $message) {
    // Just header
    echo \str_repeat('-', 10) . '[ ';
    echo (new \DateTime())->format(\DateTime::RFC3339);
    echo ' ]' . \str_repeat('-', 50) . "\n";

    // Info
    echo 'Received ' . \get_class($message) . "\n";
    echo ' > ' . $message . "\n\n";
});

Loop::run();
