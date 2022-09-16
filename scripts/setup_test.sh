echo "# Create database & add admin user & load fixtures"
php bin/console doctrine:database:drop --force --if-exists --env=test
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --no-interaction --env=test
php bin/console hautelook:fixtures:load --no-interaction --env=test
echo -e " --> DONE\n"
