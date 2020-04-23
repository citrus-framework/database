<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */


namespace Test\Database;

use Citrus\Configure\ConfigureException;
use Citrus\Database\DSN;
use PHPUnit\Framework\TestCase;

/**
 * DSN定義のテスト
 */
class DSNTest extends TestCase
{
    /** @var array 設定ファイル */
    private $configures = [];



    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        // 設定ファイル
        $this->configures = require(dirname(__DIR__). '/citrus-configure.php');
    }



    /**
     * @test
     * @throws ConfigureException
     */
    public function 設定ファイルを読み込める()
    {
        // 設定ファイル読み込み
        /** @var  $dsn */
        $dsn = DSN::getInstance()->loadConfigures($this->configures);

        // 初期ファイル内容
        $database = $this->configures['default']['database'];

        // 検証
        $this->assertSame($database['type'], $dsn->type);
        $this->assertSame($database['hostname'], $dsn->hostname);
        $this->assertSame($database['port'], $dsn->port);
        $this->assertSame($database['database'], $dsn->database);
        $this->assertSame($database['schema'], $dsn->schema);
        $this->assertSame($database['username'], $dsn->username);
        $this->assertSame($database['password'], $dsn->password);
    }



    /**
     * @test
     * @throws ConfigureException
     */
    public function 接続文字列生成ができる()
    {
        // 設定ファイル読み込み
        /** @var  $dsn */
        $dsn = DSN::getInstance()->loadConfigures($this->configures);

        // 初期ファイル内容
        $database = $this->configures['default']['database'];

        // 検証
        $this->assertSame(sprintf('pgsql:host=%s;port=%s;dbname=%s', $database['hostname'], $database['port'], $database['database']), $dsn->toString());
        $this->assertSame(sprintf('pgsql:host=%s;port=%s;dbname=%s;user=%s;password=%s', $database['hostname'], $database['port'], $database['database'], $database['username'], $database['password']), $dsn->toStringWithAuth());
    }
}
