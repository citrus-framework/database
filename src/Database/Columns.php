<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database;

use Citrus\Database\Columns\Base;
use Citrus\Variable\Binders;
use Citrus\Variable\Dates;

/**
 * データベースカラム情報
 */
class Columns
{
    use Base;
    use Binders;

    /** @var string schema */
    public $schema;



    /**
     * constructor.
     */
    public function __construct()
    {
        $this->schema = DSN::getInstance()->schema;
    }



    /**
     * {@inheritdoc}
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
     * call primary keys
     *
     * @return string[]
     */
    public function callPrimaryKeys(): array
    {
        return [];
    }



    /**
     * complete insert column
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
     * complete modify column
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
     * null to blank
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
     * all nullify
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



    /**
     * object vars getter of (not null property) and (ignore slasses property)
     *
     * @param string[] $class_names 削除したいプロパティーを持つクラスのクラス名配列
     * @return array
     */
    public function notNullPropertyAndIgnoreClassProperties(array $class_names = []): array
    {
        // null以外のプロパティー
        $properties = $this->properties();

        $not_null_properties = [];
        foreach ($properties as $ky => $vl)
        {
            if (false === is_null($vl))
            {
                $not_null_properties[$ky] = $vl;
            }
        }

        // 指定クラスのプロパティーを削除する
        foreach ($class_names as $class_name)
        {
            // 指定クラスのプロパティーを削除する
            $class_property_keys = array_keys(get_class_vars($class_name));
            foreach ($class_property_keys as $class_property_key)
            {
                if (true === array_key_exists($class_property_key, $not_null_properties))
                {
                    unset($not_null_properties[$class_property_key]);
                }
            }
        }

        return $not_null_properties;
    }
}
