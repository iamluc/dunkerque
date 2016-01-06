iamluc/dunkerque
================

# About

Docker hub & registry image.

Souce code: [https://github.com/iamluc/dunkerque](https://github.com/iamluc/dunkerque)

# Run dunkerque

We suggest you to use docker-compose.

First, create a data container:

```sh
docker create --name dunkerque_data iamluc/dunkerque
```

Then run the containers stack with a similar `docker-compose.yml` file:

```yml
app:
    image: iamluc/dunkerque
    ports:
        - 80:80
    volumes_from:
        - dunkerque_data
    links:
        - mariadb:db
        - rabbitmq:rabbitmq

mariadb:
    image: mariadb:10
    environment:
        - MYSQL_ROOT_PASSWORD=dkpassword

rabbitmq:
    image: rabbitmq:3-management
    environment:
        - RABBITMQ_DEFAULT_USER=dunkerque
        - RABBITMQ_DEFAULT_PASS=dkpassword

workerwebhook:
    image: iamluc/dunkerque
    volumes_from:
        - dunkerque_data
    links:
        - rabbitmq:rabbitmq
    command: app/console dunkerque:broker:setup && app/console swarrot:consume:webhook
```

# Use your registry with docker

Please note that currently the image exposes only port 80.
You must setup a proxy (like [nginx-proxy](https://hub.docker.com/r/jwilder/nginx-proxy/)) with a certificate to use HTTPS.
Without HTTPS, you must add the `--insecure-registry` option to your daemon configuration. See https://docs.docker.com/registry/insecure/.

To push an image, you can follow this tutorial: https://www.digitalocean.com/community/tutorials/how-to-set-up-a-private-docker-registry-on-ubuntu-14-04#step-seven-—-publish-to-your-docker-registry

# Configure

You can use environment variables to configure the application:

| Variable name           | Default value       |
|-------------------------|---------------------|
| DUNKERQUE_STORAGE_PATH  | /data               |
