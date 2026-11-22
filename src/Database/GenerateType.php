<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2026, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database;

/**
 * データベースオブジェクト生成タイプ
 */
enum GenerateType: string
{
    /** Propertyクラス */
    case PROPERTY = 'property';

    /** Conditionクラス */
    case CONDITION = 'condition';

    /** 全て */
    case ALL = 'all';
}
