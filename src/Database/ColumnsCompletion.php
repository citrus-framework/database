<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2026, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database;

use Citrus\Variable\Dates;

/**
 * Columns の補完値
 */
class ColumnsCompletion
{
    /**
     * スキーマの補完(未設定なら DSN のスキーマ)
     * @param string|null $schema スキーマ
     * @return string|null
     */
    public static function schema(string|null $schema): string|null
    {
        return $schema ?? DSN::getInstance()->schema;
    }

    /**
     * タイムスタンプの補完(未設定なら現在日時)
     * @param string|null $timestamp タイムスタンプ
     * @return string
     */
    public static function timestamp(string|null $timestamp): string
    {
        return $timestamp ?? Dates::now()->format('Y-m-d H:i:s');
    }
}
