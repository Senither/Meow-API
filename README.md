[![forthebadge](https://forthebadge.com/images/badges/contains-cat-gifs.svg)](https://forthebadge.com)

# Meow API

The Meow API is a simple cat API used by some of my own projects, most recently [AvaIre](https://github.com/avaire/avaire) since the original API used by the project ran into a lot of issues.

## Getting Started

The Meow API is empowered by the [Lumen](https://lumen.laravel.com/) PHP framework, to host the project, you first need to install all the PHP dependencies using [Composer](https://getcomposer.org/).

    $ composer install

Next, copy the `.env.example` file and call it `.env`, then fill out the `DB_XXX` and `APP_XXX` environment properties, when you're done you can save the new `.env` file.

> The `APP_KEY` should be a string of random characters with a length of 32 characters.

Now we're ready to migrate the database, this will create all the necessary tables required by the API to function properly.

    $ php artisan migrate

Next, add any and all images you want the API to serve users inside of the `public/c` directory, when you're done you can then sync the images with the database so they can be indexed:

    $ php artisan image:sync

And you're done!

## License

The Meow API is open-sourced software licensed under the [GNU General Public License v3.0](http://www.gnu.org/licenses/gpl.html).
