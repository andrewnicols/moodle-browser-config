<?php

use AndrewNicols\Behat\ProfileManager;

$profiles = [];

for ($i = 1; $i <= 3; $i++) {
    // This is a hacky way to copy a profile.
    // We'll add a better way in future.
    $profile = array_merge([], $CFG->behat_profiles['headlessgeckodriver']);
    $profile['wd_host'] = str_replace('4444', 4444 + $i - 1, $profile['wd_host']);
    $profiles["headlessgeckodriver{$i}"] = $profile;
}

return $profiles;
