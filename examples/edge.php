<?php

use AndrewNicols\Behat\ProfileManager;

return [
    'myProfileName' => ProfileManager::getBrowserStackUrl(
        'edge',
        ProfileManager::getEdgedriverUrl(),
        ProfileManager::supportsW3C()
    )
];
