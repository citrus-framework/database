<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database;

use Citrus\Variable\Binders;
use Citrus\Variable\Dates;

/**
 * データベースカラム情報
 */
class Columns
{
    use Binders;

    /** @var string schema */
    public $schema;

    /** @var string status */
    public $status = 0;

    /** @var string created_at */
    public $created_at;

    /** @var string updated_at */
    public $updated_at;

    /** @var int rowid */
    public $rowid;

    /** @var int rev */
    public $rev;



    /**
     * constructor.
     */
    public function __construct()
    {
        $this->schema = DSN::getInstance()->schema;
    }



    /**
     * インスタンスのプロパティを配列で取得
     */
    public function properties(): array
    {
        $properties = get_object_vars($this);
        unset($properties['schema']);
        foreach ($properties as $ky => $vl)
        {
            if (true === is_bool($vl))
            {
                unset($properties[$ky]);
            }
        }

        return $properties;
    }



    /**
     * プライマリキーのカラム名配列を取得
     *
     * @return string[]
     */
    public function callPrimaryKeys(): array
    {
        return [];
    }



    /**
     * INSERT時に必要なカラム情報を補完する
     *
     * @param string|null $timestamp
     */
    public function completeCreateColumn(string $timestamp = null): void
    {
        // スキーマ設定
        $this->schema = $this->schema ?? DSN::getInstance()->schema;
        // タイムスタンプ設定
        $timestamp = $timestamp ?? Dates::now()->format('Y-m-d H:i:s');
        $this->created_at = $timestamp;
        $this->updated_at = $timestamp;
    }



    /**
     * UPDATE時に必要なカラム情報を補完する
     *
     * @param string|null $timestamp
     */
    public function completeUpdateColumn(string $timestamp = null): void
    {
        // スキーマ設定
        $this->schema = $this->schema ?? DSN::getInstance()->schema;
        // タイムスタンプ設定
        $timestamp = $timestamp ?? Dates::now()->format('Y-m-d H:i:s');
        $this->updated_at = $timestamp;
    }



    /**
     * nullを空文字に変更する
     */
    public function null2blank(): void
    {
        $properties = $this->properties();
        foreach ($properties as $ky => $vl)
        {
            $this->$ky = ($vl ?? '');
        }
    }



    /**
     * 全てのインスタンス変数にnullを代入する
     */
    public function nullify(): void
    {
        $properties = $this->properties();
        foreach ($properties as $ky => $vl)
        {
            if (false === in_array($ky, ['schema']))
            {
                $this->$ky = null;
            }
        }
    }
}
