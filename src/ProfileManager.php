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

        $localprofiles = __DIR__ . '/../localprofiles.php';
        if (file_exists($localprofiles)) {
            require_once($localprofiles);
        }
    }

    /**
     * Get standard Behat profiles.
     *
     * @return  array
     */
    public function getStandardProfiles(): array {
        global $CFG;

        $w3c = $this->supportsW3C();

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
    protected function getSeleniumUrl(): string {
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
    protected function getChromedriverUrl(): string {
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
    protected function getGeckodriverUrl(): string {
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
    protected function getEdgedriverUrl(): string {
        global $CFG;

        if (property_exists($CFG, 'behat_edgedriver_url')) {
            return $CFG->behat_edgedriver_url;
        }

        // Return the default URL.
        return 'http://localhost:4444';
    }

    /**
     * Get the safaridriver URL.
     *
     * @return  string
     */
    protected function getSafaridriverUrl(): string {
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
    protected function getBrowserStackUrl(): ?string {
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
    protected function getStandardChromeProfiles(bool $w3c): array {
        return [
            // Google Chrome using Chromedriver.
            'chromedriver' => $this->getBrowserProfile(
                'chrome',
                $this->getChromedriverUrl(),
                $w3c
            ),
            'headlesschromedriver' => $this->getBrowserProfile(
                'chrome',
                $this->getChromedriverUrl(),
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
            'chrome' => $this->getBrowserProfile(
                'chrome',
                $this->getSeleniumUrl(),
                $w3c
            ),
            'headlesschrome' => $this->getBrowserProfile(
                'chrome',
                $this->getSeleniumUrl(),
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
    protected function getStandardFirefoxProfiles(bool $w3c): array {
        return [
            // Mozilla Firefox using Geckodriver.
            'gecko' => $this->getBrowserProfile(
                'firefox',
                $this->getGeckodriverUrl(),
                $w3c
            ),
            'headlessgecko' => $this->getBrowserProfile(
                'firefox',
                $this->getGeckodriverUrl(),
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
            'firefox' => $this->getBrowserProfile(
                'firefox',
                $this->getSeleniumUrl(),
                $w3c
            ),
            'headlessfirefox' => $this->getBrowserProfile(
                'firefox',
                $this->getSeleniumUrl(),
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
    protected function getStandardEdgeProfiles(bool $w3c): array {
        return [
            // Microsoft Edge.
            'edgedriver' => $this->getBrowserProfile(
                'edge',
                $this->getEdgedriverUrl(),
                $w3c
            ),
            'headlessedgedriver' => $this->getBrowserProfile(
                'edge',
                $this->getEdgedriverUrl(),
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
            'edge' => $this->getBrowserProfile(
                'edge',
                $this->getSeleniumUrl(),
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
    protected function getStandardSafariProfiles(bool $w3c): array {
        return [
            'safaridriver' => $this->getBrowserProfile(
                'safari',
                $this->getSafaridriverUrl(),
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
    protected function getStandardBrowserStackProfiles(bool $w3c): array {
        // A small selection of Browserstack browsers to gives an example of how these can be used.
        $browserstackUrl = $this->getBrowserStackUrl();
        if (!$browserstackUrl) {
            return [];
        }

        return [
            'bs_osx_safari' => $this->getBrowserProfile(
                'safari',
                $browserstackUrl,
                $w3c,
                [
                    'os' => 'OS X',
                    'os_version' => 'Big Sur',
                    'browser' => 'Safari',
                    'browserstack.local' => true,
                ]
            ),

            'bs_win_edge' => $this->getBrowserProfile(
                'edge',
                $browserstackUrl,
                $w3c,
                [
                    'os' => 'Windows',
                    'os_version' => '10',
                    'browser' => 'Edge',
                    'browser_version' => '88.0 beta',
                    'browserstack.local' => true,
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

        $CFG->behat_profiles[$profileName] = $this->getBrowserProfile($browserName, $wdhost, $w3c, $capabilities);
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
    public function getBrowserProfile(
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
    public function supportsW3C(): bool {
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
     * Whether this call is made as part of behat setup.
     *
     * @return  bool
     */
    public static function isBehatSetup(): bool {
        $backtrace = debug_backtrace(2);
        foreach ($backtrace as $params) {
            if (strpos($params['file'], '/admin/tool/behat/cli/') !== false) {
                return true;
            }
        }

        return false;
    }
}
