# vim: ts=4:sw=4:noexpandtab!:

PROJECT_BASEDIR = `if [ -d ../../../vendor ]; then echo $$(cd ../../../ && pwd); else echo $$(pwd); fi`

metrics:
	@if [ -d ./build/codebrowser ]; then rm -rf ./build/codebrowser; fi
	@mkdir -p ./build/codebrowser
	@mkdir -p ./build/logs

	@make test
	@$(PROJECT_BASEDIR)/vendor/bin/phpcs --extensions=php --report=checkstyle --report-file=./build/logs/checkstyle.xml --standard=psr2 ./src ./tests
	-@$(PROJECT_BASEDIR)/vendor/bin/phpcpd --log-pmd ./build/logs/pmd-cpd.xml src/
	-@$(PROJECT_BASEDIR)/vendor/bin/phpmd src/ xml codesize,design,naming,unusedcode --reportfile ./build/logs/pmd.xml
	-@$(PROJECT_BASEDIR)/vendor/bin/phpcb --log ./build/logs/ --source ./src --output ./build/codebrowser/

install: install-composer
	@php -d date.timezone="Europe/Berlin" ./bin/composer.phar -- install

update: install-composer
	@php -d date.timezone="Europe/Berlin" ./bin/composer.phar -- update
	@php -d date.timezone="Europe/Berlin" ./bin/composer.phar -- self-update

install-composer:
	@if [ ! -d ./bin ]; then mkdir bin; fi
	@if [ ! -f ./bin/composer.phar ]; then curl -s http://getcomposer.org/installer | php -d date.timezone="Europe/Berlin" -- --install-dir=./bin/; fi

api-doc:
	@if [ -d ./build/docs ]; then rm -rf ./build/docs; fi
	@php $(PROJECT_BASEDIR)/vendor/bin/sami.php update ./config/sami.php

code-sniffer:
	-@$(PROJECT_BASEDIR)/vendor/bin/phpcs --extensions=php --standard=psr2 ./src/ ./tests

mess-detection:
	@$(PROJECT_BASEDIR)/vendor/bin/phpmd src/ text codesize,design,naming,unusedcode

cp-detection:
	@$(PROJECT_BASEDIR)/vendor/bin/phpcpd --log-pmd ./build/logs/pmd-cpd.xml src/

test:
	@$(PROJECT_BASEDIR)/vendor/bin/phpunit --bootstrap ./tests/bootstrap.php --colors --no-configuration tests

.PHONY: test api-doc install install-composer update metrics code-sniffer mess-detection cp-detection
