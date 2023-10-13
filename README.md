# YAMDB

# Symfony Project with DDEV

This repository contains a Symfony project configured to work with [DDEV](https://ddev.readthedocs.io/en/stable/), a powerful local development environment tool for web projects.

## Table of Contents

- [Prerequisites](#prerequisites)
- [Getting Started](#getting-started)
  - [Installation](#installation)
  - [Usage](#usage)
- [Folder Structure](#folder-structure)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
  - [Running Migrations](#running-migrations)
- [API Data Import](#api-data-import)
- [License](#license)

## Prerequisites

Before you get started, make sure you have the following software installed on your machine:

- [Docker](https://www.docker.com/get-started)
- [DDEV](https://ddev.readthedocs.io/en/stable/)
- [Composer](https://getcomposer.org/download/)

## Getting Started

### Installation

1. Clone this repository to your local machine:

   ```bash
   git clone https://github.com/LaurensDS/YAMDB.git
   ```

2. Change your working directory to the project folder:

   ```bash
   cd yet_another_movie_database
   ```

3. Run composer

   ```bash
   composer install
   ```

4. Start the DDEV development environment:

   ```bash
   ddev start
   ```

5. Run npm

   ```bash
   npm run watch
   ```

### Usage

- Access the Symfony application at [http://yamdb.ddev.site](http://yamdb.ddev.site) in your web browser.

## Folder Structure

- `config/` - Configuration files for Symfony.
- `src/` - Your Symfony application's source code.
- `templates/` - Twig templates.
- `var/` - Application cache and log files.
- `ddev` - DDEV configuration files.

## Configuration

You can customize the DDEV configuration by editing the `.ddev/config.yaml` file. Refer to the [DDEV documentation](https://ddev.readthedocs.io/en/stable/users/settings_files/) for more details on configuration options.

## Database Setup

### Running Migrations

To set up the database schema, you need to run migrations. Use the following commands:

1. Make sure you have configured your database connection in the `.env` file. For DDEV you can run the following command to get the database information:

   ```bash
   ddev describe
   ```

2. Generate the migration classes:

   ```bash
   php bin/console make:migration
   ```

3. Apply the migrations:

   ```bash
   php bin/console doctrine:migrations:migrate
   ```

## API Data Import

To import data from an API, you can use the following custom command:

1. Open a terminal in your project directory.

2. Execute the API data import command:

   ```bash
   php bin/console app:import-movies
   ```

This command should fetch data from the API and populate your database.

Don't forget to add your Access Token fom [TMDB](https://developer.themoviedb.org/reference/intro/authentication) to the .env file

## License

This project is open-source and available under the [MIT License](LICENSE). Feel free to use and modify it as needed.
```

This Markdown format can be copied and pasted into a README.md file within your Symfony project repository.