DOCKER_COMPOSE = sudo docker-compose
EXEC           = $(DOCKER_COMPOSE) exec
EXEC_PHP       = $(DOCKER_COMPOSE) exec -T php
SYMFONY        = $(EXEC_PHP) bin/console
COMPOSER       = $(EXEC_PHP) composer
RUN       = $(DOCKER_COMPOSE) run

##
## Project
## -------
##

build:
	#$(DOCKER_COMPOSE) pull
	#@$(DOCKER_COMPOSE) pull --parallel --quiet --ignore-pull-failures 2> /dev/null
	$(DOCKER_COMPOSE) build --pull

install: build start vendor db webpack
#install: build start vendor

start:
	$(DOCKER_COMPOSE) up --build --remove-orphans --force-recreate --detach

stop:
	$(DOCKER_COMPOSE) stop

kill:
	$(DOCKER_COMPOSE) kill
	$(DOCKER_COMPOSE) down --volumes --remove-orphans

clean: kill
	rm -rf var vendor

reinstall: clean install

reload:
	rm -rf var/cache/dev/* var/logs/dev/*
	bin/console cache:clear --env=dev
	bin/console doctrine:database:drop --force  --env=dev
	bin/console doctrine:database:create --env=dev
	bin/console doctrine:migrations:migrate --no-interaction --env=dev
	bin/console doctrine:fixtures:load --no-interaction --env=dev

.PHONY: build kill install reset start stop clean reinstall reload



##
## Utils
## -----
##

webpack:
	$(RUN) node yarn install
	$(RUN) node yarn add vue@^2.5 vue-loader@^15 vue-template-compiler --dev
	$(RUN) node yarn add bootstrap-vue bootstrap
	$(RUN) node yarn encore dev
	$(RUN) node yarn encore production
.PHONY: build


db: ## Reset the database and load fixtures
db: vendor
	$(SYMFONY) doctrine:database:drop --if-exists --force
	$(SYMFONY) doctrine:database:create --if-not-exists
	$(SYMFONY) doctrine:migrations:migrate --no-interaction --allow-no-migration
	$(SYMFONY) doctrine:fixtures:load --no-interaction

migration: ## Generate a new doctrine migration
migration: vendor
	$(SYMFONY) doctrine:migrations:diff

db-validate-schema: ## Validate the doctrine ORM mapping
db-validate-schema: vendor
	$(SYMFONY) doctrine:schema:validate

.PHONY: db migration watch


# rules based on files

#composer.lock: composer.json
#	$(COMPOSER) update --lock --no-scripts --no-interaction

vendor:
	$(COMPOSER) install --profile --verbose

.PHONY: vendor

ci: ## Run all quality insurance checks (tests, code styles, linting, security, static analysis...)
#ci: php-cs-fixer phpcs phpmd phpmnd phpstan psalm lint validate-composer validate-mapping security test test-coverage test-spec
ci: test test-coverage validate-composer validate-mapping

test: ## Run phpunit tests
test:
	$(EXEC_PHP) bin/phpunit

test-coverage: ## Run phpunit tests with code coverage (phpdbg)
test-coverage: test-coverage-pcov

test-coverage-pcov: ## Run phpunit tests with code coverage (pcov - uncomment extension in dockerfile)
test-coverage-pcov:
	$(EXEC_PHP) vendor/bin/phpunit --coverage-clover=coverage.xml

validate-composer: ## Validate composer.json and composer.lock
validate-composer:
	$(EXEC_PHP) composer validate
#	$(EXEC_PHP) composer normalize --dry-run

validate-mapping: ## Validate doctrine mapping
validate-mapping:
	$(SYMFONY) doctrine:schema:validate --skip-sync -vvv --no-interaction

security: ## Run security-checker
security:
	$(EXEC_PHP) vendor/bin/security-checker security:check