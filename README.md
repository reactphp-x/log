
# wpjscc/reactphp-log

reactphp is a collection of event-driven libraries for PHP designed with fibers and concurrency in mind.
`wpjscc/reactphp-log` provides a non-blocking stream handler for `monolog/monolog`.

[![Release](https://img.shields.io/github/release/amphp/log.svg?style=flat-square)](https://github.com/amphp/log/releases)
![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)

## Installation

This package can be installed as a [Composer](https://getcomposer.org/) dependency.

```bash
composer require wpjscc/reactphp-log -vvv
```

## Usage

```php
<?php

use React\Stream;
use Wpjscc\Log\ConsoleFormatter;
use Wpjscc\Log\StreamHandler;
use Wpjscc\Log\FileWriteStream;
use Monolog\Logger;

require dirname(__DIR__) . '/vendor/autoload.php';

// $handler = new StreamHandler(new FileWriteStream(__DIR__ . '/example.log'));

// Here we'll log to the standard output stream of the current process:
$handler = new StreamHandler(new Stream\WritableResourceStream(STDOUT));
$handler->setFormatter(new ConsoleFormatter);

$logger = new Logger('main');
$logger->pushHandler($handler);

$logger->debug("Hello, world!");
$logger->info("Hello, world!");
$logger->notice("Hello, world!");
$logger->error("Hello, world!");
$logger->alert("Hello, world!");
```
