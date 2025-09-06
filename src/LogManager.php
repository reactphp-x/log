<?php declare(strict_types=1);

namespace ReactphpX\Log;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use React\Stream\WritableResourceStream;

final class LogManager
{
    /**
     * @var array<string, mixed>
     */
    private array $config;

    /**
     * @var array<string, LoggerInterface>
     */
    private array $channels = [];

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $this->mergeDefaultConfig($config);
    }

    /**
     * @param array<string, mixed> $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $this->mergeDefaultConfig($config);
        $this->channels = [];
    }

    public function channel(?string $name = null): LoggerInterface
    {
        $channelName = $name ?? ($this->config['default'] ?? 'stdout');

        if (isset($this->channels[$channelName])) {
            return $this->channels[$channelName];
        }

        return $this->channels[$channelName] = $this->resolveChannel($channelName);
    }

    /**
     * Create a stack logger composed of the given channels.
     *
     * @param string[] $channels
     */
    public function stack(array $channels, ?string $name = 'stack'): LoggerInterface
    {
        $logger = new Logger($name ?? 'stack');

        foreach ($channels as $childChannelName) {
            $childLogger = $this->channel($childChannelName);

            if ($childLogger instanceof Logger) {
                foreach ($childLogger->getHandlers() as $handler) {
                    $logger->pushHandler($handler);
                }
            }
        }

        return $logger;
    }

    public function __call(string $method, array $parameters): mixed
    {
        return $this->channel()->{$method}(...$parameters);
    }

    private function resolveChannel(string $name): LoggerInterface
    {
        /** @var array<string, mixed>|null $config */
        $config = $this->config['channels'][$name] ?? null;

        if ($config === null) {
            throw new \InvalidArgumentException(\sprintf('Log channel "%s" is not defined.', $name));
        }

        $driver = (string) ($config['driver'] ?? 'stdout');

        return match ($driver) {
            'stdout' => $this->createStreamLogger($name, new WritableResourceStream(\STDOUT), $config),
            'stderr' => $this->createStreamLogger($name, new WritableResourceStream(\STDERR), $config),
            'single' => $this->createFileLogger($name, (string) ($config['path'] ?? (\sys_get_temp_dir() . '/reactphp.log')), $config),
            'stack' => $this->createStackLogger($name, (array) ($config['channels'] ?? [])),
            default => throw new \InvalidArgumentException(\sprintf('Unsupported log driver "%s" for channel "%s".', $driver, $name)),
        };
    }

    /**
     * @param array<string, mixed> $config
     */
    private function createStreamLogger(string $name, \React\Stream\WritableStreamInterface $sink, array $config): LoggerInterface
    {
        $logger = new Logger($name);
        $handler = new StreamHandler($sink);
        $this->applyFormatter($handler, (string) ($config['formatter'] ?? 'console'));
        $logger->pushHandler($handler);

        return $logger;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function createFileLogger(string $name, string $path, array $config): LoggerInterface
    {
        $logger = new Logger($name);
        $handler = new StreamHandler(new FileWriteStream($path));
        $this->applyFormatter($handler, (string) ($config['formatter'] ?? 'line'));
        $logger->pushHandler($handler);

        return $logger;
    }

    /**
     * @param string[] $channels
     */
    private function createStackLogger(string $name, array $channels): LoggerInterface
    {
        return $this->stack($channels, $name);
    }

    private function applyFormatter(StreamHandler $handler, string $formatter): void
    {
        if ($formatter === 'console') {
            $handler->setFormatter(new ConsoleFormatter());
            return;
        }

        $handler->setFormatter(new LineFormatter(ConsoleFormatter::DEFAULT_FORMAT));
    }

    /**
     * @param array<string, mixed> $config
     * @return array<string, mixed>
     */
    private function mergeDefaultConfig(array $config): array
    {
        $default = [
            'default' => 'stdout',
            'channels' => [
                'stdout' => [
                    'driver' => 'stdout',
                    'formatter' => 'console',
                ],
                'stderr' => [
                    'driver' => 'stderr',
                    'formatter' => 'console',
                ],
                'single' => [
                    'driver' => 'single',
                    'path' => \sys_get_temp_dir() . '/reactphp.log',
                    'formatter' => 'line',
                ],
            ],
        ];

        /** @var array<string, mixed> */
        $merged = \array_replace_recursive($default, $config);

        return $merged;
    }
}


