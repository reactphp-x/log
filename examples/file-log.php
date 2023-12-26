<?php declare(strict_types=1);

use Reactphp\Framework\Log\FileWriteStream;
use Reactphp\Framework\Log\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger;

require dirname(__DIR__) . '/vendor/autoload.php';

// This example requires amphp/file to be installed.

$file = new FileWriteStream(__DIR__ . '/example.log');

$handler = new StreamHandler($file);
$handler->setFormatter(new LineFormatter());

$logger = new Logger('hello-world');
$logger->pushHandler($handler);

$logger->debug("Hello, world!");
$logger->info("Hello, world!");
$logger->notice("Hello, world!");
$logger->warning("Hello, world!");
$logger->error("Hello, world!");
$logger->critical("Hello, world!");
$logger->alert("Hello, world!");
$logger->emergency("Hello, world!");
