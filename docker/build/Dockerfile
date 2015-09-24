FROM iamluc/symfony

RUN rm index.html \
  && wget -O - https://github.com/iamluc/dunkerque/archive/master.tar.gz | tar xz --strip 1 \
  && composer install --no-interaction \
  && rm -Rf app/cache/* app/logs/* web/app_dev.php \
  && chown www-data:www-data app/cache app/logs

COPY entrypoint.sh /usr/local/bin/entrypoint.sh

VOLUME ['/var/www/html/app/cache', '/var/www/html/app/logs']
