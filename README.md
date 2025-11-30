# tium-wearable-project-be

## Stack Requirements

-   PHP 8.1 or higher
-   Composer
-   Laravel 10 or higher
-   MySQL

### How To Run This Project

```bash
# Clone into YOUR directory
git clone https://github.com/esu-partners/tium-wearable-project-be.git

#move to project
cd tium-wearable-project-be

# make an env
cp .env.example .env

# install packages
composer install

# generate new key
php artisan key:generate

# create auth passport
php artisan passport:install --force

# run migration
php artisan migrate --seed

# copy your firebase config
nano ./storage/app/firebase_credentials.json

# run app
php artisan serve --port=9090

# Open at browser this url
http://localhost:9090

# noted auth : user id : 001 , pass : secret

```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
