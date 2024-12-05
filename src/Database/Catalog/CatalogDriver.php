<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database\Catalog;

use Citrus\Database\DSN;
use PDO;

/**
 * データベースカタログ管理用
 */
abstract class CatalogDriver
{
    /**
     * constructor.
     * @param DSN|null $dsn     DB接続情報
     * @param PDO|null $handler DBハンドラ
     */
    public function __construct(
        protected DSN|null $dsn = null,
        protected PDO|null $handler = null,
    ) {
        $this->handler = new PDO($this->dsn->toStringWithAuth());
    }

    /**
     * テーブルのカラム定義の取得
     * @param string $table_name テーブル名
     * @return ColumnDef[]
     */
    abstract public function tableColumns(string $table_name): array;

    /**
     * テーブルのカラム定義の取得
     * @param string $table_name テーブル名
     * @return array
     */
    abstract public function columnComments(string $table_name): array;

    /**
     * テーブルのプライマリキー定義の取得
     * @param string $table_name テーブル名
     * @return array
     */
    abstract public function primaryKeys(string $table_name): array;
}
