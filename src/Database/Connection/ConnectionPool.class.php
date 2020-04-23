<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database\Connection;

use Citrus\Database\DSN;

/**
 * データベース接続プール
 */
class ConnectionPool
{
    /** @var Connection[] */
    private static $POOLS = [];

    /** @var string|null デフォルト接続キー */
    private static $DEFAULT_POOL_KEY;



    /**
     * Connectionの生成と取得
     *
     * @param DSN|null  $dsn        DSN情報
     * @param bool|null $is_default defaultコネクション
     * @return Connection
     */
    public static function callConnection(DSN $dsn = null, bool $is_default = false): Connection
    {
        $dsn_key = $dsn->toString();
        // 生成して設定
        self::$POOLS[$dsn_key] = (self::$POOLS[$dsn_key] ?? new Connection($dsn));
        // デフォルト設定
        if (true === $is_default)
        {
            self::$DEFAULT_POOL_KEY = $dsn_key;
        }
        // 取得
        return self::$POOLS[$dsn_key];
    }



    /**
     * デフォルト設定されているコネクションを取得
     *
     * @return Connection|null
     */
    public static function callDefault(): ?Connection
    {
        // デフォルト設定がある場合は取得
        if (false === is_null(self::$DEFAULT_POOL_KEY))
        {
            return (self::$POOLS[self::$DEFAULT_POOL_KEY] ?? null);
        }
        return null;
    }
}
