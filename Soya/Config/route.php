<?php


return [
    'STATIC_ROUTE_ON'   => false,
    'WILDCARD_ROUTE_ON' => true,
    'WILDCARD_ROUTE_RULES'    => [
        '/wechat/[num]'   => [
            'm' => 'Wechat',
            'c' => 'Index',
            'a' => 'index',
            '$1'    => 'p.id',
        ],
    ],
];