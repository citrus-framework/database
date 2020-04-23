<?php

/**
 * @copyright   Copyright 2020, CitrusDatabase. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

$dir_base = dirname(__FILE__) . '/Sample';

return [
    'default' => [
        'application' => [
            'id'        => 'Test\Sample',
            'path'      => $dir_base,
        ],
        'database' => [
            'type'      => 'postgresql',
            'hostname'  => '192.168.0.1',
            'port'      => '5432',
            'database'  => 'cf-database',
            'schema'    => 'public',
            'username'  => 'citrus',
            'password'  => 'hogehoge',
            'options'   => [
                PDO::ATTR_PERSISTENT => true,
            ],
        ],
    ],
    'example.com' => [
        'application' => [
            'name'      => 'CitrusFramework Console.',
            'copyright' => 'Copyright 2019 CitrusFramework System, All Rights Reserved.',
            'domain'    => 'hoge.example.com',
        ],
    ],
];
