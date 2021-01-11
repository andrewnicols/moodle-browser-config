<?php

use AndrewNicols\Behat\ProfileManager;

return [
    'bs_win10_firefox' => ProfileManager::getBrowserProfile(
        'firefox',
        ProfileManager::getBrowserStackUrl(),
        ProfileManager::supportsW3C(),
        [
            'bstack:options' => [
                'os' => 'Windows',
                'osVersion' => '10',
                'local' => true,
            ],
            'capabilities' => [
                'browserName' => 'Firefox',
                'browserVersion' => 'latest-beta',
            ],
        ]
    ),
    'bs_osx_firefox' => ProfileManager::getBrowserProfile(
        'firefox',
        ProfileManager::getBrowserStackUrl(),
        ProfileManager::supportsW3C(),
        [
            'bstack:options' => [
                'os' => 'OS X',
                'osVersion' => 'Big Sur',
                'local' => true,
            ],
            'capabilities' => [
                'browserName' => 'Firefox',
                'browserVersion' => 'latest-beta',
            ],
        ]
    ),
];
