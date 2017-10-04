# Behat Placeholders Extension
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ciandt-dev/behat-placeholders-extension/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ciandt-dev/behat-placeholders-extension/?branch=master) [![Travis CI](https://travis-ci.org/ciandt-dev/behat-placeholders-extension.svg?branch=master)](https://travis-ci.org/ciandt-dev/behat-placeholders-extension)


A Behat extension to run scenarios with different parameters per environment and/or per application variant.

### Installing

Install with composer:
```
composer require ciandt/behat-placeholders-extension
```

## Getting Started


Set up on behat.yml:
```
default:
  extensions:
    Ciandt\Behat\PlaceholdersExtension:
      config_tags:
        foo: %paths.base%/features/foo.yml
```

Create your replacements file (features/foo.yml):
```
default:
  placeholders:
    my_placeholder: 'My replacement'
```

Use your placeholder on your features, wrapping it with ${}:
```
Scenario: Echo the value
    Given I echo "${my_placeholder}"
```

On runtime, **${my_placeholder}** will be replaced by **My replacement**


## More info

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/your/project/tags). 
We also keep [a changelog](CHANGELOG.md) based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)


## Authors

* **Bruno Wowk** - *Lead developer* - [bwowk](https://github.com/bwowk)

See also the list of [contributors](https://github.com/ciandt-dev/behat-placeholders-extension/contributors) who participated in this project.

## License

This project is licensed under the GPL-3.0 License - see the [LICENSE.md](LICENSE.md) file for details

