set -e

cd /var/www/smky-core/base

#lumen commands
composer install --no-dev

cd ../
cp config/config.php base/config.php
sudo rm -r backup
sudo mv live backup
mv base live
# sudo mkdir base
sudo chown -R www-data:www-data live
sudo chmod -R 777 live/storage
echo "done deploying"