<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database\ResultSet;

/**
 * 結果クラスの抽象実装
 */
interface ResultClass
{
    /**
     * 結果内容のバインド
     *
     * 必要ないパターンも多いので、実装化してしまう
     *
     * @return self
     */
    public function bindColumn();
}
