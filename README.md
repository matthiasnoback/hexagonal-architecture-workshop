# Sandbox project for the "Hexagonal Architecture" training

You'll find all the available training programs here: <https://matthiasnoback.nl/training/>

## Requirements

- Docker Engine
- Docker Compose
- Git
- Bash

## Getting started

- Clone this repository (`git clone git@github.com:matthiasnoback/hexagonal-architecture-workshop.git`) and `cd` into it.
- Run `bin/install`.
- Open <http://localhost:8000> in a browser. You should see the homepage of the Bunchup application.

If port 8000 is no longer available on your local machine, modify `docker-compose.yml` to publish to another port:

```yaml
ports:
    # To try port 8081:
    - "8001:8080"
```

## Running development tools

- Run `bin/load-users` to create a standard set of users
- Run `bin/composer` to use Composer (e.g. `bin/composer require symfony/var-dumper`)
- Run `bin/test` to run all tests, including PHPStan
- Run `bin/console` to run CLI commands specific to this application (e.g. `bin/console sign-up`)

## Cleaning up after the workshop

- Run `bin/cleanup` to remove all containers for this project, their images, and their volumes.
- Remove the project directory.
