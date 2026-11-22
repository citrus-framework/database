<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2026, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database\Connection;

use Citrus\Database\DatabaseException;

/**
 * トランザクション処理の実行
 */
class TransactionRunner
{
    /**
     * constructor.
     * @param Connection $connection 接続
     */
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    /**
     * トランザクション処理
     *
     * コールバックかコミットで例外が出たらロールバックし、DatabaseException に作り直して投げる。
     * トランザクションがもう無いとき(コミットに失敗したあとなど)はロールバックしない。ロールバックが失敗しても、元の例外を作り直して投げる
     * @param callable $transaction
     * @return void
     * @throws DatabaseException
     */
    public function run(callable $transaction): void
    {
        // トランザクション開始
        $this->connection->begin();
        try
        {
            // トランザクション内処理
            $transaction();
            // コミット
            $this->connection->commit();
        }
        catch (\Exception $e)
        {
            try
            {
                // ロールバック
                $this->rollbackIfActive();
            }
            finally
            {
                // ロールバックが失敗しても、元の例外を作り直して投げる
                throw DatabaseException::fromThrowable($e);
            }
        }
    }

    /**
     * トランザクションが続いているときだけロールバックする(コミットに失敗したあとなど、もう無いときはしない)
     * @return void
     */
    private function rollbackIfActive(): void
    {
        if ($this->connection->handle?->inTransaction() === true)
        {
            $this->connection->rollback();
        }
    }
}
