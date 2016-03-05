#!/bin/bash
set -e

# Generate keys needed by JWT
if [ ! -f var/jwt/private.pem ]; then
    openssl genrsa -passout env:DK_JWT_KEY_PASS_PHRASE -out var/jwt/private.pem -aes256 4096
    openssl rsa -pubout -passin env:DK_JWT_KEY_PASS_PHRASE -in var/jwt/private.pem -out var/jwt/public.pem
fi

# Init project with environment variables given at runtime
composer run-script post-install-cmd --no-interaction --no-dev

# Delete cache created by root
rm -rf app/cache/* app/logs/*

if [ "$1" = 'apache2ctl' ]; then
    # Let's time to other containers (i.e. mysql)
    sleep 5

    # Warmup cache
    su www-data -s /bin/bash -c "app/console cache:warmup --no-interaction"

    # Setup/update database
    su www-data -s /bin/bash -c "app/console doctrine:database:create --no-interaction --if-not-exists"
    su www-data -s /bin/bash -c "app/console doctrine:migrations:migrate --no-interaction"

    # let's start apache as root
    exec "$@"
else
    # change to user www-data
    su www-data -s /bin/bash -c "$*"
fi
