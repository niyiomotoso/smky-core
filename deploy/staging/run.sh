set -e

#in /var/www/smky-core/base

#laravel commands
composer install --no-dev
ls -la
#php artisan migrate --force
#php artisan db:seed --force
php artisan cache:clear
####

sudo rsync -a --exclude=live/ ./ live/
#sudo rm -r !(live)

sudo chown -R www-data:www-data live
sudo chmod -R 777 live/storage
echo "done deploying"
