<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database;

use Citrus\Database\ResultSet\BindColumn;
use Citrus\Database\ResultSet\ResultClass;
use Citrus\Variable\Binders;

/**
 * データベースカラム情報
 */
class Columns extends \stdClass implements ResultClass
{
    use Binders;
    use BindColumn;

    /** @var string|null schema */
    public string|null $schema = null;

    /** @var int|null status */
    public int|null $status = 0;

    /** @var string|null created_at */
    public string|null $created_at = null;

    /** @var string|null updated_at */
    public string|null $updated_at = null;

    /** @var int|null rowid */
    public int|null $rowid = null;

    /** @var int|null rev */
    public int|null $rev = null;

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
        // bool は除く
        return array_filter($properties, static fn ($vl) => is_bool($vl) === false);
    }

    /**
     * インスタンスのプロパティを配列で取得
     * nullはunsetする
     */
    public function nullUnsetProperties(): array
    {
        return array_filter($this->properties(), static fn ($vl) => $vl !== null);
    }

    /**
     * プライマリキーのカラム名配列を取得
     * @return string[]
     */
    public function callPrimaryKeys(): array
    {
        return [];
    }

    /**
     * INSERT時に必要なカラム情報を補完する
     * @param string|null $timestamp
     */
    public function completeCreateColumn(string|null $timestamp = null): void
    {
        // スキーマ設定
        $this->schema = ColumnsCompletion::schema($this->schema);
        // タイムスタンプ設定
        $timestamp = ColumnsCompletion::timestamp($timestamp);
        $this->created_at = $timestamp;
        $this->updated_at = $timestamp;
    }

    /**
     * UPDATE時に必要なカラム情報を補完する
     * @param string|null $timestamp
     */
    public function completeUpdateColumn(string|null $timestamp = null): void
    {
        // スキーマ設定
        $this->schema = ColumnsCompletion::schema($this->schema);
        // タイムスタンプ設定
        $this->updated_at = ColumnsCompletion::timestamp($timestamp);
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
        // schema は消さない
        foreach (array_keys(array_diff_key($this->properties(), ['schema' => null])) as $ky)
        {
            $this->$ky = null;
        }
    }
}
