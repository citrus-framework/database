<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database\Column;

/**
 * テーブルの共通カラム
 */
trait Base
{
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
}
