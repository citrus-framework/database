<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2026, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database;

use Citrus\Database\Catalog\ColumnDef;
use Citrus\Variable\Klass;
use Citrus\Variable\Klass\KlassFileComment;
use Citrus\Variable\Klass\KlassMethod;
use Citrus\Variable\Klass\KlassProperty;
use Citrus\Variable\Klass\KlassReturn;
use Citrus\Variable\Klass\KlassTrait;
use Citrus\Variable\Klass\KlassVisibility;

/**
 * 生成するクラスの文字列を作る
 *
 * DB の読み込みとファイルへの書き込みは Generate が行い、ここでは文字列を作ることだけをする
 */
class GenerateSource
{
    /** Property に書かない列(query の Record がすでに持っている列と、database の Columns の schema。query のパッケージを使わないため、名前の一覧で持つ) */
    public const array RECORD_COLUMNS = [
        'schema',
        'status',
        'created_at',
        'updated_at',
        'rowid',
        'rev',
    ];

    /** 列の型(小文字)からPHPの型への読み替え */
    private const array PHP_TYPES = [
        'integer' => 'int',
        'bigint' => 'int',
        'smallint' => 'int',
        'real' => 'float',
        'double precision' => 'float',
        'numeric' => 'float',
        'boolean' => 'bool',
    ];

    /** 読み替えにない列の型のPHPの型 */
    private const string DEFAULT_PHP_TYPE = 'string';

    /**
     * テーブルカラムの型からPHPの型に変換
     * @param string $data_type カラムデータタイプ
     * @return string PHPの型
     */
    public static function convertToPHPType(string $data_type): string
    {
        return self::PHP_TYPES[strtolower($data_type)] ?? self::DEFAULT_PHP_TYPE;
    }

    /**
     * Propertyクラスの文字列を作る
     * @param string               $namespace    設定の namespace
     * @param string               $class_prefix クラス接頭辞
     * @param ColumnDef[]          $columns      カラム定義
     * @param ColumnDef[]          $comments     コメント定義(キーはカラム名)
     * @param string[]             $primary_keys プライマリキーのカラム名
     * @param string               $timestamp    生成日時
     * @return string
     */
    public static function property(
        string $namespace,
        string $class_prefix,
        array $columns,
        array $comments,
        array $primary_keys,
        string $timestamp
    ): string {
        $class_name = sprintf('%sProperty', $class_prefix);

        $klass = (new Klass($class_name))
            ->setStrictTypes(true)
            ->setFileComment(KlassFileComment::newRaw(
                sprintf('generated Citrus Property file at %s', $timestamp)
            ))
            ->setNamespace(sprintf('%s\\Integration\\Property', $namespace))
            ->addUse('Citrus\\Query\\RawExpr')
            ->setClassComment($class_name)
            ->setExtends('\\Citrus\\Query\\ResultSet\\Record')
            ->addMethod(self::primaryKeysMethod($primary_keys));

        foreach (self::propertyColumns($columns) as $column)
        {
            $klass->addProperty(self::propertyOf($column, $comments));
        }

        return $klass->toString();
    }

    /**
     * Conditionクラスの文字列を作る
     * @param string $namespace    設定の namespace
     * @param string $class_prefix クラス接頭辞
     * @param string $timestamp    生成日時
     * @return string
     */
    public static function condition(string $namespace, string $class_prefix, string $timestamp): string
    {
        $class_name = sprintf('%sCondition', $class_prefix);
        $extend_name = sprintf('\\%s\\Integration\\Property\\%sProperty', $namespace, $class_prefix);

        $klass = (new Klass($class_name))
            ->setStrictTypes(true)
            ->setFileComment(KlassFileComment::newRaw(
                sprintf('generated Citrus Condition file at %s', $timestamp)
            ))
            ->setNamespace(sprintf('%s\\Integration\\Condition', $namespace))
            ->setClassComment($class_name)
            ->setExtends($extend_name)
            ->addTrait(new KlassTrait('\\Citrus\\Variable\\PathBinders'));

        return $klass->toString();
    }

    /**
     * Record がすでに持っているカラムを除いたカラム定義
     * @param ColumnDef[] $columns カラム定義
     * @return ColumnDef[]
     */
    private static function propertyColumns(array $columns): array
    {
        return array_filter(
            $columns,
            static fn (ColumnDef $column): bool => in_array($column->column_name, self::RECORD_COLUMNS, true) === false
        );
    }

    /**
     * カラムのプロパティ
     * @param ColumnDef   $column   カラム定義
     * @param ColumnDef[] $comments コメント定義(キーはカラム名)
     * @return KlassProperty
     */
    private static function propertyOf(ColumnDef $column, array $comments): KlassProperty
    {
        $type = sprintf('%s|RawExpr|null', self::convertToPHPType($column->data_type));
        $comment = ($comments[$column->column_name]->comment ?? '');
        return new KlassProperty($type, $column->column_name, 'null', $comment);
    }

    /**
     * primaryKeys メソッド
     * @param string[] $primary_keys プライマリキーのカラム名
     * @return KlassMethod
     */
    private static function primaryKeysMethod(array $primary_keys): KlassMethod
    {
        $quoted = array_map(static fn (string $key): string => sprintf('\'%s\'', $key), $primary_keys);
        $body = sprintf('        return [%s];', implode(', ', $quoted));

        return (new KlassMethod(KlassVisibility::PUBLIC, 'primaryKeys', false, 'call primary keys'))
            ->setReturn(new KlassReturn('string[]'))
            ->setBody($body);
    }
}
