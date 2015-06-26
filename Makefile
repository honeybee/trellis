# vim: ts=4:sw=4:noexpandtab!:

PROJECT_BASEDIR = `if [ -d ../../../vendor ]; then echo $$(cd ../../../ && pwd); else echo $$(pwd); fi`

build:
	@if [ -d ./build/logs ]; then rm -rf ./build/logs; fi
	@mkdir -p ./build/logs
	@make test
	@$(PROJECT_BASEDIR)/vendor/bin/phpcs --extensions=php --report=checkstyle --report-file=./build/logs/checkstyle.xml --standard=psr2 ./src ./tests

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

test:
	@$(PROJECT_BASEDIR)/vendor/bin/phpunit --colors tests

.PHONY: test api-doc install install-composer update build code-sniffer
