<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database;

use Citrus\Database\ResultSet\ResultClass;

/**
 * 汎用結果
 */
class Result extends Columns implements ResultClass
{
    /** @var int count */
    public int $count;

    /** @var int sum */
    public int $sum;

    /** @var int avg */
    public int $avg;

    /** @var int max */
    public int $max;

    /** @var int min */
    public int $min;

    /** @var int id */
    public int $id;

    /** @var string name */
    public string $name;

    /**
     * 結果内容のバインド
     * 必要ないパターンも多いので、実装化してしまう
     *
     * @return self
     */
    public function bindColumn(): self
    {
        return $this;
    }
}
