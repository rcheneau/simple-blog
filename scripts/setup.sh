echo "# Create database & add admin user & load fixtures"
php bin/console doctrine:database:drop --force --if-exists
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction
rm public/media/cache/thumbnail/*
php bin/console hautelook:fixtures:load --no-interaction
echo -e " --> DONE\n"
