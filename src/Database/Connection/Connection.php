<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database\Connection;

use Citrus\Database\DatabaseException;
use Citrus\Database\DSN;
use PDO;

/**
 * データベース接続
 */
class Connection
{
    /**
     * constructor.
     *
     * @param DSN|null $dsn    DSN情報
     * @param PDO|null $handle ハンドラー
     */
    public function __construct(
        public DSN|null $dsn = null,
        public PDO|null $handle = null,
    ) {
    }

    /**
     * destructor.
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * データベース接続
     *
     * @return void
     * @throws DatabaseException
     */
    public function connect(): void
    {
        // 設定済みの場合はスルー
        if (false === is_null($this->handle))
        {
            return;
        }

        try
        {
            $dsn = $this->dsn;
            $this->handle = new PDO(
                $dsn->toString(),
                $dsn->username,
                $dsn->password,
                $dsn->options
            );
        }
        catch (\PDOException $e)
        {
            throw new DatabaseException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * データベース切断
     *
     * @return void
     */
    public function disconnect(): void
    {
        $this->handle = null;
    }

    /**
     * ハンドルの取得
     *
     * @return PDO
     * @throws DatabaseException
     */
    public function callHandle(): PDO
    {
        // ハンドルがNULLなら接続できない
        DatabaseException::exceptionIf(is_null($this->handle), 'データベース接続が取得できません。');
        return $this->handle;
    }

    /**
     * トランザクション開始
     *
     * @return void
     */
    public function begin(): void
    {
        if (false === is_null($this->handle))
        {
            $this->handle->beginTransaction();
        }
    }

    /**
     * コミット
     *
     * @return void
     */
    public function commit(): void
    {
        if (false === is_null($this->handle))
        {
            $this->handle->commit();
        }
    }

    /**
     * ロールバック
     *
     * @return void
     */
    public function rollback(): void
    {
        if (false === is_null($this->handle))
        {
            $this->handle->rollBack();
        }
    }

    /**
     * トランザクション処理
     *
     * @param callable $transaction
     * @return void
     * @throws DatabaseException
     */
    public function transaction(callable $transaction): void
    {
        // トランザクション開始
        $this->begin();
        try
        {
            // トランザクション内処理
            $transaction();
            // コミット
            $this->commit();
        }
        catch (\Exception $e)
        {
            // ロールバック
            $this->rollback();
            /** @var DatabaseException $e */
            $e = DatabaseException::convert($e);
            throw $e;
        }
    }
}
