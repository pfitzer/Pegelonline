[![Build Status](https://travis-ci.org/pfitzer/Pegelonline.svg?branch=master)](https://travis-ci.org/pfitzer/Pegelonline) [![codecov](https://codecov.io/gh/pfitzer/Pegelonline/branch/master/graph/badge.svg)](https://codecov.io/gh/pfitzer/Pegelonline)

##PHP Class to use Pegelonline Rest-Api
For more information see [pegelonline.wsv.de](https://www.pegelonline.wsv.de)

install
-------
composer require pfitzer/pegelonline
```
"require": {
    "pfitzer/pegelonline": "*"
}
```
usage
-----
```
$pgOnline = new Pegelonline();

# get all waters with stations
try {
    $result = $pgonline->getWaters(true);
} catch (\InvalidArgumentException $e) {
    # do something
}
```

contribute
----------
feel free to do so

#### install dev dependencies
```
composer install
```
#### run unit tests
```
phpunit --configuration phpunit.xml