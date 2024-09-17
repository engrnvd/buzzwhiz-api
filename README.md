# BuzzWhiz

BuzzWhiz is a sample news aggregator website. This project acts as the api backend for the website. The frontend
application repository is located [here](https://github.com/engrnvd/buzzwhiz-web).

## Local Development Setup

### Run the project

    cp .env.example .env

Then add your api keys to

    NEWS_API_KEY=
    THE_GUARDIAN_KEY=
    NYT_KEY=

Start the docker container by running

    docker-compose up -d

or

    docker compose up -d

depending on your docker setup.

To populate news in the local db, run the artisan command  `sail artisan app:scrape-news`.
(Instructions for setting up `sail` are in the next section.)

### To execute commands on the container:

    docker exec -it buzzwhiz-api-laravel.app-1 php artisan 

or

    ./vendor/bin/sail up

or create an alias for sail by adding this to your `.bashrc` or `.zshrc`:

    alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'

and then you can simply run `sail artisan`

All commands here: https://laravel.com/docs/9.x/sail#executing-sail-commands


