<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database\Catalog;

/**
 * データベースカタログ管理用のカラム定義
 */
class ColumnDef
{
    /** @var string カラム名 */
    public $column_name = '';

    /** @var string データタイプ */
    public $data_type = '';

    /** @var string コメント */
    public $comment = '';



    /**
     * 生成処理(データタイプ用)
     *
     * @param string $column_name カラム名
     * @param string $data_type   データ型
     * @return self
     */
    public static function forDataType(string $column_name, string $data_type): self
    {
        $column = new self();
        $column->column_name = $column_name;
        $column->data_type = $data_type;
        return $column;
    }



    /**
     * 生成処理(コメント用)
     *
     * @param string      $column_name カラム名
     * @param string|null $comment     コメント
     * @return self
     */
    public static function forComment(string $column_name, string $comment = null): self
    {
        $column = new self();
        $column->column_name = $column_name;
        $column->comment = $comment;
        return $column;
    }
}
