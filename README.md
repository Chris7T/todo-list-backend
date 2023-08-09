# TodoList Backend API

_API for managing and integrating task lists._

## About the API

The API was built using the PHP language with the Laravel framework:

- **Implementation of an `Action` layer** - Segregates business logic from the application layer.
- **Incorporation of `Repositories` layer** - Allows for database abstraction.
- **Development of `Unit Tests`** - Ensures the functionality of Actions.
- **Development of `Feature Tests`** - Verifies the interaction between actions and repositories.
- **Usage of `Swagger`** - Provides comprehensive documentation.
- **User authentication** - Leveraged by JWT.
- **Integration** - With the Google Task API.

## Running the project

### Prerequisites

- Docker
- Docker-compose
- Google Cloud Secret File

## Step by step

1. - Clone the project
```
git clone https://github.com/Chris7T/todo-list-backend.git
```
2. - Enter the project folder
```
cd todo-list-backend
```
3. - Up the containers
```
docker-compose up -d
```
4. - Enter the workspace
```
docker exec -it app bash
```
5. - Install the composer
```
COMPOSER_PROCESS_TIMEOUT=6000 composer i
```
6. - Generate .env
```
cp .env.example .env
```
7. - Generate the API Key
```
php artisan key:generate
```
8. - Generate the JWT secret
```
php artisan jwt:secret
```
9. - Run the migrations
```
php artisan migrate
```
10. - Run the tests
```
php artisan test
```
11. - Generate documentation
```
php artisan l5-swagger:generate
```


# Documentation link

Para acessar a documentação basta acessar o link 

```
   /api/documentation/
```
