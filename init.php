<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Behat Browser configuration.
 *
 * @package    test
 * @copyright  2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_behat\test;

load_local_configuration();
$CFG->behat_profiles = get_standard_profiles();

function load_local_configuration() {
    global $CFG;
    $browserconfigfilepath = __DIR__ . '/config.php';
    if (file_exists($browserconfigfilepath)) {
        require_once($browserconfigfilepath);
    }
}

/**
 * Get standard Behat profiles.
 *
 * @return  array
 */
function get_standard_profiles(): array {
    global $CFG;

    if (!isset($CFG->behat_chromedriver_url)) {
        $CFG->behat_chromedriver_url = 'http://localhost:9515';
    }

    if (!isset($CFG->behat_geckodriver_url)) {
        $CFG->behat_geckodriver_url = 'http://localhost:4446';
    }

    if (!isset($CFG->behat_safaridriver_url)) {
        $CFG->behat_safaridriver_url = 'http://localhost:4447';
    }

    if (!isset($CFG->behat_edgedriver_url)) {
        $CFG->behat_edgedriver_url = 'http://localhost:4448';
    }

    if (!isset($CFG->behat_selenium_url)) {
        $CFG->behat_selenium_url = 'http://localhost:4444/wd/hub';
    }

    $w3c = supports_w3c();

    $profiles = [
        'safaridriver' => get_profile_for_browser('safari', $CFG->behat_safaridriver_url, $w3c),

        // Microsoft Edge.
        'edgedriver' => get_profile_for_browser(
            'edge',
            $CFG->behat_edgedriver_url,
            $w3c
        ),
        'headlessedgedriver' => get_profile_for_browser(
            'edge',
            $CFG->behat_edgedriver_url,
            $w3c, [
                'ms:edgeOptions' => [
                    'args' => [
                        'headless',
                        'no-gpu',
                    ],
                ],
            ]
        ),

        // Note: There is currently some configuration issue when running Edge through selenium.
        'edge' => get_profile_for_browser(
            'edge',
            $CFG->behat_selenium_url,
            $w3c
        ),

        // Google Chrome using Chromedriver.
        'chromedriver' => get_profile_for_browser(
            'chrome',
            $CFG->behat_chromedriver_url,
            $w3c
        ),
        'headlesschromedriver' => get_profile_for_browser(
            'chrome',
            $CFG->behat_chromedriver_url,
            $w3c,
            [
                'chromeOptions' => [
                    'args' => [
                        'headless',
                        'no-gpu',
                    ],
                ],
            ]
        ),

        // Google Chrome using Selenium.
        'chrome' => get_profile_for_browser(
            'chrome',
            $CFG->behat_selenium_url,
            $w3c
        ),
        'headlesschrome' => get_profile_for_browser(
            'chrome',
            $CFG->behat_selenium_url,
            $w3c,
            [
                'chromeOptions' => [
                    'args' => [
                        'headless',
                        'no-gpu',
                    ],
                ],
            ]
        ),

        // Mozilla Firefox using Geckodriver.
        'gecko' => get_profile_for_browser(
            'firefox',
            $CFG->behat_geckodriver_url,
            $w3c
        ),
        'headlessgecko' => get_profile_for_browser(
            'firefox',
            $CFG->behat_geckodriver_url,
            $w3c,
            [
                'moz:firefoxOptions' => [
                    'args' => [
                        '-headless',
                    ],
                ],
            ]
        ),

        // Mozilla Firefox using Selenium.
        'firefox' => get_profile_for_browser(
            'firefox',
            $CFG->behat_selenium_url,
            $w3c
        ),
        'headlessfirefox' => get_profile_for_browser(
            'firefox',
            $CFG->behat_selenium_url,
            $w3c,
            [
                'moz:firefoxOptions' => [
                    'args' => [
                        '-headless',
                    ],
                ],
            ]
        ),
    ];

    // A small selection of Browserstack browsers to gives an example of how these can be used.
    if (property_exists($CFG, 'behat_browserstack_url')) {
        $profiles['bs_osx_safari'] = get_profile_for_browser(
            'safari',
            $CFG->behat_browserstack_url,
            $w3c,
            [
                'os' => 'OS X',
                'os_version' => 'Big Sur',
                'browser' => 'Safari',
                'browserstack.local' => true,
            ]
        );

        $profiles['bs_win_edge'] = get_profile_for_browser(
            'edge',
            $CFG->behat_browserstack_url,
            $w3c,
            [
                'os' => 'Windows',
                'os_version' => '10',
                'browser' => 'Edge',
                'browser_version' => '88.0 beta',
                'browserstack.local' => true,
            ]
        );
    }

    return $profiles;
}

