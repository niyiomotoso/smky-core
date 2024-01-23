set -e

cd /var/www/user-core-service

if [ -d "user-core-service" ]; then
   rm -r user-core-service
fi


git clone -b master git@gitlab.com:beedng/user-core-service.git
cd user-core-service

#lumen commands
composer install --no-dev
#composer update
cp ../config/.env .env
php artisan migrate --force
php artisan db:seed --force
php artisan cache:clear
####

cd ../
sudo rm -r backup
sudo mv live backup
mv user-core-service live
sudo chown -R www-data:www-data live
sudo chmod -R 777 live/storage
echo "done deploying"
