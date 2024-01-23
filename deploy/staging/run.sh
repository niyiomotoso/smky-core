set -e

#cd /var/www/smky-core/base

#laravel commands
composer install --no-dev
cd ../
ls -la
cp config/.env   .env
cd base
#php artisan migrate --force
#php artisan db:seed --force
php artisan cache:clear
####

cd ../
sudo rm -r backup
sudo mv live backup
mv base live
sudo chown -R www-data:www-data live
sudo chmod -R 777 live/storage
echo "done deploying"
