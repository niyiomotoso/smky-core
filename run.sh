set -e

cd /var/www/smky-core/base

#lumen commands
composer install --no-dev

cd ../
cp live/config.php base/
# might need to remove this later so as to change the color
cp live/public/content/variables.css base/public/content/variables.css
sudo rm -r backup
sudo mv live backup
mv base live
# sudo mkdir base
sudo chown -R www-data:www-data live
sudo chmod -R 777 live/storage
echo "done deploying"