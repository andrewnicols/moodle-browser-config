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
 * Behat Browser Configuration Manager class.
 *
 *
 * @package    test
 * @copyright  2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace AndrewNicols\Behat;

class ProfileManager {

    /**
     * Setup the Behat Profile Manager.
     */
    public function __construct() {
        global $CFG;

        if (!property_exists($CFG, 'behat_profiles')) {
            $CFG->behat_profiles = [];
        }

        $this->loadLocalConfiguration();
    }

    /**
     * Load local configuration
     */
    protected function loadLocalConfiguration() {
        global $CFG;
        $browserconfigfilepath = __DIR__ . '/../config.php';
        if (file_exists($browserconfigfilepath)) {
            require_once($browserconfigfilepath);
        }
    }

    /**
     * Add the standard profiles.
     */
    public function addStandardProfiles() {
        global $CFG;

        if (!property_exists($CFG, 'behat_profiles')) {
            $CFG->behat_profiles = [];
        }

        $CFG->behat_profiles = array_merge(
            $this->getStandardProfiles(),
            $CFG->behat_profiles
        );
    }

    /**
     * Add any desired custom profiles.
     */
    public function addCustomProfiles() {
        global $CFG;

        $profiles = [];

        $localprofiles = __DIR__ . '/../localprofiles.php';
        if (file_exists($localprofiles)) {
            $profiles = include($localprofiles);
        }

        if (!is_array($profiles)) {
            throw new \Exception("Invalid return type when loading custom profiles");
        }

        $CFG->behat_profiles = array_merge(
            $CFG->behat_profiles,
            $profiles
        );
    }

    /**
     * Get standard Behat profiles.
     *
     * @return  array
     */
    public function getStandardProfiles(): array {
        global $CFG;

        $w3c = self::supportsW3C();

        $profiles = array_merge(
            $this->getStandardChromeProfiles($w3c),
            $this->getStandardFirefoxProfiles($w3c),
            $this->getStandardEdgeProfiles($w3c),
            $this->getStandardSafariProfiles($w3c),
            $this->getStandardBrowserStackProfiles($w3c)
        );

        return $profiles;
    }

    /**
     * Get the Selenium URL.
     *
     * @return  string
     */
    public static function getSeleniumUrl(): string {
        global $CFG;

        if (property_exists($CFG, 'behat_selenium_url')) {
            return $CFG->behat_selenium_url;
        }

        // Return the default selenium URL.
        return 'http://localhost:4444/wd/hub';
    }

    /**
     * Get the chromedriver URL.
     *
     * @return  string
     */
    public static function getChromedriverUrl(): string {
        global $CFG;

        if (property_exists($CFG, 'behat_chromedriver_url')) {
            return $CFG->behat_chromedriver_url;
        }

        // Return the default URL.
        return 'http://localhost:9515';
    }

    /**
     * Get the geckodriver URL.
     *
     * @return  string
     */
    public static function getGeckodriverUrl(): string {
        global $CFG;

        if (property_exists($CFG, 'behat_geckodriver_url')) {
            return $CFG->behat_geckodriver_url;
        }

        // Return the default URL.
        return 'http://localhost:4444';
    }

    /**
     * Get the edgedriver URL.
     *
     * @return  string
     */
    public static function getEdgedriverUrl(): string {
        global $CFG;

        if (property_exists($CFG, 'behat_edgedriver_url')) {
            return $CFG->behat_edgedriver_url;
        }

        // Return the default URL.
        return 'http://localhost:4444';
    }

    /**
     * Get the path to the MS Edge Binary.
     *
     * This is required for some variants of edge, notably the Dev channels.
     *
     * @return  null|string
     */
    public static function getEdgeBinaryPath(): ?string {
        global $CFG;

        if (property_exists($CFG, 'behat_edge_binary')) {
            return $CFG->behat_edge_binary;
        }

        return null;
    }

    /**
     * Get the safaridriver URL.
     *
     * @return  string
     */
    public static function getSafaridriverUrl(): string {
        global $CFG;

        if (property_exists($CFG, 'behat_safaridriver_url')) {
            return $CFG->behat_safaridriver_url;
        }

        // Return the default URL.
        return 'http://localhost:4444';
    }

    /**
     * Get the BrowserStack URL.
     *
     * @return  string
     */
    public static function getBrowserStackUrl(): ?string {
        global $CFG;

        if (property_exists($CFG, 'behat_browserstack_url')) {
            return $CFG->behat_browserstack_url;
        }

        return null;
    }

