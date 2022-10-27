# Docker
up:
	docker-compose up -d
down:
	docker-compose down
ps:
	docker-compose ps
sh:
	docker-compose exec php-fpm sh
restart:
	make down
	make up

# Migrations
make-migration:
	docker-compose exec php-fpm bin/console make:migration
migrate:
	docker-compose exec php-fpm bin/console doctrine:migrations:migrate
