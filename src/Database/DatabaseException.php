<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database;

use Citrus\CitrusException;
use PDOException;

/**
 * Database用例外
 */
class DatabaseException extends CitrusException
{
    /**
     * PDO exception
     * @param PDOException $e
     * @return self
     */
    public static function pdoException(PDOException $e)
    {
        return static::fromThrowable($e);
    }

    /**
     * 例外から作り直す
     *
     * 文は元の例外のまま、previous に元の例外を入れる。
     * コードは、整数ならそのまま使い、そうでなければ0にする(PDOException のコードは SQLSTATE の文字列のため)
     * @param \Throwable $e 元の例外
     * @return static
     */
    public static function fromThrowable(\Throwable $e): static
    {
        $code = $e->getCode();
        return new static($e->getMessage(), is_int($code) ? $code : 0, $e);
    }

    /**
     * pdo errorInfo
     * @param array $errorInfo
     * @return self
     */
    public static function pdoErrorInfo(array $errorInfo)
    {
        return new static(sprintf('[%s] %s', $errorInfo[0], $errorInfo[2]), $errorInfo[1]);
    }

    /**
     * {@inheritDoc}
     * @throws DatabaseException
     */
    public static function exceptionIf($expr, string $message): void
    {
        parent::exceptionIf($expr, $message);
    }

    /**
     * {@inheritDoc}
     * @throws DatabaseException
     */
    public static function exceptionElse($expr, string $message): void
    {
        parent::exceptionElse($expr, $message);
    }
}
