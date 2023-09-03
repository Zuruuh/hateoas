set shell := ["bash", "-uc"]

vendor_bin := "./vendor/bin/"
phpunit_bin := vendor_bin + "phpunit"
phpstan_bin := vendor_bin + "phpstan"
psalm_bin := vendor_bin + "psalm"
phpcsfixer_bin := vendor_bin + "php-cs-fixer"

cache_dir:
    @-mkdir .cache 2> /dev/null

fix: cache_dir
    {{phpcsfixer_bin}} fix

lint: cache_dir
    {{phpcsfixer_bin}} fix --dry-run

phpstan: cache_dir
    {{phpstan_bin}} analyse

psalm: cache_dir
    {{psalm_bin}} --config psalm.dist.xml

analyze: analyse
analyse: phpstan psalm

phpunit *args: cache_dir
    {{phpunit_bin}} --configuration phpunit.dist.xml --coverage-html .cache/coverage --testdox {{args}}

test: phpunit
