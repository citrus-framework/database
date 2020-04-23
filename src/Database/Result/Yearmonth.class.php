<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database\Result;

use Citrus\Database\Column;

/**
 * 年月
 * @deprecated 使ってないんじゃないかな
 */
class Yearmonth extends Column
{
    /** @var int year */
    public $year;

    /** @var int month */
    public $month;

    /** @var float result */
    public $result;
}
