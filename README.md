# GCDb

The Giants' Club database: a simple collection of character sheets for fursonas.

A work in progress. Incomplete and largely non-functional!

## Requirements

PHP 7.1 or higher, with SQLite3 support enabled. (And probably other things enabled that should likely be there out of the box.)

Note that I'm using [CodeKit](https://codekitapp.com) for SCSS compilation, set to compile everything in `src/scss/` to the `public/style.css` stylesheet.

## Setup

Warning: "incomplete and largely non-functional" was not a euphemism.

1. Initialize the database:

        cd src/db
        sqlite3 gcdb.db < schema.sql

2. Start the PHP test server from the top-level directory (e.g., the one that contains this file):

        php -S 127.0.0.1:4000 -t public

That's it. If you want to test this with a real server, the `public/` directory should be the webroot and the server should be configured with URL rewriting that sends every URL that isn't pointing at a real file to `index.php`:

```
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.php [QSA,L]
```
