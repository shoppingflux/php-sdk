#!/usr/bin/env bash

# cd into project root deduce from script location
cd "$( dirname "${BASH_SOURCE[0]}" )"
cd ..

BUILD_DIR=build
RESOURCE_DIR=resources

if [ ! -d "vendor" ]; then
    echo -e "You must install composer dependencies before using this script\n"
    echo -e "Run : composer install --dev"
    exit 1
fi

# Run unit tests
vendor/bin/phpunit --configuration=phpunit.xml --coverage-clover=${BUILD_DIR}/phpunit/clover.xml --coverage-html=${BUILD_DIR}/phpunit/coverage/ --log-junit=${BUILD_DIR}/phpunit/log.junit

# Run code style checks
echo -e "\nRun checkstyles\n"
vendor/bin/phpcs --colors --report=full --standard=${RESOURCE_DIR}/phpcs/ruleset.xml --report-checkstyle=${BUILD_DIR}/phpcs/phpcs.xml --report-emacs=${BUILD_DIR}/phpcs/phpcs.log --extensions=php -p