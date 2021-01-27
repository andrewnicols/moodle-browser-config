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
     * @var object Local configuration for the Browser Config tool.
     */
    protected static $config;

    /**
     * Setup the Behat Profile Manager.
     */
    public function __construct() {
        global $CFG;

        if (!property_exists($CFG, 'behat_profiles')) {
            $CFG->behat_profiles = [];
        }

        self::$config = (object) [];
        self::loadLocalConfiguration();
    }

    /**
     * Load local configuration
     */
    protected static function loadLocalConfiguration() {
        $browserconfigfilepath = __DIR__ . '/../config.php';
        if (file_exists($browserconfigfilepath)) {
            $config = require($browserconfigfilepath);
            if (is_object($config)) {
                self::$config = $config;
            }
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

    protected static function getConfig(string $key, $default = null) {
        if (self::$config === null) {
            return $default;
        }

        if (property_exists(self::$config, $key)) {
            return self::$config->{$key};
        }

        return $default;
    }

    /**
     * Get the Selenium URL.
     *
     * @return  string
     */
    public static function getSeleniumUrl(): string {
        return self::getConfig('seleniumUrl', 'http://localhost:4444/wd/hub');
    }

    /**
     * Get the chromedriver URL.
     *
     * @return  string
     */
    public static function getChromedriverUrl(): string {
        return self::getConfig('chromedriverUrl', 'http://localhost:9515');
    }

    /**
     * Get the path to the Chrome Binary.
     *
     * @return  null|string
     */
    public static function getChromeBinaryPath(): ?string {
        return self::getConfig('chromeBinaryPath');
    }

    /**
     * Get the geckodriver URL.
     *
     * @return  string
     */
    public static function getGeckodriverUrl(): string {
        return self::getConfig('geckodriverUrl', 'http://localhost:4444');
    }

    /**
     * Get the path to the Firefox Binary.
     *
     * @return  null|string
     */
    public static function getFirefoxBinaryPath(): ?string {
        return self::getConfig('firefoxBinaryPath');
    }

    /**
     * Get the edgedriver URL.
     *
     * @return  string
     */
    public static function getEdgedriverUrl(): string {
        return self::getConfig('edgedriverUrl', 'http://localhost:9515');
    }

    /**
     * Get the path to the MS Edge Binary.
     *
     * This is required for some variants of edge, notably the Dev channels.
     *
     * @return  null|string
     */
    public static function getEdgeBinaryPath(): ?string {
        return self::getConfig('edgeBinaryPath');
    }

    /**
     * Get the safaridriver URL.
     *
     * @return  string
     */
    public static function getSafaridriverUrl(): string {
        return self::getConfig('safaridriverUrl', 'http://localhost:4444');
    }

    /**
     * Get the BrowserStack URL.
     *
     * @return  string
     */
    public static function getBrowserStackUrl(): ?string {
        global $CFG;

        if ($browserstackUrl = self::getConfig('browserstackUrl')) {
            return $browserstackUrl;
        }

        $username = self::getConfig('browserstackUsername');
        $password = self::getConfig('browserstackPassword');

        if (empty($username) || empty($password)) {
            return null;
        }

        return "https://{$username}:{$password}@hub-cloud.browserstack.com/wd/hub";
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
            'geckodriver' => self::getBrowserProfile(
                'firefox',
                self::getGeckodriverUrl(),
                $w3c
            ),
            'headlessgeckodriver' => self::getBrowserProfile(
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
            'headlessedge' => self::getBrowserProfile(
                'edge',
                self::getSeleniumUrl(),
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
            $capabilities = self::processChromeBrowserProfile($browserName, $wdhost, $w3c, $capabilities);
        }
        else if ($browserName === 'firefox') {
            $capabilities = self::processFirefoxBrowserProfile($browserName, $wdhost, $w3c, $capabilities);
        }
        else if ($browserName === 'edge') {
            $capabilities = self::processEdgeBrowserProfile($browserName, $wdhost, $w3c, $capabilities);
        }

        $profile = [];
        $profile['browser'] = $browserName;
        $profile['capabilities'] = [
            'extra_capabilities' => [],
        ];

        $profile['wd_host'] = $wdhost;

        [
            'capabilities' => $capabilities,
            'profile' => $profile,
        ] = self::processChromeOptions($profile, $capabilities, $w3c);
        [
            'capabilities' => $capabilities,
            'profile' => $profile,
        ] = self::processFirefoxOptions($profile, $capabilities, $w3c);
        [
            'capabilities' => $capabilities,
            'profile' => $profile,
        ] = self::processSafariOptions($profile, $capabilities, $w3c);
        [
            'capabilities' => $capabilities,
            'profile' => $profile,
        ] = self::processEdgeOptions($profile, $capabilities, $w3c);
        [
            'capabilities' => $capabilities,
            'profile' => $profile,
        ] = self::processBrowserstackOptions($profile, $capabilities, $w3c);

        if (array_key_exists('capabilities', $capabilities)) {
            $profile['capabilities']['extra_capabilities'] = array_merge_recursive(
                $profile['capabilities']['extra_capabilities'],
                $capabilities['capabilities']
            );
        }

        return $profile;
    }

    /**
     * Process the profile for Chrome, filling in defaults as required.
     *
     * @param   string $browserName
     * @param   string $wdhost
     * @param   bool $w3c
     * @param   array $capabilities
     * @return  array
     */
    protected static function processChromeBrowserProfile(
        string $browserName,
        string $wdhost,
        bool $w3c = true,
        array $capabilities = []
    ): array {
        $defaultcapabilities = [
            'args' => [
                'no-sandbox',
            ],
        ];

        if ($binaryPath = self::getChromeBinaryPath()) {
            $defaultcapabilities['chromeOptions']['binary'] = $binaryPath;
        }

        return array_merge_recursive(
            $defaultcapabilities,
            $capabilities
        );
    }

    /**
     * Process the Options for Chrome, translating known configuration parameters wher epossible.
     *
     * @param   array $profile
     * @param   array $capabilities
     * @param   bool $w3c
     * @return  array The modified $capabilities
     */
    protected static function processChromeOptions(array $profile, array $capabilities, bool $w3c): array {
        if (!array_key_exists('chromeOptions', $capabilities)) {
            return [
                'capabilities' => $capabilities,
                'profile' => $profile,
            ];
        }

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

        unset($capabilities['chromeOptions']);
        return [
            'capabilities' => $capabilities,
            'profile' => $profile,
        ];
    }

    /**
     * Process the profile for Firefox, filling in defaults as required.
     *
     * @param   string $browserName
     * @param   string $wdhost
     * @param   bool $w3c
     * @param   array $capabilities
     * @return  array
     */
    protected static function processFirefoxBrowserProfile(
        string $browserName,
        string $wdhost,
        bool $w3c = true,
        array $capabilities = []
    ): array {
        $defaultcapabilities = [
            'moz:firefoxOptions' => [
                'prefs' => [
                    'devtools.console.stdout.content' => true,
                ],
                'log' => [
                    'level' => 'trace',
                ],
            ],
        ];

        if ($binaryPath = self::getFirefoxBinaryPath()) {
            $defaultcapabilities['moz:firefoxOptions']['binary'] = $binaryPath;
        }

        return array_merge_recursive(
            $defaultcapabilities,
            $capabilities
        );
    }

    /**
     * Process the Options for Chrome, translating known configuration parameters wher epossible.
     *
     * @param   array $profile
     * @param   array $capabilities
     * @param   bool $w3c
     * @return  array The modified $capabilities
     */
    protected static function processFirefoxOptions(array $profile, array $capabilities, bool $w3c): array {
        if (!array_key_exists('moz:firefoxOptions', $capabilities)) {
            return [
                'capabilities' => $capabilities,
                'profile' => $profile,
            ];
        }

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

        unset($capabilities['moz:firefoxOptions']);
        return [
            'capabilities' => $capabilities,
            'profile' => $profile,
        ];
    }

    /**
     * Process the Options for Safari, translating known configuration parameters wher epossible.
     *
     * @param   array $profile
     * @param   array $capabilities
     * @param   bool $w3c
     * @return  array The modified $capabilities
     */
    protected static function processSafariOptions(array $profile, array $capabilities, bool $w3c): array {
        if (!array_key_exists('safari:options', $capabilities)) {
            return [
                'capabilities' => $capabilities,
                'profile' => $profile,
            ];
        }

        $browserOptions = $capabilities['safari:options'];
        $profile['capabilities']['extra_capabilities']['safari:options'] = $browserOptions;

        unset($capabilities['safari:options']);
        return [
            'capabilities' => $capabilities,
            'profile' => $profile,
        ];
    }

    /**
     * Process the profile for Edge, filling in defaults as required.
     *
     * @param   string $browserName
     * @param   string $wdhost
     * @param   bool $w3c
     * @param   array $capabilities
     * @return  array
     */
    protected static function processEdgeBrowserProfile(
        string $browserName,
        string $wdhost,
        bool $w3c = true,
        array $capabilities = []
    ): array {
        $defaultcapabilities = [
            'ms:edgeOptions' => [],
            'ms:edgeChromium' => true,
        ];

        if ($binaryPath = self::getEdgeBinaryPath()) {
            $defaultcapabilities['ms:edgeOptions']['binary'] = $binaryPath;
        }

        return array_merge_recursive(
            $defaultcapabilities,
            $capabilities
        );
    }

    /**
     * Process the Options for Edge, translating known configuration parameters wher epossible.
     *
     * @param   array $profile
     * @param   array $capabilities
     * @param   bool $w3c
     * @return  array The modified $capabilities
     */
    protected static function processEdgeOptions(array $profile, array $capabilities, bool $w3c): array {
        if (!array_key_exists('ms:edgeOptions', $capabilities)) {
            return [
                'capabilities' => $capabilities,
                'profile' => $profile,
            ];
        }
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

        $profile['capabilities']['extra_capabilities']['browserName'] = 'MicrosoftEdge';
        $profile['capabilities']['extra_capabilities']['ms:edgeOptions'] = $browserOptions;
        $profile['capabilities']['extra_capabilities']['ms:edgeChromium'] = true;
        if (array_key_exists('ms:edgeChromium', $capabilities)) {
            $profile['capabilities']['extra_capabilities']['ms:edgeChromium'] = $capabilities['ms:edgeChromium'];
        }


        unset($capabilities['ms:edgeOptions']);
        return [
            'capabilities' => $capabilities,
            'profile' => $profile,
        ];
    }

    /**
     * Process the Options for BrowserStack, translating known configuration parameters wher epossible.
     *
     * @param   array $profile
     * @param   array $capabilities
     * @param   bool $w3c
     * @return  array The modified $capabilities
     */
    protected static function processBrowserstackOptions(array $profile, array $capabilities, bool $w3c): array {
        // Handle browserstack additional options.
        if (array_key_exists('bstack:options', $capabilities)) {
            $profile['capabilities']['extra_capabilities']['bstack:options'] = $capabilities['bstack:options'];

            // Unset binary paths.
            unset($profile['capabilities']['extra_capabilities']['chromeOptions']['binary']);
            unset($profile['capabilities']['extra_capabilities']['moz:firefoxOptions']['binary']);
            unset($profile['capabilities']['extra_capabilities']['ms:edgeOptions']['binary']);
        }

        if (!empty($profile['capabilities']['extra_capabilities']['bstack:options'])) {
            if (!empty($profile['capabilities']['extra_capabilities']['bstack:options']['projectName'])) {
                $profile['capabilities']['name'] = $profile['capabilities']['extra_capabilities']['bstack:options']['projectName'];
            }
        }

        unset($capabilities['bstack:options']);
        return [
            'capabilities' => $capabilities,
            'profile' => $profile,
        ];
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
            if (substr(str_replace('\\', '/', $params['file']), -11) === '/config.php') {
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
            if (strpos(str_replace('\\', '/', $params['file']), '/admin/tool/behat/cli/') !== false) {
                return true;
            }
        }

        return false;
    }
}
