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

Sensible defaults are provided for all drivers. These can be configured, either in the Moodle `config.php` for a
specific installation of Moodle, or in the `config.php` file located alongside the `init.php` file for this helper.

The following default URLs are used for the various WebDriver variants:

|| Driver       || Default URL                   || Configuration variable        ||
|| Selenium     | `http://localhost:4444/wd/hub` | `$CFG->behat_selenium_url`     |
|| Chrome       | `http://localhost:9515`        | `$CFG->behat_chromedriver_url` |
|| Firefox      | `http://localhost:4444`        | `$CFG->behat_geckodriver_url`  |
|| Safari       | `http://localhost:4444`        | `$CFG->behat_safaridriver_url` |
|| Edge         | `http://localhost:4444`        | `$CFG->behat_edgedriver_url`   |
|| BrowserStack | [Not set]                      | `$CFG->behat_browserstack_url` |

An example configuration is available in `config-dist.php`.

You can also specify additional profiles by creating a `localprofiles.php` file alognside the `init.php` file. See the
`examples` folder for some examples of these.
