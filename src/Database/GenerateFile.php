<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2026, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database;

/**
 * 生成したクラスのファイルへの書き込み
 */
class GenerateFile
{
    /**
     * 上書きして書き込む
     * @param string $path   ファイルパス
     * @param string $source 書き込む文字列
     */
    public static function overwrite(string $path, string $source): void
    {
        self::write($path, $source);
    }

    /**
     * ファイルがなければ書き込む
     * @param string $path   ファイルパス
     * @param string $source 書き込む文字列
     * @return bool true:書き込んだ、false:ファイルがすでにあるので書き込まなかった
     */
    public static function createIfAbsent(string $path, string $source): bool
    {
        if (file_exists($path) === true)
        {
            return false;
        }

        self::write($path, $source);
        return true;
    }

    /**
     * 書き込む(書き込めなければ例外)
     * @param string $path   ファイルパス
     * @param string $source 書き込む文字列
     * @throws DatabaseException
     */
    private static function write(string $path, string $source): void
    {
        DatabaseException::exceptionIf(
            file_put_contents($path, $source) === false,
            sprintf('ファイルに書き込めませんでした => %s', $path),
        );
    }
}
