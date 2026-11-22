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
     * @param string $table_name テーブル名
     * @return ColumnDef[] キーはカラム名
     */
    public function tableColumns(string $table_name): array
    {
        $stmt = $this->handler->prepare(
            <<<SQL
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
     * @param string $table_name テーブル名
     * @return ColumnDef[] キーはカラム名
     */
    public function columnComments(string $table_name): array
    {
        $stmt = $this->handler->prepare(
            <<<SQL
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
     * @param string $table_name テーブル名
     * @return string[]
     */
    public function primaryKeys(string $table_name): array
    {
        $stmt = $this->handler->prepare(
            <<<SQL
SELECT
      pg_attribute.attname AS column_name
FROM pg_index
INNER JOIN pg_class
        ON pg_class.oid = pg_index.indrelid
INNER JOIN pg_namespace
        ON pg_namespace.oid = pg_class.relnamespace
INNER JOIN pg_attribute
        ON pg_attribute.attrelid = pg_class.oid
       AND pg_attribute.attnum = ANY(pg_index.indkey)
WHERE pg_index.indisprimary
  AND array_position(CAST(pg_index.indkey AS int2[]), pg_attribute.attnum)
      - array_lower(CAST(pg_index.indkey AS int2[]), 1) < pg_index.indnkeyatts
  AND pg_namespace.nspname = :schema
  AND pg_class.relname = :table
ORDER BY array_position(CAST(pg_index.indkey AS int2[]), pg_attribute.attnum)
SQL
        );
        // information_schema はテーブルの持ち主にしか制約の列を見せないので、pg_catalog から読む(INCLUDE の列は除く)
        $stmt->execute([
            ':schema' => $this->dsn->schema,
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