/**
 * Add a browser profile, translating known configuration parameters where possible.
 *
 * @param   string $browserName
 * @param   string $wdhost
 * @param   bool $w3c
 * @param   array $capabilities
 * @return  array
 */
function get_profile_for_browser(string $browserName, string $wdhost, bool $w3c = true, array $capabilities = []): array {
    global $CFG;

    if ($browserName === 'chrome') {
        $capabilities = array_merge_recursive(
            [
                'args' => [
                    'no-sandbox',
                ],
            ],
            $capabilities
        );
    }
    else if ($browserName === 'firefox') {
        $capabilities = array_merge_recursive(
            [
                'moz:firefoxOptions' => [
                    'prefs' => [
                        'devtools.console.stdout.content' => true,
                    ],
                    'log' => [
                        'level' => 'trace',
                    ],
                ],
            ],
            $capabilities
        );
    }
    else if ($browserName === 'edge') {
        $defaultcapabilities = [
            [
                'ms:edgeOptions' => [],
                'ms:edgeChromium' => true,
            ],
        ];

        if (isset($CFG->behat_edge_binary)) {
            $defaultcapabilities['ms:edgeOptions']['binary'] = $CFG->behat_edge_binary;
        }
        $capabilities = array_merge_recursive(
            $defaultcapabilities,
            $capabilities
        );
    }

    $profile = [];
    $profile['browser'] = $browserName;
    $profile['capabilities'] = [
        'extra_capabilities' => [],
    ];

    $profile['wd_host'] = $wdhost;

    if (array_key_exists('chromeOptions', $capabilities)) {
        // Taken from https://chromedriver.chromium.org/capabilities.
        $types = [
            'binary' => 'scalar',
            'debuggerAddress' => 'scalar',
            'detach' => 'scalar',
            'minidumpPath' => 'scalar',

            'args' => 'list',
            'extensions' => 'list',
            'excludeSwitches' => 'list',
            'windowTypes' => 'list',

            'localState' => 'dict',
            'prefs' => 'dict',
            'mobileEmulation' => 'dict',
            'perfLoggingPrefs' => 'dict',
        ];

        $browserOptions = [];
        foreach ($capabilities['chromeOptions'] as $key => $values) {
            if (!array_key_exists($key, $types)) {
                throw new \InvalidArgumentException("Unknown option in chromeOptions: '{$key}'");
            }
            if ($types[$key] === 'scalar') {
                $browserOptions[$key] = $values;
            } else if ($types[$key] === 'list') {
                if (array_key_exists($key, $browserOptions)) {
                    $browserOptions[$key] = array_merge($browserOptions[$key], $values);
                } else {
                    $browserOptions[$key] = $values;
                }
            } else if ($types[$key] === 'dict') {
                if (array_key_exists($key, $browserOptions)) {
                    $browserOptions[$key] = array_replace($browserOptions[$key], $values);
                } else {
                    $browserOptions[$key] = $values;
                }
            }
        }

        $profile['capabilities']['extra_capabilities']['chromeOptions'] = $browserOptions;
    } else
    if (array_key_exists('moz:firefoxOptions', $capabilities)) {
        // Taken from https://developer.mozilla.org/en-US/docs/Web/WebDriver/Capabilities/firefoxOptions
        $types = [
            'binary' => 'scalar',
            'profile' => 'scalar',

            'args' => 'list',

            'prefs' => 'dict',
            'log' => 'dict',
        ];

        $browserOptions = [];
        foreach ($capabilities['moz:firefoxOptions'] as $key => $values) {
            if (!array_key_exists($key, $types)) {
                throw new \InvalidArgumentException("Unknown option in firefoxOptions: '{$key}'");
            }
            if ($types[$key] === 'scalar') {
                $browserOptions[$key] = $values;
            } else if ($types[$key] === 'list') {
                if (array_key_exists($key, $browserOptions)) {
                    $browserOptions[$key] = array_merge($browserOptions[$key], $values);
                } else {
                    $browserOptions[$key] = $values;
                }
            } else if ($types[$key] === 'dict') {
                if (array_key_exists($key, $browserOptions)) {
                    $browserOptions[$key] = array_replace($browserOptions[$key], $values);
                } else {
                    $browserOptions[$key] = $values;
                }

            }
        }

        if ($w3c) {
            $profile['capabilities']['extra_capabilities']['moz:firefoxOptions'] = $browserOptions;
        } else {
            $profile['capabilities']['extra_capabilities'] = $browserOptions;
            $profile['capabilities']['extra_capabilities']['marionette'] = false;
        }
    } else
    if (array_key_exists('safari:options', $capabilities)) {
        $browserOptions = $capabilities['safari:options'];
        $profile['capabilities']['extra_capabilities']['safari:options'] = $browserOptions;

    } else
    if (array_key_exists('ms:edgeOptions', $capabilities)) {
        $types = [
            'binary' => 'scalar',
            'args' => 'list',
        ];

        $browserOptions = [];
        foreach ($capabilities['ms:edgeOptions'] as $key => $values) {
            if (!array_key_exists($key, $types)) {
                throw new \InvalidArgumentException("Unknown option in firefoxOptions: '{$key}'");
            }
            if ($types[$key] === 'scalar') {
                $browserOptions[$key] = $values;
            } else if ($types[$key] === 'list') {
                if (array_key_exists($key, $browserOptions)) {
                    $browserOptions[$key] = array_merge($browserOptions[$key], $values);
                } else {
                    $browserOptions[$key] = $values;
                }
            } else if ($types[$key] === 'dict') {
                if (array_key_exists($key, $browserOptions)) {
                    $browserOptions[$key] = array_replace($browserOptions[$key], $values);
                } else {
                    $browserOptions[$key] = $values;
                }

            }
        }

        $profile['capabilities']['extra_capabilities']['ms:edgeOptions'] = $browserOptions;
        $profile['capabilities']['extra_capabilities']['ms:edgeChromium'] = true;
        if (array_key_exists('ms:edgeChromium', $capabilities)) {
            $profile['capabilities']['extra_capabilities']['ms:edgeChromium'] = $capabilities['ms:edgeChromium'];
        }

    } else
    if (array_key_exists('capabilities', $capabilities)) {
        $profile['capabilities']['extra_capabilities'] = $capabilities['capabilities'];
    }

    // Handle browserstack additional options.
    if (!empty($profile['capabilities']['extra_capabilities']['bstack:options'])) {
        if (!empty($profile['capabilities']['extra_capabilities']['bstack:options']['projectName'])) {
            $profile['capabilities']['name'] = $profile['capabilities']['extra_capabilities']['bstack:options']['projectName'];
        }
    }

    return $profile;
}

/**
 * Attempt to guess whether this Moodle install supports the W3C WebDriver protocols.
 *
 * @return  bool
 */
function supports_w3c(): bool {
    $relativedir = null;

    $backtrace = debug_backtrace(2);
    foreach ($backtrace as $params) {
        if (substr($params['file'], -11) === '/config.php') {
            $relativedir = dirname($params['file']);
            break;
        }
    }

    if ($relativedir === null) {
        throw new \Exception('Unable to determine the moodle dirroot');
    }

    $composerlock = "{$relativedir}/composer.lock";
    if (strpos(file_get_contents($composerlock), 'instaclick/php-webdriver') !== false) {
        return false;
    }

    return true;
}
