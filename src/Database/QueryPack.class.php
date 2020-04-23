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
    /** @var string クエリ */
    protected $query;

    /** @var array パラメータ */
    protected $parameters = [];

    /** @var string 結果クラス */
    protected $result_class;



    /**
     * クエリの取得
     *
     * @return string
     */
    public function callQuery(): string
    {
        return $this->query;
    }



    /**
     * パラメタの取得
     *
     * @return array
     */
    public function callParameters(): array
    {
        return $this->parameters;
    }



    /**
     * 結果クラスの取得
     *
     * @return string
     */
    public function callResultClass(): string
    {
        return $this->result_class;
    }



    /**
     * ジェネレータ
     *
     * @param string      $query        クエリ
     * @param array       $parameters   パラメタ
     * @param string|null $result_class 結果クラス
     * @return self
     */
    public static function pack(string $query, array $parameters, ?string $result_class)
    {
        $self = new self();
        $self->query = $query;
        $self->parameters = $parameters;
        $self->result_class = $result_class;
        return $self;
    }
}
