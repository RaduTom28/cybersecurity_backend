composer install
php bin/console doctrine:schema:update
php bin/console doctrine:fixtures:load
