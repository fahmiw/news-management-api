# News Management Application API

This guide provides step-by-step instructions on how to clone and set up the Laravel 8 project from the given repository, and configure it to use MySQL as the database.

## Prerequisites

Before getting started, ensure that you have the following installed on your machine:

- PHP (7.4 or higher)
- Composer
- MySQL
- Redis

## Installation

Follow these steps to clone and set up the News Management Application API:

1. Clone the repository:

    ```bash
    git clone https://github.com/fahmiw/news-management-api
    ```

2. Navigate to the project directory:

    ```bash
    cd news-management-api
    ```

3. Install project dependencies using Composer:

    ```bash
    composer install
    ```

4. Install or Run MySQL database in XAMPP,

5. Set Up Redis (if not using Docker)

    If you're not using Docker, ensure you have Redis installed and running on your machine.
    If using docker, run this command:

    ```
    docker-compose up -d
    ```

6. Set Up the Environment

    Create a copy of the .env.example file and name it .env. Configure the database and Redis settings in the .env file.

    ```bash
    cp .env.example .env
    ```

7. Update the Laravel database configuration:
    Open the .env file in your project and update the following lines:
    ```ini
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=27017
    DB_DATABASE=your-database-name
    DB_USERNAME=
    DB_PASSWORD=

    QUEUE_CONNECTION=redis
    REDIS_HOST=127.0.0.1
    REDIS_PASSWORD=null
    REDIS_PORT=6379
    REDIS_CLIENT=predis
    ```
8. Generate an application key:

    ```bash
    php artisan key:generate
    ```
9. Generate Access Client OAuth:

    ```bash
    php artisan passport:install
    ```

10. Running the Application

    To run the cloned Laravel application with MongoDB, execute the following commands:
    ```bash
    php artisan serve
    ```

    This command will start the development server, and you should see a message like "Laravel development server started: http://localhost:8000".

11. Open your postman and visit http://localhost:8000 to access the News Management Application API.

## API Endpoints

The following API endpoints are available in the project,
Postman Collection : https://documenter.getpostman.com/view/24919470/2s946mZpzK

## Additional Information

For more detailed instructions and information about Laravel, please refer to the official Laravel documentation: https://laravel.com/docs/8.x

## License

This project is licensed under the MIT License.
Feel free to copy and use this updated template, which includes the steps to 
