<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2026, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database;

use Citrus\Database\Connection\Connection;
use PDOStatement;

/**
 * アドバイザリーロック(PostgreSQL 用)
 *
 * キーの名前(文字列)を hashtext() で数値にして、PostgreSQL のアドバイザリーロックを取る。
 * キーには2種類ある。
 * ・トランザクションのキー: トランザクションが終わると外れる。取れるまで待つ
 * ・つなぎのキー: DB とのつなぎが切れるまで持ち続ける。待たずに取る
 */
class AdvisoryLock
{
    /**
     * つないでいる間持ち続けるキーの、pg_locks のレコード(? にキーの名前)
     *
     * pg_try_advisory_lock(bigint) のキーは、pg_locks の classid に上の32ビット、objid に下の32ビット、objsubid に 1 で出る
     */
    private const string SESSION_LOCK_ROWS = <<<'SQL'
        FROM pg_locks l
        CROSS JOIN (SELECT CAST(hashtext(CAST(? AS text)) AS bigint) AS k) h
        WHERE l.locktype = 'advisory' AND l.granted AND l.objsubid = 1
        AND l.database = (SELECT oid FROM pg_database WHERE datname = current_database())
        AND CAST(l.classid AS bigint) = ((h.k >> 32) & 4294967295)
        AND CAST(l.objid AS bigint) = (h.k & 4294967295)
        SQL;

    /**
     * constructor.
     * @param Connection $connection 接続
     */
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    /**
     * トランザクションが終わるまで持つキーの取得(取れるまで待つ)
     *
     * pg_advisory_xact_lock(hashtext(キーの名前))。トランザクションの中で呼ぶ。トランザクションが終わると外れる
     * @param string $key キーの名前
     * @return void
     * @throws DatabaseException
     */
    public function acquireTransaction(string $key): void
    {
        $this->execute('SELECT pg_advisory_xact_lock(hashtext(CAST(? AS text)))', [$key]);
    }

    /**
     * つないでいる間持ち続けるキーの取得(待たない)
     *
     * pg_try_advisory_lock(hashtext(キーの名前))。取れたら、DB とのつなぎが切れるまで持つ
     * @param string $key キーの名前
     * @return bool 取れたか(ほかのつなぎが持っていれば false)
     * @throws DatabaseException
     */
    public function tryAcquireSession(string $key): bool
    {
        $value = $this
            ->execute('SELECT pg_try_advisory_lock(hashtext(CAST(? AS text))) AS acquired', [$key])
            ->fetchColumn();
        return $value === true or $value === 't' or $value === 1 or $value === '1';
    }

    /**
     * つないでいる間持ち続けるキーを、いまどれかのつなぎが持っているか(pg_locks で見る)
     * @param string $key キーの名前
     * @return bool
     * @throws DatabaseException
     */
    public function isSessionLockHeld(string $key): bool
    {
        return $this->execute('SELECT 1 ' . self::SESSION_LOCK_ROWS . ' LIMIT 1', [$key])->fetchColumn() !== false;
    }

    /**
     * つないでいる間持ち続けるキーを、このつなぎ(pg_backend_pid())が持っているか
     * @param string $key キーの名前
     * @return bool
     * @throws DatabaseException DB とのつなぎが切れているときなど
     */
    public function isSessionLockHeldByMe(string $key): bool
    {
        return $this
            ->execute('SELECT 1 ' . self::SESSION_LOCK_ROWS . ' AND l.pid = pg_backend_pid() LIMIT 1', [$key])
            ->fetchColumn() !== false;
    }

    /**
     * SQL を送る
     *
     * 先に DB につなぐ(つながっていれば何もしない)
     * @param string             $sql        SQL(? で値を渡す)
     * @param array<int, string> $parameters 値
     * @return PDOStatement
     * @throws DatabaseException
     */
    private function execute(string $sql, array $parameters): PDOStatement
    {
        $this->connection->connect();
        $handle = $this->connection->callHandle();
        $statement = $handle->prepare($sql);
        if ($statement === false)
        {
            throw DatabaseException::pdoErrorInfo($handle->errorInfo());
        }
        $statement->execute($parameters);
        return $statement;
    }
}
