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
use PHPUnit\Framework\TestCase;

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
}
