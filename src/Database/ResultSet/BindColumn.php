<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database\ResultSet;

/**
 * bindColumnのデフォルト実装
 */
trait BindColumn
{
    /**
     * @return $this
     */
    public function bindColumn()
    {
        return $this;
    }
}
