<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database\Catalog\Driver;

use Citrus\Database\Catalog\CatalogDriver;
use Citrus\Database\Catalog\ColumnDef;
use PDO;

/**
 * データベースカタログ管理 SQLite
 */
class Sqlite extends CatalogDriver
{
    /**
     * テーブルのカラム定義の取得
     *
     * @param string $table_name テーブル名
     * @return ColumnDef[] キーはカラム名
     */
    public function tableColumns(string $table_name): array
    {
        $stmt = $this->handler->query(sprintf('PRAGMA table_info (%s);',
            $this->handler->quote($table_name)
            ));
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $columns = [];
        foreach ($results as $row)
        {
            $columns[$row['name']] = ColumnDef::forDataType($row['name'], $row['type']);
        }

        return $columns;
    }



    /**
     * テーブルのカラム定義の取得
     *
     * @param string $table_name テーブル名
     * @return ColumnDef[] キーはカラム名
     */
    public function columnComments(string $table_name): array
    {
        // 基本的にSQLiteにはカラムコメントはないはずなので、コメントが空のカラム定義をそのまま返す
        return $this->tableColumns($table_name);
    }



    /**
     * テーブルのプライマリキー定義の取得
     *
     * @param string $table_name テーブル名
     * @return string[]
     */
    public function primaryKeys(string $table_name): array
    {
        $stmt = $this->handler->query(sprintf('PRAGMA table_info (%s);',
            $this->handler->quote($table_name)
        ));
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // プライマリキーデータ
        $primary_keys = [];
        foreach ($results as $row)
        {
            if (1 === $row['pk'])
            {
                $primary_keys[] = $row['name'];
            }
        }

        return $primary_keys;
    }
}
