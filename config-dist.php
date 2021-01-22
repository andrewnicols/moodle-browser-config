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

return (object) [

    //
    // Configuration for Selenium.
    //
    // A default value for the selenium URL:
    //  'seleniumUrl' => 'http://localhost:4444/wd/hub',

    //
    // Configuration for chromedriver.
    // Chromedriver is the driver used for Google Chrome.
    //
    // A default value for the chromedriver URL:
    //  'chromedriverUrl' => 'http://localhost:9515',
    //
    // You can also specify a binary to use.
    //  'chromeBinaryPath' => '',

    //
    // Configuration for geckodriver.
    // Geckodriver is the driver used for Mozilla Firefox.
    //
    // A default value for the geckodriver URL:
    //  'geckodriverUrl' => 'http://localhost:4444',
    //
    // You can also specify a binary to use.
    //  'firefoxBinaryPath' => '',

    //
    // Configuration for safaridriver.
    // Safaridriver is the driver used for Apple's Safari Browser.
    //
    // A default value for the safaridriver URL:
    //  'safaridriverUrl' => 'http://localhost:4444',

    //
    // Configuration for edgedriver.
    // Edgedriver is the driver used for the non-legacy MS Edge.
    //
    // A default value for the edgedriver URL:
    //  'edgedriverUrl' => 'http://localhost:9515',
    //
    // You can also specify a binary to use for Microsoft Edge.
    //  'edgeBinaryPath' => '',

    //
    // Browserstack Configuration
    //
    // You can provide a username and password for browserstack.
    // These will be used to generate the correct browserstack URL.
    //  'browserstackUsername' => 'myusername';
    //  'browserstackPassword' => 'myautomationkey';
    //
    // Alternatively you can specify a full Browserstack Webdriver URL.
    //  'browserstackUrl' => "https://USERNAME:KEY@hub-cloud.browserstack.com/wd/hub";
    //
    // If both the username/password, and the url are provided, then only the url is used.
];
