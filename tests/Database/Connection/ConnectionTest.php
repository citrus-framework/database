<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test\Database\Connection;

use Citrus\Configure\ConfigureException;
use Citrus\Database\Connection\Connection;
use Citrus\Database\DatabaseException;
use Citrus\Database\DSN;
use PDO;
use PDOException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * データベース接続のテスト
 */
class ConnectionTest extends TestCase
{
    /** @var array 設定配列 */
    private $configures;



    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        // 設定配列
        $this->configures = [
            'default' => [
                'database' => [
                    'type'      => 'sqlite',
                    'hostname'  => ':memory:',
                ],
            ],
        ];
    }



    /**
     * @test
     * @throws ConfigureException
     * @throws DatabaseException
     * @doesNotPerformAssertions
     */
    public function 接続処理ができる()
    {
        // DSN生成
        $dsn = DSN::getInstance()->loadConfigures($this->configures);

        // 接続
        $con = new Connection($dsn);
        $con->connect();
    }

    #[Test]
    public function トランザクション内のPDO例外はDatabaseExceptionになる(): void
    {
        $handle = new PDO('sqlite::memory:');
        $con = new Connection(null, $handle);

        try
        {
            // 存在しないテーブルへの SELECT(コードは SQLSTATE の文字列になる)
            $con->transaction(function () use ($handle)
            {
                $handle->exec('SELECT * FROM table_not_exists');
            });
            $this->fail('例外が投げられていません。');
        }
        catch (DatabaseException $e)
        {
            $this->assertSame(0, $e->getCode());
            $this->assertInstanceOf(PDOException::class, $e->getPrevious());
        }
        // ロールバックされている
        $this->assertFalse($handle->inTransaction());
    }

    #[Test]
    public function ロールバックが失敗しても元の例外を作り直して投げる(): void
    {
        $handle = new PDO('sqlite::memory:');
        $con = new Connection(null, $handle);

        try
        {
            $con->transaction(function () use ($handle)
            {
                // トランザクションを先に終わらせて、ロールバックを失敗させる
                $handle->exec('ROLLBACK');
                throw new RuntimeException('元の例外');
            });
            $this->fail('例外が投げられていません。');
        }
        catch (DatabaseException $e)
        {
            $this->assertSame('元の例外', $e->getMessage());
            $this->assertInstanceOf(RuntimeException::class, $e->getPrevious());
        }
    }
}
