<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test\Database\ResultSet;

use Citrus\Database\Connection\Connection;
use Citrus\Database\DatabaseException;
use Citrus\Database\DSN;
use Citrus\Database\ResultSet\ResultSet;
use PHPUnit\Framework\TestCase;
use Test\Sample\Business\Entity\UserEntity;
use Test\TestFile;

/**
 * 結果セットのテスト
 */
class ResultSetTest extends TestCase
{
    use TestFile;

    /** @var string 出力ディレクトリ */
    private $output_dir;

    /** @var array 設定ファイル */
    private $configures = [];



    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        // 設定ファイル
        $this->configures = require(dirname(__DIR__). '/../citrus-configure.php');
        $this->output_dir = $this->configures['default']['application']['path'];
        $this->configures['default']['database'] = [
            'type'      => 'sqlite',
            'hostname'  => $this->output_dir . '/test.sqlite',
            'options'   => [
                \PDO::ATTR_PERSISTENT => true,
            ],
        ];
    }



    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
        parent::tearDown();

        // ディレクトリがあったら削除
        $this->forceRemove($this->output_dir . '/test.sqlite');
    }



    /**
     * @test
     * @throws DatabaseException
     */
    public function 想定どおり()
    {
        /** @var DSN $dsn 設定ファイルを読み込みDSNを生成 */
        $dsn = DSN::getInstance()->loadConfigures($this->configures);

        $connection = new Connection($dsn);
        $connection->connect();
        $connection->callHandle()->exec(<<<'SQL'
CREATE TABLE users (
    `user_id` int NOT NULL PRIMARY KEY,
    `name` TEXT
);
SQL);
        $connection->callHandle()->exec('INSERT INTO users (user_id, name) VALUES (123, "hoge");');
        $statement = $connection->callHandle()->prepare('SELECT user_id, name FROM users;');
        $resultSet = new ResultSet($statement, UserEntity::class);

//        $userEntity = $resultSet->getIterator()->current();
//        var_dump($userEntity);
        foreach ($resultSet as $item)
        {
            var_dump($item);
        }
    }
}
