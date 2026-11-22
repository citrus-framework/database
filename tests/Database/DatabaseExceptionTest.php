<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2026, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test\Database;

use Citrus\Database\DatabaseException;
use PDOException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use RuntimeException;

/**
 * データベース例外のテスト
 */
class DatabaseExceptionTest extends TestCase
{
    #[Test]
    public function 文字列のコードは0にしてpreviousを入れる(): void
    {
        // SQLSTATE のように、コードが文字列の PDOException
        $source = new PDOException('重複しています');
        (new ReflectionProperty(\Exception::class, 'code'))->setValue($source, '23505');

        $actual = DatabaseException::fromThrowable($source);

        $this->assertSame('重複しています', $actual->getMessage());
        $this->assertSame(0, $actual->getCode());
        $this->assertSame($source, $actual->getPrevious());
    }

    #[Test]
    public function 整数のコードはそのまま使う(): void
    {
        $source = new RuntimeException('失敗', 7);

        $actual = DatabaseException::fromThrowable($source);

        $this->assertSame('失敗', $actual->getMessage());
        $this->assertSame(7, $actual->getCode());
        $this->assertSame($source, $actual->getPrevious());
    }
}