    /**
     * Get the standard Chrome profiles for instantiation via both Selenium, and chromedriver.
     *
     * @param   bool $w3c
     * @return  array
     */
    public function getStandardChromeProfiles(bool $w3c): array {
        return [
            // Google Chrome using Chromedriver.
            'chromedriver' => self::getBrowserProfile(
                'chrome',
                self::getChromedriverUrl(),
                $w3c
            ),
            'headlesschromedriver' => self::getBrowserProfile(
                'chrome',
                self::getChromedriverUrl(),
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
            'chrome' => self::getBrowserProfile(
                'chrome',
                self::getSeleniumUrl(),
                $w3c
            ),
            'headlesschrome' => self::getBrowserProfile(
                'chrome',
                self::getSeleniumUrl(),
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
        ];
    }

    /**
     * Get the standard Firefox profiles for instantiation via both Selenium, and geckodriver.
     *
     * @param   bool $w3c
     * @return  array
     */
    public function getStandardFirefoxProfiles(bool $w3c): array {
        return [
            // Mozilla Firefox using Geckodriver.
            'gecko' => self::getBrowserProfile(
                'firefox',
                self::getGeckodriverUrl(),
                $w3c
            ),
            'headlessgecko' => self::getBrowserProfile(
                'firefox',
                self::getGeckodriverUrl(),
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
            'firefox' => self::getBrowserProfile(
                'firefox',
                self::getSeleniumUrl(),
                $w3c
            ),
            'headlessfirefox' => self::getBrowserProfile(
                'firefox',
                self::getSeleniumUrl(),
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
    }

    /**
     * Get the standard MS Edge profiles for instantiation via both Selenium, and edgedriver.
     *
     * @param   bool $w3c
     * @return  array
     */
    public function getStandardEdgeProfiles(bool $w3c): array {
        return [
            // Microsoft Edge.
            'edgedriver' => self::getBrowserProfile(
                'edge',
                self::getEdgedriverUrl(),
                $w3c
            ),
            'headlessedgedriver' => self::getBrowserProfile(
                'edge',
                self::getEdgedriverUrl(),
                $w3c,
                [
                    'ms:edgeOptions' => [
                        'args' => [
                            'headless',
                            'no-gpu',
                        ],
                    ],
                ]
            ),

            // Note: There is currently some configuration issue when running Edge through selenium.
            'edge' => self::getBrowserProfile(
                'edge',
                self::getSeleniumUrl(),
                $w3c
            ),
        ];
    }

    /**
     * Get the standard Safari profiles for instantiation via both Selenium, and safaridriver.
     *
     * @param   bool $w3c
     * @return  array
     */
    public function getStandardSafariProfiles(bool $w3c): array {
        return [
            'safaridriver' => self::getBrowserProfile(
                'safari',
                self::getSafaridriverUrl(),
                $w3c
            ),
        ];
    }

    /**
     * Get the standard BrowserStack profiles for instantiation via BrowserStack.
     *
     * @param   bool $w3c
     * @return  array
     */
    public function getStandardBrowserStackProfiles(bool $w3c): array {
        // A small selection of Browserstack browsers to gives an example of how these can be used.
        $browserstackUrl = self::getBrowserStackUrl();
        if (!$browserstackUrl) {
            return [];
        }

        return [
            'bs_osx_safari' => self::getBrowserProfile(
                'safari',
                $browserstackUrl,
                $w3c,
                [
                    'bstack:options' => [
                        'os' => 'OS X',
                        'osVersion' => 'Big Sur',
                        'local' => true,
                    ],
                    'capabilities' => [
                        'browserName' => 'Safari',
                    ],
                ]
            ),

            'bs_win_edge' => self::getBrowserProfile(
                'edge',
                $browserstackUrl,
                $w3c,
                [
                    'bstack:options' => [
                        'os' => 'Windows',
                        'osVersion' => '10',
                        'local' => true,
                    ],
                    'capabilities' => [
                        'browserName' => 'Edge',
                        'browserVersion' => '87.0',
                    ],
                ]
            ),
        ];
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
    public function addBrowserProfile(
        string $profileName,
        string $browserName,
        string $wdhost,
        bool $w3c = true,
        array $capabilities = []
    ) {
        global $CFG;

        $CFG->behat_profiles[$profileName] = self::getBrowserProfile($browserName, $wdhost, $w3c, $capabilities);
    }

    /**
     * Get a browser profile, translating known configuration parameters where possible.
     *
     * @param   string $browserName
     * @param   string $wdhost
     * @param   bool $w3c
     * @param   array $capabilities
     * @return  array
     */
    public static function getBrowserProfile(
        string $browserName,
        string $wdhost,
        bool $w3c = true,
        array $capabilities = []
    ): array {
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

            if ($binaryPath = self::getEdgeBinaryPath()) {
                $defaultcapabilities['ms:edgeOptions']['binary'] = $binaryPath;
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
        if (array_key_exists('bstack:options', $capabilities)) {
            $profile['capabilities']['extra_capabilities']['bstack:options'] = $capabilities['bstack:options'];
        }

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
    public static function supportsW3C(): bool {
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

    /**
     * Whether this call is made as part of a behat CLI call.
     *
     * @return  bool
     */
    public static function isBehatCliUsage(): bool {
        if (defined('BEHAT_TEST') && BEHAT_TEST) {
            // BEHAT_TEST is set when running Behat via CLI.
            return true;
        }

        // Determine if one of the behat CLI scripts is in use.
        $backtrace = debug_backtrace(2);
        foreach ($backtrace as $params) {
            if (strpos($params['file'], '/admin/tool/behat/cli/') !== false) {
                return true;
            }
        }

        return false;
    }
}
