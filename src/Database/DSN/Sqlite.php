<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database\DSN;

/**
 * DSN SQLite
 */
trait Sqlite
{
    /** @var string[] SQLite */
    public static $TYPES_SQLITE = [
        'sqlite',
    ];



    /**
     * データベースタイプがSQLiteかどうか
     *
     * @return bool
     */
    public function isSQLite()
    {
        return in_array($this->type, self::$TYPES_SQLITE, true);
    }
}
