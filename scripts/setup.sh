echo "# Create database & add admin user & load fixtures"
php bin/console doctrine:database:drop --force --if-exists
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction
rm -f public/media/cache/image_500_500/*
rm -f var/storage/images/*
php bin/console hautelook:fixtures:load --no-interaction
echo -e " --> DONE\n"
