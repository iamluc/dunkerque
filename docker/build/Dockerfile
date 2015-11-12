FROM iamluc/symfony

MAINTAINER Luc Vieillescazes <luc@vieillescazes.net>

RUN rm index.html \
  && wget -O - https://github.com/iamluc/dunkerque/archive/master.tar.gz | tar xz --strip 1 \
  && composer install --no-interaction --no-dev --no-scripts \
  && rm -Rf app/cache/* app/logs/* .composer web/app_dev.php \
  && mkdir /data \
  && chown www-data:www-data app/cache app/logs /data

COPY entrypoint.sh /usr/local/bin/entrypoint.sh

VOLUME ["/data"]

ENV SYMFONY_ENV prod
ENV DUNKERQUE_STORAGE_PATH /data
