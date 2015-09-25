#!/bin/bash
set -e

# Init project with corrects parameters
composer run-script post-install-cmd --no-interaction --no-dev
# Delete cache created by root
rm -rf app/cache/* appl/log/*

# Create or update database
su www-data -s /bin/bash -c "app/console doctrine:database:create --env=prod"
su www-data -s /bin/bash -c "app/console doctrine:schema:update --force --env=prod"

if [ "$1" = 'apache2ctl' ]; then
    # let's start as root
    exec "$@"
else
    # change to user www-data
    su www-data -s /bin/bash -c "$*"
fi
