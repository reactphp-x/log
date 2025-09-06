<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use ReactphpX\Log\Log;

// Configure channels (optional, sensible defaults exist)
Log::configure([
    'default' => 'stdout',
    'channels' => [
        'single' => [
            'driver' => 'single',
            'path' => __DIR__ . '/example.log',
            'formatter' => 'line',
        ],
        'stdout' => [
            'driver' => 'stdout',
            'formatter' => 'console',
        ],
        'stderr' => [
            'driver' => 'stderr',
            'formatter' => 'console',
        ],
        'stacked' => [
            'driver' => 'stack',
            'channels' => ['stdout', 'single'],
        ],
    ],
]);

// Log to default channel (stdout)
Log::info('Hello from Log facade (stdout)');
Log::warning('Warning with context', ['user' => 'alice']);

// Log to single file channel
Log::channel('single')->error('This goes to examples/example.log');

// Log to both stdout and file via stack
Log::stack(['stdout', 'single'])->notice('This should appear in both stdout and file');


