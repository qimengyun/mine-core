<?php

namespace Mine\Enum;

enum BusinessEnum
{
    //用户端
    const CLIENT = 'Client';
    //商户端
    const MERCHANT = 'Merchant';
    //平台
    const PLATFORM = 'Platform';

    public static function getName($name): string
    {
        return match ($name) {
            self::CLIENT => 'client',
            self::MERCHANT => 'merchant',
            self::PLATFORM => 'platform',
        };
    }
}
