<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2026, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database;

use Citrus\Configure\Configurable;
use Citrus\Console\ConsoleOutput;
use Citrus\Database\Catalog\CatalogManager;
use Citrus\Variable\Dates;
use Citrus\Variable\Singleton;

/**
 * データベースオブジェクト生成処理
 */
class Generate extends Configurable
{
    use ConsoleOutput;
    use Singleton;

    /** @var CatalogManager|null カタログマネージャ */
    protected CatalogManager|null $catalogManager = null;

    /**
     * {@inheritDoc}
     */
    public function loadConfigures(array $configures = []): Configurable
    {
        // 設定配列の読み込み
        parent::loadConfigures($configures);

        // 出力ファイル出力パスの設定
        $this->setupOutputDirectory();

        // DSN情報
        $dsn = DSN::getInstance()->loadConfigures($this->configures);

        // カタログマネージャ
        $this->catalogManager = new CatalogManager($dsn);

        return $this;
    }

    /**
     * Conditionクラスの生成
     *
     * 案件の側で項目やメソッドを書き足すため、ファイルがすでにあれば作らない
     * @param string $class_prefix クラス接頭辞
     */
    public function condition(string $class_prefix): void
    {
        $generate_class_path = sprintf(
            '%s/Condition/%sCondition.php',
            $this->configures['output_dir'],
            $class_prefix
        );

        $source = GenerateSource::condition(
            $this->configures['namespace'],
            $class_prefix,
            Dates::now()->formatTimestamp()
        );

        // すでにあれば作らない
        if (GenerateFile::createIfAbsent($generate_class_path, $source) === false)
        {
            $this->success(sprintf('condition class file already exists => %s', $generate_class_path));
            return;
        }
        $this->success(sprintf('generate class file => %s', $generate_class_path));
    }

    /**
     * Propertyクラスの生成
     *
     * 作るたびに上書きする
     * @param string $class_prefix クラス接頭辞
     * @param string $table_name   テーブル名
     */
    public function property(string $class_prefix, string $table_name): void
    {
        // 列が読めなければ、中身の無い Property で上書きしないよう止める
        $columns = $this->catalogManager->tableColumns($table_name);
        DatabaseException::exceptionIf(
            count($columns) === 0,
            sprintf('テーブルの列が読めませんでした => %s', $table_name),
        );

        $source = GenerateSource::property(
            $this->configures['namespace'],
            $class_prefix,
            $columns,
            $this->catalogManager->columnComments($table_name),
            $this->catalogManager->primaryKeys($table_name),
            Dates::now()->formatTimestamp()
        );

        $generate_class_path = sprintf('%s/Property/%sProperty.php', $this->configures['output_dir'], $class_prefix);
        GenerateFile::overwrite($generate_class_path, $source);
        $this->success(sprintf('generate class file => %s', $generate_class_path));
    }

    /**
     * クラスの一括生成
     * @param string $class_prefix クラス接頭辞
     * @param string $table_name   テーブル名
     */
    public function all(string $class_prefix, string $table_name): void
    {
        $this->condition($class_prefix);
        $this->property($class_prefix, $table_name);
    }

    /**
     * {@inheritDoc}
     */
    protected function configureKey(): string
    {
        return 'integration';
    }

    /**
     * {@inheritDoc}
     */
    protected function configureDefaults(): array
    {
        return [
            'mode' => 0755,
            'owner' => posix_getpwuid(posix_geteuid())['name'],
            'group' => posix_getgrgid(posix_getegid())['name'],
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function configureRequires(): array
    {
        return [
            'database',
            'mode',
            'owner',
            'group',
            'output_dir',
            'namespace',
        ];
    }

    /**
     * 出力ファイル格納ディレクトリパスの設定
     */
    private function setupOutputDirectory(): void
    {
        // 出力ディレクトリ
        $parent_dir = $this->configures['output_dir'];

        // 各ディレクトリ
        $dirs = [
            '/Condition',
            '/Property',
        ];

        foreach ($dirs as $dir)
        {
            // 各出力ディレクトリ
            $output_dir = sprintf('%s%s', $parent_dir, $dir);

            // ディレクトリがなければ生成
            if (file_exists($output_dir) === false)
            {
                DatabaseException::exceptionIf(
                    mkdir($output_dir, $this->configures['mode'], true) === false,
                    sprintf('フォルダを作れませんでした => %s', $output_dir),
                );
                chmod($output_dir, $this->configures['mode']);
                chown($output_dir, $this->configures['owner']);
                chgrp($output_dir, $this->configures['group']);
            }
        }
    }
}
