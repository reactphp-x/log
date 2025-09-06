<?php declare(strict_types=1);

namespace ReactphpX\Log;

use Psr\Log\LoggerInterface;

final class Log
{
    private static ?LogManager $manager = null;

    /**
     * @param array<string, mixed> $config
     */
    public static function configure(array $config): void
    {
        if (self::$manager === null) {
            self::$manager = new LogManager($config);
            return;
        }

        self::$manager->setConfig($config);
    }

    public static function getLogger(): LoggerInterface
    {
        return self::manager()->channel();
    }

    public static function channel(?string $name = null): LoggerInterface
    {
        return self::manager()->channel($name);
    }

    /**
     * @param string[] $channels
     */
    public static function stack(array $channels, ?string $name = 'stack'): LoggerInterface
    {
        return self::manager()->stack($channels, $name);
    }

    public static function emergency(string $message, array $context = []): void { self::getLogger()->emergency($message, $context); }
    public static function alert(string $message, array $context = []): void { self::getLogger()->alert($message, $context); }
    public static function critical(string $message, array $context = []): void { self::getLogger()->critical($message, $context); }
    public static function error(string $message, array $context = []): void { self::getLogger()->error($message, $context); }
    public static function warning(string $message, array $context = []): void { self::getLogger()->warning($message, $context); }
    public static function notice(string $message, array $context = []): void { self::getLogger()->notice($message, $context); }
    public static function info(string $message, array $context = []): void { self::getLogger()->info($message, $context); }
    public static function debug(string $message, array $context = []): void { self::getLogger()->debug($message, $context); }

    private static function manager(): LogManager
    {
        if (self::$manager === null) {
            self::$manager = new LogManager();
        }
        return self::$manager;
    }
}


