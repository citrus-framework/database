<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2026, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test\Database;

use Citrus\Database\GenerateFile;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * 生成したクラスのファイルへの書き込みのテスト
 */
class GenerateFileTest extends TestCase
{
    /** @var string テスト用のファイルパス */
    private string $path = '';

    protected function setUp(): void
    {
        $this->path = sprintf('%s/generate_file_test_%s.php', sys_get_temp_dir(), uniqid('', true));
    }

    protected function tearDown(): void
    {
        if (file_exists($this->path) === true)
        {
            unlink($this->path);
        }
    }

    #[Test]
    public function ファイルが無ければConditionを作る(): void
    {
        $actual = GenerateFile::createIfAbsent($this->path, 'new');

        $this->assertTrue($actual);
        $this->assertSame('new', file_get_contents($this->path));
    }

    #[Test]
    public function ファイルがあれば上書きしない(): void
    {
        file_put_contents($this->path, 'edited');

        $actual = GenerateFile::createIfAbsent($this->path, 'new');

        $this->assertFalse($actual);
        $this->assertSame('edited', file_get_contents($this->path));
    }

    #[Test]
    public function 上書きの書き込みは中身を置き換える(): void
    {
        file_put_contents($this->path, 'old');

        GenerateFile::overwrite($this->path, 'new');

        $this->assertSame('new', file_get_contents($this->path));
    }
}
