echo "# Create database & add admin user & load fixtures"
php bin/console doctrine:database:drop --force --if-exists
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console hautelook:fixtures:load --no-interaction
php bin/console app:create-user -a admin@email.test admin password
echo -e " --> DONE\n"
