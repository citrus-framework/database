<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2026, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test\Database;

use Citrus\Database\Catalog\ColumnDef;
use Citrus\Database\GenerateSource;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * 生成するクラスの文字列のテスト
 */
class GenerateSourceTest extends TestCase
{
    /**
     * 列の型の読み替え
     * @return array<string, array{string, string}>
     */
    public static function providePhpTypes(): array
    {
        return [
            'integer' => ['integer', 'int'],
            'bigint' => ['bigint', 'int'],
            'smallint' => ['smallint', 'int'],
            'real' => ['real', 'float'],
            'double precision' => ['double precision', 'float'],
            'numeric' => ['numeric', 'float'],
            'boolean' => ['boolean', 'bool'],
            'character varying' => ['character varying', 'string'],
            'text' => ['text', 'string'],
            'date' => ['date', 'string'],
            'timestamp' => ['timestamp without time zone', 'string'],
            'uuid' => ['uuid', 'string'],
            'jsonb' => ['jsonb', 'string'],
            '知らない型' => ['unknown_type', 'string'],
            '大文字' => ['INTEGER', 'int'],
        ];
    }

    #[Test]
    #[DataProvider('providePhpTypes')]
    public function 列の型をPHPの型に読み替える(string $data_type, string $expected): void
    {
        $this->assertSame($expected, GenerateSource::convertToPHPType($data_type));
    }

    #[Test]
    public function 列の一覧からPropertyの文字列を作る(): void
    {
        $columns = [
            ColumnDef::forDataType('tag_id', 'character varying'),
            ColumnDef::forDataType('account_id', 'character varying'),
            ColumnDef::forDataType('status', 'integer'),
            ColumnDef::forDataType('created_at', 'timestamp without time zone'),
            ColumnDef::forDataType('updated_at', 'timestamp without time zone'),
            ColumnDef::forDataType('rowid', 'bigint'),
            ColumnDef::forDataType('rev', 'integer'),
            ColumnDef::forDataType('schema', 'text'),
        ];
        $comments = [
            'tag_id' => ColumnDef::forComment('tag_id', 'タグのID'),
            'account_id' => ColumnDef::forComment('account_id', '運用アカウントID'),
        ];

        $actual = GenerateSource::property(
            'Social',
            'Tag',
            $columns,
            $comments,
            ['tag_id'],
            '2026-11-05 10:00:00'
        );

        $expected = <<<'PHP'
<?php

declare(strict_types=1);

/**
 * generated Citrus Property file at 2026-11-05 10:00:00
 */

namespace Social\Integration\Property;

use Citrus\Query\RawExpr;

/**
 * TagProperty
 */
class TagProperty extends \Citrus\Query\ResultSet\Record
{
    /** @var string|RawExpr|null タグのID */
    public string|RawExpr|null $tag_id = null;

    /** @var string|RawExpr|null 運用アカウントID */
    public string|RawExpr|null $account_id = null;

    /**
     * call primary keys
     * @return string[]
     */
    public function primaryKeys(): array
    {
        return ['tag_id'];
    }
}

PHP;
        $this->assertSame($expected, $actual);
    }

    #[Test]
    public function 主キーが無ければ空の配列を返す(): void
    {
        $actual = GenerateSource::property('Social', 'Tag', [], [], [], '2026-11-05 10:00:00');

        $this->assertStringContainsString('return [];', $actual);
    }

    #[Test]
    public function 主キーが複数あればカンマで区切る(): void
    {
        $actual = GenerateSource::property('Social', 'Tag', [], [], ['a_id', 'b_id'], '2026-11-05 10:00:00');

        $this->assertStringContainsString('return [\'a_id\', \'b_id\'];', $actual);
    }

    #[Test]
    public function 型に合わせてPropertyの型を変える(): void
    {
        $columns = [
            ColumnDef::forDataType('is_member', 'boolean'),
            ColumnDef::forDataType('score', 'numeric'),
        ];

        $actual = GenerateSource::property('Social', 'Tag', $columns, [], [], '2026-11-05 10:00:00');

        $this->assertStringContainsString('public bool|RawExpr|null $is_member = null;', $actual);
        $this->assertStringContainsString('public float|RawExpr|null $score = null;', $actual);
    }

    #[Test]
    public function Conditionの文字列を作る(): void
    {
        $actual = GenerateSource::condition('Social', 'Tag', '2026-11-05 10:00:00');

        $expected = <<<'PHP'
<?php

declare(strict_types=1);

/**
 * generated Citrus Condition file at 2026-11-05 10:00:00
 */

namespace Social\Integration\Condition;

/**
 * TagCondition
 */
class TagCondition extends \Social\Integration\Property\TagProperty
{
    use \Citrus\Variable\PathBinders;
}

PHP;
        $this->assertSame($expected, $actual);
    }
}
