<?php

use AndrewNicols\Behat\ProfileManager;

return [
    'myProfileName' => [
        'edge',
        ProfileManager::getEdgedriverUrl(),
        ProfileManager::supportsW3C(),
        [
            'ms:edgeOptions' => [
                'binary' => '/path/to/edge/binary',
            ],
        ]
    ],
];
