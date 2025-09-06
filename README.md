
# wpjscc/reactphp-log

reactphp is a collection of event-driven libraries for PHP designed with fibers and concurrency in mind.
`reactphp-x/log` provides a non-blocking stream handler for `monolog/monolog`.

[![Release](https://img.shields.io/github/release/amphp/log.svg?style=flat-square)](https://github.com/amphp/log/releases)
![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)

## Installation

This package can be installed as a [Composer](https://getcomposer.org/) dependency.

```bash
composer require reactphp-x/log -vvv
```

## Usage

```php
<?php

use React\Stream;
use ReactphpX\Log\ConsoleFormatter;
use ReactphpX\Log\StreamHandler;
use ReactphpX\Log\FileWriteStream;
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

## Facade (Laravel-like) Usage

```php
<?php

use ReactphpX\Log\Log;

require dirname(__DIR__) . '/vendor/autoload.php';

// Minimal usage (defaults to stdout)
Log::info('Hello from Log facade');
Log::error('Something went wrong', ['code' => 123]);

// Optional: configure channels
Log::configure([
    'default' => 'stdout',
    'channels' => [
        'single' => ['driver' => 'single', 'path' => __DIR__ . '/example.log', 'formatter' => 'line'],
        'stdout' => ['driver' => 'stdout', 'formatter' => 'console'],
        'stderr' => ['driver' => 'stderr', 'formatter' => 'console'],
        'stacked' => ['driver' => 'stack', 'channels' => ['stdout', 'single']],
    ],
]);

Log::channel('single')->warning('This goes to a file');
Log::stack(['stdout', 'single'])->notice('This appears in both stdout and file');
```

### Run the example

```bash
php examples/log-facade.php
```
