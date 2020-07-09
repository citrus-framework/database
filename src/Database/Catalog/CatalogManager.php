<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database\Catalog;

use Citrus\Database\Catalog\Driver\Postgres;
use Citrus\Database\Catalog\Driver\Sqlite;
use Citrus\Database\DSN;

/**
 * データベースカタログ管理
 */
class CatalogManager
{
    /** @var CatalogDriver DBタイプ別のクラス */
    private $catalogDriver;



    /**
     * constructor.
     *
     * @param DSN $dsn
     */
    public function __construct(DSN $dsn)
    {
        // PostgreSQL
        if (true === $dsn->isPostgreSQL())
        {
            $this->catalogDriver = new Postgres($dsn);
            return;
        }
        // SQLite
        if (true === $dsn->isSQLite())
        {
            $this->catalogDriver = new Sqlite($dsn);
            return;
        }
    }



    /**
     * テーブルのカラム定義の取得
     *
     * @param string $table_name テーブル名
     * @return ColumnDef[] キーはカラム名
     */
    public function tableColumns(string $table_name): array
    {
        return $this->catalogDriver->tableColumns($table_name);
    }



    /**
     * テーブルのカラム定義の取得
     *
     * @param string $table_name テーブル名
     * @return ColumnDef[] キーはカラム名
     */
    public function columnComments(string $table_name): array
    {
        return $this->catalogDriver->columnComments($table_name);
    }



    /**
     * テーブルのプライマリキー定義の取得
     *
     * @param string $table_name テーブル名
     * @return string[]
     */
    public function primaryKeys(string $table_name): array
    {
        return $this->catalogDriver->primaryKeys($table_name);
    }
}
