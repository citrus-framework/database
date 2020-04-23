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
     *
     * @param PDOException $e
     * @return self
     */
    public static function pdoException(PDOException $e)
    {
        return new static($e->getMessage(), $e->getCode());
    }



    /**
     * pdo errorInfo
     *
     * @param array $errorInfo
     * @return self
     */
    public static function pdoErrorInfo(array $errorInfo)
    {
        return new static(sprintf('[%s] %s', $errorInfo[0], $errorInfo[2]), $errorInfo[1]);
    }



    /**
     * {@inheritDoc}
     *
     * @throws DatabaseException
     */
    public static function exceptionIf($expr, string $message): void
    {
        parent::exceptionIf($expr, $message);
    }



    /**
     * {@inheritDoc}
     *
     * @throws DatabaseException
     */
    public static function exceptionElse($expr, string $message): void
    {
        parent::exceptionElse($expr, $message);
    }
}
