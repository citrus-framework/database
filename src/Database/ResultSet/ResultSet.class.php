<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database\ResultSet;

use Countable;
use IteratorAggregate;
use PDO;
use PDOStatement;

/**
 * 結果セット(クラスオブジェクトのインスタンス)
 *
 * @see https://docs.oracle.com/javase/jp/8/docs/api/java/sql/ResultSet.html
 */
class ResultSet implements IteratorAggregate, Countable
{
    /** @var PDOStatement */
    private $statement;

    /** @var string 返却クラス */
    private $result_class;



    /**
     * constructor.
     *
     * @param PDOStatement $statement    PDOのステートメント
     * @param string        $result_class 返却型
     */
    public function __construct(PDOStatement $statement, string $result_class)
    {
        $this->statement = $statement;
        $this->result_class = $result_class;
    }



    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        $this->statement->execute();
        $this->statement->setFetchMode(PDO::FETCH_CLASS, $this->result_class);

        // 実行してyieldでバインド
        /** @var ResultClass $row */
        foreach ($this->statement as $row)
        {
            // 有れば実行、基本的には有る前提
            if (true === method_exists($row, 'bindColumn'))
            {
                $row->bindColumn();
            }

            yield $row;
        }
    }



    /**
     * 配列化して取得
     *
     * @return array $this->result_class型
     */
    public function toList(): array
    {
        // 実行してバインド
        $results = $this->execute()->fetchAll();
        /** @var ResultClass $row */
        foreach ($results as $row)
        {
            $row->bindColumn();
        }
        return $results;
    }



    /**
     * 件数取得
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->execute()->fetchAll());
    }



    /**
     * 1件取得
     *
     * @return ResultClass
     */
    public function one()
    {
        return $this->getIterator()->current();
    }



    /**
     * ステートメントの実行
     *
     * @return PDOStatement
     */
    private function execute(): PDOStatement
    {
        // 実行
        $this->statement->execute();
        $this->statement->setFetchMode(PDO::FETCH_CLASS, $this->result_class);
        return $this->statement;
    }
}
