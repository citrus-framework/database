<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database\DSN;

/**
 * DSN PostgreSQL
 */
trait Postgres
{
    /** @var string[] PostgreSQL */
    public static $TYPES_POSTGRESQL = [
        'pgsql',
        'postgres',
        'postgresql',
    ];



    /**
     * データベースタイプがPostgreSQLかどうか
     *
     * @return bool
     */
    public function isPostgreSQL()
    {
        return in_array($this->type, self::$TYPES_POSTGRESQL, true);
    }
}
