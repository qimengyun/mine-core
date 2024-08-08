<?php

declare(strict_types=1);

use Hyperf\Context\ApplicationContext;
use Hyperf\Context\Context;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Redis\Redis;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

if (!function_exists('container')) {
    /**
     * 获取容器实例.
     */
    function container(): ContainerInterface
    {
        return ApplicationContext::getContainer();
    }
}

if (!function_exists('redis')) {
    /**
     * 获取Redis实例.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function redis(): Redis
    {
        return container()->get(Redis::class);
    }
}
if (!function_exists('config')) {
    /**
     * 获取config实例.
     */
    function config($key, $default = null): mixed
    {
        return Hyperf\Config\config($key, $default);
    }
}
if (!function_exists('rpc')) {
    function rpc($interface)
    {
        return container()->get($interface);
    }
}

if (!function_exists('console')) {
    /**
     * 获取控制台输出实例.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function console(): StdoutLoggerInterface
    {
        return container()->get(StdoutLoggerInterface::class);
    }
}

if (!function_exists('logger')) {
    /**
     * 获取日志实例.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function logger(string $name = 'Log'): LoggerInterface
    {
        return container()->get(LoggerFactory::class)->get($name);
    }
}

if (!function_exists('format_size')) {
    /**
     * 格式化大小.
     */
    function format_size(int $size): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $index = 0;
        for ($i = 0; $size >= 1024 && $i < 5; ++$i) {
            $size /= 1024;
            $index = $i;
        }
        return round($size, 2).$units[$index];
    }
}

if (!function_exists('lang')) {
    /**
     * 获取当前语言
     */
    function lang(): string
    {
        $acceptLanguage = container()
            ->get(RequestInterface::class)
            ->getHeaderLine('accept-language');
        return str_replace('-', '_', !empty($acceptLanguage) ? explode(',', $acceptLanguage)[0] : 'zh_CN');
    }
}


if (!function_exists('context_set')) {
    /**
     * 设置上下文数据.
     * @param  mixed  $data
     */
    function context_set(string $key, $data): bool
    {
        return (bool)Context::set($key, $data);
    }
}

if (!function_exists('context_get')) {
    /**
     * 获取上下文数据.
     * @return mixed
     */
    function context_get(string $key)
    {
        return Context::get($key);
    }
}

if (!function_exists('uuid')) {
    /**
     * 生成UUID.
     */
    function uuid(): string
    {
        return Uuid::uuid4()->toString();
    }
}

if (!function_exists('event')) {
    /**
     * 事件调度快捷方法.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function event(object $dispatch): object
    {
        return container()->get(EventDispatcherInterface::class)->dispatch($dispatch);
    }
}

if (!function_exists('blank')) {
    /**
     * 判断给定的值是否为空.
     */
    function blank(mixed $value): bool
    {
        if (is_null($value)) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        if (is_numeric($value) || is_bool($value)) {
            return false;
        }

        if ($value instanceof Countable) {
            return count($value) === 0;
        }

        return empty($value);
    }
}

if (!function_exists('filled')) {
    /**
     * 判断给定的值是否不为空.
     */
    function filled(mixed $value): bool
    {
        return !blank($value);
    }
}
if (!function_exists('request_only')) {
    //过滤值
    function request_only(array $data, array $default = null): array
    {
        $keys = array_keys($default);
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = data_get($data, $key, $default[$key] ?? '');
        }

        return $result;
    }
}

if (!function_exists('data_get')) {
    //获取值
    function data_get($data, $key, $default)
    {
        return $data[$key] ?? $default;
    }
}