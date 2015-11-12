#!/bin/bash
set -e

# Init project with environment variables given at runtime
composer run-script post-install-cmd --no-interaction --no-dev

# Delete cache created by root
rm -rf app/cache/* appl/log/*

# Warmup cache
su www-data -s /bin/bash -c "app/console cache:warmup"

if [ "$1" = 'apache2ctl' ]; then
    # Setup/update database
    su www-data -s /bin/bash -c "app/console doctrine:database:create"
    su www-data -s /bin/bash -c "app/console doctrine:schema:update --force"

    # Setup/update RabbitMq configuration
    su www-data -s /bin/bash -c "app/console dunkerque:broker:setup"

    # let's start apache as root
    exec "$@"
else
    # change to user www-data
    su www-data -s /bin/bash -c "$*"
fi
