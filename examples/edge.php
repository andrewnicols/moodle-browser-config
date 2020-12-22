<?php

use AndrewNicols\Behat\ProfileManager;

$browserSetup = new ProfileManager();
$browserSetup->addBrowserProfile(
    'myProfileName',
    'edge',
    $browserSetup->getEdgedriverUrl(),
    $browserSetup->supportsW3C(),
    [
        'ms:edgeOptions' => [
            'binary' => '/path/to/edge/binary',
        ],
    ]
);
