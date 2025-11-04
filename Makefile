.PHONY: dev prod down check consume test clean-cache fixtures

dev:
	docker compose -f compose.yml --env-file .env.local up -d --wait

prod:
	docker compose -f compose.yml -f compose.override.yml --env-file .env.local up -d --wait

down:
	docker compose down -v

check:
	vendor/bin/phpstan analyse

consume:
	docker compose exec php bin/console messenger:consume async -vv

test:
	php bin/phpunit --testdox

clean-cache:
	php bin/console cache:clear --env=dev
	php bin/console cache:clear --env=test

fixtures:
	docker compose exec php bin/console doctrine:fixtures:load
