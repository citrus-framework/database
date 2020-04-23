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
 * データベースカタログ管理 PostgreSQL
 */
class Postgres extends CatalogDriver
{
    /**
     * テーブルのカラム定義の取得
     *
     * @param string $table_name テーブル名
     * @return ColumnDef[] キーはカラム名
     */
    public function tableColumns(string $table_name): array
    {
        $stmt = $this->handler->prepare(<<<SQL
SELECT 
      column_name
    , column_default
    , data_type
FROM information_schema.columns 
WHERE table_catalog = :database
  AND table_schema = :schema 
  AND table_name = :table 
ORDER BY ordinal_position
SQL
        );
        $stmt->execute([
            ':database' => $this->dsn->database,
            ':schema' => $this->dsn->schema,
            ':table' => $table_name,
        ]);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $columns = [];
        foreach ($result as $row)
        {
            $columns[] = ColumnDef::forDataType($row['column_name'], $row['data_type']);
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
        $stmt = $this->handler->prepare(<<<SQL
SELECT
      pg_stat_all_tables.relname
    , pg_attribute.attname
    , pg_description.description
FROM pg_stat_all_tables
INNER JOIN pg_description
        ON pg_description.objoid = pg_stat_all_tables.relid
       AND pg_description.objsubid <> 0
INNER JOIN pg_attribute
        ON pg_attribute.attrelid = pg_description.objoid
       AND pg_attribute.attnum = pg_description.objsubid
WHERE pg_stat_all_tables.schemaname = :schema
  AND pg_stat_all_tables.relname = :table
ORDER BY pg_description.objsubid
SQL
        );
        $stmt->execute([
            ':schema' => $this->dsn->schema,
            ':table' => $table_name,
        ]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // コメントデータ
        $comments = [];
        foreach ($results as $row)
        {
            $comments[$row['attname']] = ColumnDef::forComment($row['attname'], $row['description']);
        }

        return $comments;
    }



    /**
     * テーブルのプライマリキー定義の取得
     *
     * @param string $table_name テーブル名
     * @return string[]
     */
    public function primaryKeys(string $table_name): array
    {
        $stmt = $this->handler->prepare(<<<SQL
SELECT
      constraint_column_usage.column_name
FROM information_schema.table_constraints
INNER JOIN information_schema.constraint_column_usage
        ON constraint_column_usage.constraint_name = table_constraints.constraint_name
       AND constraint_column_usage.table_name = table_constraints.table_name
       AND constraint_column_usage.table_schema = table_constraints.table_schema
       AND constraint_column_usage.table_catalog = table_constraints.table_catalog
       AND constraint_column_usage.table_catalog = table_constraints.table_catalog
WHERE table_constraints.constraint_type = 'PRIMARY KEY'
  AND table_constraints.table_name = :table
SQL
        );
        $stmt->execute([
            ':table' => $table_name,
        ]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // プライマリキーデータ
        $primary_keys = [];
        foreach ($results as $row)
        {
            $primary_keys[] = $row['column_name'];
        }

        return $primary_keys;
    }
}
