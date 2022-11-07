<p align="center">
  <img src="https://raw.githubusercontent.com/antony382/roles-and-permission/master/public/images/logo.png" style="width: 15% !important;max-width: 20% !important;">
</p>


ProcessDrive laravel locale translate
=============================================
  This pacakage is used for store your locale file in database and run the artisan command retrive and overrite the value in local lang folder language file. Then you can directly update and store languages. if you wants to create new language you can directly mention this package. it will translate and store the values.


Installation
============

Run this command in your terminal

```
composer require process-drive/laravel-file-translate
```

After Installation
==================

To set service provider to config/app.php file
```
'providers' => [
        ProcessDrive\LaravelFileTranslate\LaravelFileTranslateServiceProvider::class,
    ]
```
If you not added in job table in your project. you run this below command or refer this link : "https://laravel.com/docs/9.x/queues"

```
php artisan queue:table
```
Mention .env:
```
QUEUE_CONNECTION=database
```
Run the migration

```
php artisan migrate
````

Run this command in your terminal store the local file value to database

```
php artisan translate:filetodb
```

Run this command in your terminal retrive the database value

```
php artisan translate:dbtofile
```

Then:
```
php artisan serve
```

```
Go to this link: "http://127.0.0.1:8000/translation/index"
```

License
=======
MIT





has context menu
