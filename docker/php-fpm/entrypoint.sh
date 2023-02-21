#!/bin/bash

# Unpacking parameters.yaml.dist and .env.dist
[ -f .env ] && echo "Env file is configured" || (echo "Configuring env file" && cp .env.dist .env  && echo "done")

# php dependencies installation
composer install

echo "Warming up cache.."
php bin/console cache:warmup
echo "Warming up cache finished."

echo "Creating database.."
php bin/console doctrine:database:drop --force  --env=dev
php bin/console doc:database:create --if-not-exists
echo "Creating database finished."


echo "Applying migrations.."
php bin/console doc:mig:mig -n
echo "Applying migrations finished."

php-fpm -F -R


