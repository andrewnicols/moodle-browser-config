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


## Default driver URLs:

The following variables are configured by default for the various drivers.
```
    $CFG->behat_chromedriver_url = 'http://localhost:9515';
    $CFG->behat_geckodriver_url = 'http://localhost:4446';
    $CFG->behat_safaridriver_url = 'http://localhost:4447';
    $CFG->behat_edgedriver_url = 'http://localhost:4448';
    $CFG->behat_selenium_url = 'http://localhost:4444/wd/hub';
```

You can override these in your config.php, or by adding a config.php to this repo for all Moodle sites.
