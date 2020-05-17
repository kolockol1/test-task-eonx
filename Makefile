init: docker-down-clear docker-pull docker-build docker-up composer-install
up: docker-up composer-install
down: docker-down
restart: down up composer-install

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-pull:
	docker-compose pull

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-build:
	docker-compose build

composer-install:
	docker-compose run --rm api-php-cli composer install