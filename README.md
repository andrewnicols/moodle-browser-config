# Moodle Behat Browser configuration utility

## Setup instructions

1. Open your `config.php` in your preferred editor
1. Just before the call to `require(__DIR__ . '/lib/setup.php');` add the following:
```
require_once('/path/to/moodle-browser-config/init.php');
```
1. Initialise behat


## Usage instructions
1. Start up the relevant driver
1. Run behat, specifying one of the defined browsers as a profile, for example:
```
vendor/bin/behat --profile=headlesschrome --config=/Users/nicols/Sites/moodles/w3c/moodledata_behat/behatrun/behat/behat.yml --tags=@javascript
```

## Configuration

Sensible defaults are provided for all drivers. These can be configured in the `config.php` file in the project root
folder. See the included `config-dist.php` for an example configuration.

The following configuration values can be configured:

Driver                | Configuration variable | Description                                                                                      | Default
---                   | ---                    | ---                                                                                              | ---
Selenium              | `seleniumUrl`          | The webdriver URL configured for Selenium                                                        | `http://localhost:4444/wd/hub`
chromedriver          | `chromedriverUrl`      | The webdriver URL configured for chromedriver                                                    | `http://localhost:9515`
Selenium/chromedriver | `chromeBinaryPath`     | The path to the Chrome or Chromium binary. Passed to selenium/chromedriver                       | [Not set]
geckodriver           | `geckodriverUrl`       | The webdriver URL configured for geckodriver                                                     | `http://localhost:4444`
Selenium/geckodriver  | `firefoxBinaryPath`    | The path to the Firefox binary. Passed to selenium/firefoxdriver                                 | [Not set]
edgedriverUrl         | `edgedriverUrl`        | The webdriver URL configured for Edge                                                            | `http://localhost:9515`
Selenium/edgedriver   | `edgeBinaryPath`       | The path to the Edge binary. Passed to selenium/edgedriver                                       | [Not set]
safaridriverUrl       | `safaridriverUrl`      | The webdriver URL configured for Safari                                                          | `http://localhost:4444`
BrowserStack          | `browserstackUrl`      | The webdriver URL configured for Browserstack. The username and password variables are preferred | [Automatically calculated from `browserstackUsername` and `browserstackPassword`]
Browserstack          | `browserstackUsername  | A username used to automatically configure the browserstackUrl                                   | [Not set]
Browserstack          | `browserstackPassword  | A password used to automatically configure the browserstackUrl                                   | [Not set]


An example configuration is available in `config-dist.php`.

## Default profiles

The following are the default profiles included with this utility.

Profile name           | Browser | Driver       | Notes
---                    | ---     | ---          | ---
`chrome`               | Chrome  | Selenium     | chromedriver required
`headlesschrome`       | Chrome  | Selenium     | Similar to `chrome`, but headless
`firefox`              | Firefox | Selenium     | geckodriver required
`headlessfirefox`      | Firefox | Selenium     | Similar to `firefox`, but headless
`chromedriver`         | Chrome  | chromedriver |
`headlesschromedriver` | Chrome  | chromedriver | Similar to `chromedriver`, but headless
`gecko`                | Firefox | geckodriver  |
`headlessgecko`        | Firefox | geckodriver  | Similar to `gecko`, but headless

### Browserstack Profiles

A small number of Browserstack profiles are included if browserstack credentials have been supplied in the config.php.

Profile name         | Browser | Driver       | Reliability | Notes
---                  | ---     | ---          | ---         | ---
`bs_win_edge`        | MS Edge | BrowserStack | Good        | Similar to `edge`, but running on Browserstack

### Experimental profiles

The following profiles are also included but should be considered experimental. Your mileage may vary.

Profile name         | Browser | Driver       | Reliability | Notes
---                  | ---     | ---          | ---         | ---
`edgedriver          | MS Edge | edgedriver   | Good        | Fairly reliable but additional configuration may be required
`headlessedgedriver` | MS Edge | edgedriver   | Good        | Similar to `edgedriver` but headless
`edge`               | MS Edge | Selenium     | Very poor   | Does not seem to work with Selenium 3.141.59
`safaridriver`       | Safari  | safaridriver | Very poor   | Click is not supported rendering this driver useless


## Custom profiles

In addition to the Default profiles, you can easily add your own configuration as required.

Create a new `localprofiles.php` file alongside the `init.php` and any configuration you may have.

See the `examples` folder for some examples of these.

```
<?php

use AndrewNicols\Behat\ProfileManager;

return [
    'firefox_nightly' => ProfileManager::getBrowserProfile(
        'firefox',
        ProfileManager::getSeleniumUrl(),
        ProfileManager::supportsW3C(),
        [
            'moz:firefoxOptions' => [
                'binary' => '/path/to/firefox/nightly',
            ],
        ]
    ),
];
```


## Browserstack

It is possible to define and use profiles for BrowserStack. You will need to define the following configuration in your
moodle-browser-config configuration file, for example:

```
$USERNAME = "my_first_tester";
$AUTOMATE_KEY = "Passw0rd!";
$CFG->behat_browserstack_url = "https://$USERNAME:$AUTOMATE_KEY@hub-cloud.browserstack.com/wd/hub";
```

### Custom Browserstack profiles

Use the W3C Protocol Generator for BrowserStack: https://www.browserstack.com/automate/capabilities?tag=selenium-4

Browserstack has options in the `bstack:options` capability, but some capabilities may need to be specified at the top level.

This can be achieved via passing a `capabilities` entry to the array used in `getBrowserProfile()`:

```
<?php

use AndrewNicols\Behat\ProfileManager;

return [
    'bs_osx_firefox' => ProfileManager::getBrowserProfile(
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
                'browser' => 'Firefox',
                'browserVersion' => 'latest-beta',
            ],
        ]
    ),
];
```

Further examples can be seen in the examples folder.
