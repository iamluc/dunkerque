Dunkerque
=========

[![Build Status](https://api.travis-ci.org/iamluc/dunkerque.png?branch=master)](https://travis-ci.org/iamluc/dunkerque) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/8789214a-26f9-42b6-a98b-de4e3fd5ba8e/mini.png)](https://insight.sensiolabs.com/projects/8789214a-26f9-42b6-a98b-de4e3fd5ba8e)

# About

Docker hub & registry.

Written in PHP with Symfony.

**THIS PROJECT IS IN ALPHA STATE**

# Docker image

If you just want to use dunkerque, use the [docker image on the docker hub](https://hub.docker.com/r/iamluc/dunkerque/).

# Install

Base

```sh
# Clone repository
git clone https://github.com/iamluc/dunkerque

# Enter directory
cd dunkerque
```

## Method 1: Using Docker & docker-compose

```sh
# Run server (You could have to change port)
docker-compose up -d

# Install dependencies
docker-compose run --rm app composer install

# Initialize database
docker-compose run --rm app app/console doctrine:database:create --if-not-exists
docker-compose run --rm app app/console doctrine:migrations:migrate

# Create a user
docker-compose run --rm app app/console fos:user:create

# Run test suite
docker-compose run --rm app bin/run-tests
```

## Method 2: On your machine

```sh
# Install dependencies with [composer](https://getcomposer.org/download/)
composer install

# Initialize database
php app/console doctrine:database:create --if-not-exists
php app/console doctrine:migrations:migrate

# Create a user
php app/console fos:user:create

# Run test suite
./bin/run-tests
```

# LICENSE

[MIT](https://opensource.org/licenses/MIT)
