# BFDb

The BigFurs Database: a simple collection of character sheets for fursonas.

A work in progress. Incomplete and largely non-functional!

## Requirements

PHP 7.1 or higher, with SQLite3 support enabled. (And probably other things enabled that should likely be there out of the box.)

Note that I'm using [CodeKit](https://codekitapp.com) for SCSS compilation, set to compile everything in `src/scss/` to the `public/style.css` stylesheet. If you can't use CodeKit (or just choose not to), you're on your own for handling this.

## Setup

Warning: "incomplete and largely non-functional" is not a joke.

1.  Initialize the database:

        cd src/db
        sqlite3 bfdb.db < schema.sql

2.  Start the PHP test server from the top-level directory (e.g., the one that contains this file):

        php -S 127.0.0.1:4000 -t public

That's it. If you want to test this with a real server, the `public/` directory should be the webroot and the server should be configured with URL rewriting that sends every URL that isn't pointing at a real file to `index.php`:

```
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.php [QSA,L]
```

## License

BFDb (BigFurs Database)  
Copyright 2020–2021 Arilin Thorferra

This program is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License along with this program. If not, see <https://www.gnu.org/licenses/>.
