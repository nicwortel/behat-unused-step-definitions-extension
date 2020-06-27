check: vendor
	vendor/bin/phpstan analyse
	vendor/bin/phpunit --testdox
	vendor/bin/phpcs
	composer validate

vendor: composer.lock
	composer install

composer.lock: composer.json
	composer update
