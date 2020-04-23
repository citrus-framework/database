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
use Citrus\Database\Connection\ConnectionPool;
use Citrus\Database\DatabaseException;
use Citrus\Database\DSN;
use PHPUnit\Framework\TestCase;
use Test\TestFile;

/**
 * データベース接続プールのテスト
 */
class ConnectionPoolTest extends TestCase
{
    use TestFile;

    /** @var string 出力ディレクトリ */
    private $output_dir;



    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        // 出力ディレクトリ
        $this->output_dir = __DIR__ . '/Integration/temp';
    }



    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
        parent::tearDown();

        // ディレクトリがあったら削除
        $this->forceRemove($this->output_dir);
    }



    /**
     * @test
     * @throws ConfigureException
     * @throws DatabaseException
     * @doesNotPerformAssertions
     */
    public function コネクションは使い回される()
    {
        // 接続1
        $connection1 = ConnectionPool::callConnection(DSN::getInstance()->loadConfigures([
            'database' => [
                'type' => 'sqlite',
                'hostname'  => 'test1.sqlite',
            ],
        ]));
        // 接続2
        $connection2 = ConnectionPool::callConnection(DSN::getInstance()->loadConfigures([
            'database' => [
                'type' => 'sqlite',
                'hostname'  => 'test2.sqlite',
            ],
        ]), true);
        // 接続3
        $connection3 = ConnectionPool::callConnection(DSN::getInstance()->loadConfigures([
            'database' => [
                'type' => 'sqlite',
                'hostname'  => 'test1.sqlite',
            ],
        ]));

        // 接続1と接続2は別物である
        $this->assertNotSame($connection1, $connection2);

        // 接続1と接続3は同じ物である
        $this->assertSame($connection1, $connection3);

        // デフォルト接続は接続2
        $this->assertSame($connection2, ConnectionPool::callDefault());
    }
}
