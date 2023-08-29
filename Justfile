set shell := ["bash", "-uc"]

phpunit_bin := "vendor/bin/phpunit"
phpstan_bin := "vendor/bin/phpstan"
phpcsfixer_bin := "vendor/bin/php-cs-fixer"

cache_dir:
    @-mkdir .cache 2> /dev/null

fix: cache_dir
    {{phpcsfixer_bin}} fix

lint: cache_dir
    {{phpcsfixer_bin}} fix --dry-run

phpstan: cache_dir
    {{phpstan_bin}} analyse

phpunit *args: cache_dir
    {{phpunit_bin}} --configuration phpunit.dist.xml --coverage-html .cache/coverage --testdox {{args}}

test: phpunit
