<?php

use App\TcpServer;
use Spiral\Goridge\Protocol;
use Spiral\Goridge\Protocol\Version;

require __DIR__ . '/vendor/autoload.php';

/**
 * Create a Transport Protocol
 */
$protocol = new Protocol(Version::VERSION_1);

/**
 * Create TCP Server Emulator
 */
$server = new TcpServer($protocol);

/**
 * Listen 6001 Port
 */
$server->listen(6001);

/**
 * Run Server
 */
$server->run();
