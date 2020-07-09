<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database;

use Citrus\Database\Connection\Connection;
use Citrus\Database\Connection\ConnectionPool;
use Citrus\Database\ResultSet\ResultSet;
use PDOStatement;

/**
 * SQL実行クライアント
 */
class Executor
{
    /** @var Connection */
    protected $connection;



    /**
     * constructor.
     *
     * @param Connection|null $connection  接続情報
     * @throws DatabaseException
     */
    public function __construct(Connection $connection = null)
    {
        // なければPOOLから取得
        $this->connection = ($connection ?: ConnectionPool::callDefault());
        // 接続もしてしまう
        $this->connection->connect();
    }



    /**
     * SELECT
     *
     * @param QueryPack $queryPack
     * @return ResultSet
     * @throws DatabaseException
     */
    public function selectQuery(QueryPack $queryPack): ResultSet
    {
        // プリペアとパラメータ設定
        $statement = $this->prepareAndBind($queryPack);

        return new ResultSet($statement, $queryPack->callResultClass());
    }



    /**
     * INSERT
     *
     * @param QueryPack $queryPack
     * @return int
     * @throws DatabaseException
     */
    public function insertQuery(QueryPack $queryPack): int
    {
        // プリペアとパラメータ設定
        $statement = $this->prepareAndBind($queryPack);

        // 実行
        $statement->execute();

        return $statement->rowCount();
    }



    /**
     * UPDATE
     *
     * @param QueryPack $queryPack
     * @return int
     * @throws DatabaseException
     */
    public function updateQuery(QueryPack $queryPack): int
    {
        // プリペアとパラメータ設定
        $statement = $this->prepareAndBind($queryPack);

        // 実行
        $statement->execute();

        return $statement->rowCount();
    }



    /**
     * DELETE
     *
     * @param QueryPack $queryPack
     * @return int
     * @throws DatabaseException
     */
    public function deleteQuery(QueryPack $queryPack): int
    {
        // 削除全実行はフレームワークとして許容しない(全実行する場合は条件を明示的につける ex.)WHERE 1=1)
        DatabaseException::exceptionIf(
            0 === count($queryPack->callParameters()),
            '削除条件が足りません、削除要求をキャンセルしました。'
        );

        // プリペアとパラメータ設定
        $statement = $this->prepareAndBind($queryPack);

        // 実行
        $statement->execute();

        return $statement->rowCount();
    }



    /**
     * プリペアとパラメータ設定
     *
     * @param QueryPack $queryPack
     * @return PDOStatement
     * @throws DatabaseException
     */
    protected function prepareAndBind(QueryPack $queryPack): PDOStatement
    {
        // ハンドル
        $handle = $this->connection->callHandle();

        // プリペア実行
        $statement = $handle->prepare($queryPack->callQuery());
        if (false === $statement)
        {
            throw DatabaseException::pdoErrorInfo($handle->errorInfo());
        }

        // パラメータ設定
        foreach ($queryPack->callParameters() as $ky => $vl)
        {
            $statement->bindValue($ky, $vl);
        }

        return $statement;
    }
}
