# AnyGame

## About

Do you want to play classic games online with friends?

AnyGame provides various game components which can be used to play plenty of games.

For now we provide items such as:
- playing cards,
- dices.



# Installation

**Requires PHP 8**  
*PHP 7 might be used (see [Compatibilitty with PHP 7](#compatibility-with-php-7) section)*

Clone repository:

```
git clone https://github.com/pawelhanusik/AnyGame.git
cd AnyGame
```

Install php dependencies:

```
composer install
```

Create default config file:

```
cp .env.example .env
```

Generate app key:

```
php artisan key:generate
```

Configure database:

- setup for MySQL

    - install and enable mysql php extension

    - in .env file: 
        ```
        DB_CONNECTION=mysql
        DB_HOST=<database_host>
        DB_PORT=<database_port>
        DB_DATABASE=<database_name>
        DB_USERNAME=<database_username>
        DB_PASSWORD=<database_password>
        ```

- setup for SQLite

    - install and enable sqlite php extension.

    - in .env:

        ```
        DB_CONNECTION=sqlite
        ```

        and REMOVE all other vars prefixed with DB_

    - create db file:
        
        ```
        touch database/database.sqlite
        ```

Configure WebSockets:

In .env:

```
PUSHER_APP_KEY=<some_key>
PUSHER_APP_SECRET=<some_secret_key>
LARAVEL_WEBSOCKETS_HOST=<hostname>
LARAVEL_WEBSOCKETS_PORT=<websockets_port>
```

*Key and secret key should be any random alphanumeric string
The hostname have to be reachable by the users.*

Generate database:

```
php artisan migrate
```

Build frontend:

```
npm ci
npm run dev
```

# Running

## Using build-in server (not recommended)

```
php artisan serve --host=0.0.0.0 --port <www_port>
php artisan websockets:serve --port <websockets_port>
```

## Using apache

Just host `public` directory.

If you ran into issues with permissions, run (in AnyGame directory): `sudo chown -R www-data .`

And then for the WebSockets part, you have to use the artisan command:

```
php artisan websockets:serve --port <websockets_port>
```

# Testing

```
php artisan test
```

# Clearing database

```
php artisan migrate:fresh
```

# Production

```
npm run prod
```

In .env:

```
APP_ENV=production
APP_DEBUG=false
```


# SSL

In .env set following variables to match your needs:

```
LARAVEL_WEBSOCKETS_SSL_ENABLED=true
LARAVEL_WEBSOCKETS_SSL_LOCAL_CERT=null
LARAVEL_WEBSOCKETS_SSL_CA=null
LARAVEL_WEBSOCKETS_SSL_LOCAL_PK=null
LARAVEL_WEBSOCKETS_SSL_PASSPHRASE=null
```

Example for Let's Encrypt certificates:

```
LARAVEL_WEBSOCKETS_SSL_ENABLED=true
LARAVEL_WEBSOCKETS_SSL_LOCAL_CERT=/etc/letsencrypt/live/<domain>/fullchain.pem
LARAVEL_WEBSOCKETS_SSL_CA=null
LARAVEL_WEBSOCKETS_SSL_LOCAL_PK=/etc/letsencrypt/live/<domain>/privkey.pem
LARAVEL_WEBSOCKETS_SSL_PASSPHRASE=null
```

Note:

Self signed certificates are allowed only in debug mode.

# Compatibility with PHP 7

Run this custom artisan command:

```
php artisan command:downgrade2php7 --nobackup
```

# Troubleshooting

- `laravel.log could not be opened in append mode: failed to open stream: Permission denied`:

    Make sure that the server was run with permissions to AnyGame's files and directories.

- game components aren't moving, when other player is moving them:

    In most cases the reason is misconfiguration of WebSockets.
Make sure you have followed `#Websockets configration` and run `npm run prod` afterwards.
	If that didn't work, try running `sudo php artisan websockets:serve --port <websockets_port>` with `sudo`


## Have fun
