set shell := ["bash", "-uc"]

tools := "./tools/vendor/bin/"
phpunit_bin := "./vendor/bin/phpunit --configuration phpunit.dist.xml"
phpstan_bin := tools + "phpstan analyse --configure ./tools/phpstan.dist.neon"
psalm_bin := tools + "psalm --config ./tools/psalm.dist.xml"
phpcsfixer_bin := tools + "php-cs-fixer fix --config ./tools/.php-cs-fixer.dist.php"

install: cache_dir
    composer install
    composer install --working-dir tools

cache_dir:
    @-mkdir .cache 2> /dev/null

fix:
    {{phpcsfixer_bin}}

lint:
    {{phpcsfixer_bin}} --dry-run

phpstan:
    {{phpstan_bin}}

psalm:
    {{psalm_bin}}

analyze: analyse
analyse: phpstan psalm

phpunit *args:
    {{phpunit_bin}} --coverage-html .cache/coverage --testdox {{args}}

test: phpunit
