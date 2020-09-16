
PHP Goridge
================================================================================

See also [https://github.com/spiral/roadrunner](https://github.com/spiral/roadrunner) - High-performance PHP application 
server, load-balancer and process manager written in Golang.

Installation
------------

```
$ composer require spiral/goridge
```

Usage
-----

### Simple Example

```php
use Spiral\Goridge\Protocol;
use Spiral\Goridge\Transport\Connector\TcpConnector;
use Spiral\Goridge\Transport\SyncStreamReceiver;
use Spiral\Goridge\Transport\SyncStreamResponder;

$protocol = new Protocol();
$connection = TcpConnector::create('127.0.0.1:6001');

$receiver = new SyncStreamReceiver($connection, $protocol);
$responder = new SyncStreamResponder($connection, $protocol);

while ($response = $receiver->waitForResponse()) {
    echo $response;

    $responder->send('Thanks server, I got this message ♥');
}
```

### Protocol

This package supports multiple protocol versions to ensure compatibility 
with earlier versions of Goridge (and RoadRunner).

```php
use Spiral\Goridge\Protocol;
use Spiral\Goridge\Protocol\Version;

// Goridge v1 (RoadRunner 1.x)
$protocolV1 = new Protocol(Version::VERSION_1);

// Goridge v2 (RoadRunner 2.x)
$protocolV2 = new Protocol(Version::VERSION_2);
```

### Connection

Now you need to create a connection to the Goridge. To do this, you can use the 
following connectors:

### TCP Connection

Provides duplex TCP socket connection to Goridge server implementation.

```php
use \Spiral\Goridge\Transport\Connector\TcpConnector;

$connection = TcpConnector::create('127.0.0.1:6001');
```

### Unix Connection

Provides duplex Unix socket connection to Goridge server implementation.

```php
use Spiral\Goridge\Transport\Connector\UnixConnector;

$connection = UnixConnector::create('/path/to/socket.sock');
```

### Pipe Connection

Provides readable and writable pipe connections to Goridge server implementation.

```php
use Spiral\Goridge\Transport\Connector\StdInConnector;
use Spiral\Goridge\Transport\Connector\StdOutConnector;

[$read, $write] = [StdInConnector::create(), StdOutConnector::create()];
```

### Receiver

The protocol supports both synchronous and asynchronous work with incoming 
messages.

#### Sync Receiver

To work without any dependencies, you can use the synchronous operation mode.
To do this, do not forget to start a loop (like `while (true)`) for reading 
from the incoming stream.

```php
use Spiral\Goridge\Transport\SyncStreamReceiver;
use Spiral\Goridge\Transport\Receiver\MessageInterface;

$connection = ...; // Stream connection instance
$protocol   = ...; // Goridge protocol instance

$receiver = new SyncStreamReceiver($connection, $protocol);

while ($response = $receiver->waitForResponse()) {
    /** @var MessageInterface $response */
    echo $response;
}
```

Alternatively, you can use the pseudo-asynchronous approach with blocking 
via `while (true)` expression.

```php
use Spiral\Goridge\Transport\SyncStreamReceiver;
use Spiral\Goridge\Transport\Receiver\MessageInterface;

$connection = ...; // Stream connection instance
$protocol   = ...; // Goridge protocol instance

$receiver = new SyncStreamReceiver($connection, $protocol);

$receiver->receive(function (MessageInterface $response) {
    echo $response;
});

while (true) {
    $receiver->waitForResponse();
}

// or "while($receiver->waitForResponse()) { ... }"
// it does not matter.
```

#### Async ReactPHP Receiver

> ReactPHP is a low-level library for event-driven programming in PHP.
> See also https://reactphp.org

To work with a ReactPHP, you must have at least a `react/event-loop` (^1.0) 
installed. To do this, use the following command.

```bash
$ composer require react/event-loop
```

To work with ReactPHP event loop, use the `$receiver->receive()` method and 
run this event loop.

```php
use React\EventLoop\Factory;
use Spiral\Goridge\Transport\ReactReceiver;
use Spiral\Goridge\Transport\Receiver\MessageInterface;

$connection = ...; // Stream connection instance
$protocol   = ...; // Goridge protocol instance

$loop = Factory::create();

$receiver = new ReactReceiver($loop, $connection, $protocol);

$receiver->receive(function (MessageInterface $response) {
    echo $response;
});

$loop->run();
```

#### Async Amp Receiver

> Amp is an event-driven concurrency framework for PHP providing primitives to 
> manage cooperative multitasking building upon an event loop, and promises.
> See also https://amphp.org

To work with an Amp, you must have at least a `amphp/amp` (^2.0) 
installed. To do this, use the following command.

```bash
$ composer require amphp/amp
```

To work with Amp event loop, use the `$receiver->receive()` method and 
run this event loop.

```php
use Amp\Loop;
use Spiral\Goridge\Transport\AmpReceiver;
use Spiral\Goridge\Transport\Receiver\MessageInterface;

$connection = ...; // Stream connection instance
$protocol   = ...; // Goridge protocol instance

$receiver = new AmpReceiver($connection, $protocol);

$receiver->receive(function (MessageInterface $response) {
    echo $response;
});

Loop::run();
```

License
-------

The MIT License (MIT). Please see [`LICENSE`](./LICENSE) for more information.
