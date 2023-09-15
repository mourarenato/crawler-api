fileMode: ##Sets git fileMode to false
	@echo "Configuring git fileMode to false"
	git config core.fileMode false

files: ##@Copy files and set permissions
	sudo chmod 777 -R *
	sudo cp .env.example .env
	sudo cp docker-compose.example.yml docker-compose.yml

keys: ##Generate keys
	@echo "Generating keys"
	docker exec -it php-crawler-api bash -c "php artisan key:generate && php artisan jwt:secret"

start: ###Up containers
	docker-compose up -d

install: ##Install dependencies
	@echo "Installing dependencies"
	make files
	docker-compose up -d
	sudo chmod 777 -R vendor/

migrate: ##Run migrations
	@echo "Running migrations"
	docker exec -it php-crawler-api bash -c "php artisan migrate && php artisan migrate --database=testing"

migrate-reset: ##Reset migrations
	@echo "Reverting migrations"
	docker exec -it php-crawler-api bash -c "php artisan migrate:reset"

run-tests: ##Run unit tests
	docker exec -it php-crawler-api bash -c "php artisan test --no-coverage"

run-coverage: ##Run unit tests with coverage
	docker exec -it php-crawler-api bash -c "php artisan test"

restart: ##Restart server
	docker-compose down;docker-compose up -d
