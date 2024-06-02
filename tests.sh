#!/bin/bash

echo '----------------------------------------------------------------------'
echo ' Checking formatting with PSR12 Standard'
echo '----------------------------------------------------------------------'
vendor/bin/phpcs --colors -p app/ --standard=PSR12 app/ --extensions=php

echo '----------------------------------------------------------------------'
echo ' Checking for syntax and runtime errors with PHPStan'
echo '----------------------------------------------------------------------'
vendor/bin/phpstan analyse -l 5 -c tests/phpstan.neon .

echo '----------------------------------------------------------------------'
echo ' Checking for code quality with PHPMD'
echo '----------------------------------------------------------------------'
vendor/bin/phpmd app/ ansi tests/phpmd-ruleset.xml

