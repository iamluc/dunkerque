Dunkerque
=========

Docker registry with admin interface.

Written in PHP with Symfony.

**THIS PROJECT IS IN PRE-ALPHA STATE**

# INSTALL

```
# Clone repository
git clone https://github.com/iamluc/dunkerque

# Enter directory
cd dunkerque

# Install dependencies with [composer](https://getcomposer.org/download/)
composer install

# Initialize database
php app/console doctrine:schema:create
```

Note: by default, your database and your layers will be stored in the cache folder (`app/cache`)

# LICENSE

[MIT](https://opensource.org/licenses/MIT)
