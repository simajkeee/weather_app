# weather_app
Simple app to fetch weather data.


Steps to launch the app:
1. Install composer dependencies.
2. Create db with collation utf8_general_ci.
3. Set connection in .env file.
4. Execute php bin/console doctrine:migrations:migrate.
5. Execute php bin/console doctrine:schema:update --force.
6. Execute php bin/console fos:user:creat --super-admin.
7. Execute php bin/console app:weather to fetch weather data.
8. Setup cron. As example: */50 * * * * php /path/to/app/bin/console app:weather.
