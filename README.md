-open terminal and type composer install
-if you cannot find .env file, type cp .env.example .env
and put database values;
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=study_group_db
DB_USERNAME=root
DB_PASSWORD=hT024112001
-if you can put the values directly
-php artisan key:generate
-php artisan migrate
-Intelephense: Clear Cache
-php artisan serve
