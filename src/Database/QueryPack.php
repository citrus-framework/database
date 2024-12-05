<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database;

/**
 * SQL実行のパッケージ
 */
class QueryPack
{
    /**
     * constructor.
     * @param string      $query        クエリ
     * @param array       $parameters   パラメータ
     * @param string|null $result_class 結果クラス
     */
    public function __construct(
        protected string $query,
        protected array $parameters = [],
        protected string|null $result_class = null,
    ) {
    }

    /**
     * クエリの取得
     * @return string
     */
    public function callQuery(): string
    {
        return $this->query;
    }

    /**
     * パラメタの取得
     * @return array
     */
    public function callParameters(): array
    {
        return $this->parameters;
    }

    /**
     * 結果クラスの取得
     * @return string
     */
    public function callResultClass(): string
    {
        return $this->result_class;
    }

    /**
     * ジェネレータ
     * @param string      $query        クエリ
     * @param array       $parameters   パラメタ
     * @param string|null $result_class 結果クラス
     * @return $this
     * @deprecated constructorに移行したい
     */
    public static function pack(string $query, array $parameters, string|null $result_class): self
    {
        return new self($query, $parameters, $result_class);
    }
}
