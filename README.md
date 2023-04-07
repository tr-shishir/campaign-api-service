# Campaign-api-service

This is an API for managing campaigns, with features such as creating, updating, deleting, and joining campaigns. It also allows users to leave campaigns, and to update their order quantity during an ongoing campaign.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

- PHP 8.1 or higher
- Composer

### Installing

1. Clone the repository: `git clone https://github.com/tr-shishir/campaign-api-service`
2. Navigate to the project directory: ```cd campaign-api```
3. Install PHP dependencies: ```composer install```
4. Rename the `.env.example` file to `.env`
5. Generate a new application key: `php artisan key:generate`
6. Generate JWT Secret: `php artisan jwt:secret`
7. Set up your database in the `.env` file: `DB_CONNECTION=mysql` `DB_HOST=127.0.0.1` `DB_PORT=3306` `DB_DATABASE=your_database_name` `DB_USERNAME=your_database_username` `DB_PASSWORD=your_database_password` 
8. Migrate the database: `php artisan migrate`
9. Start the development server: `php artisan serve`


## API Endpoints

### Campaigns

- `GET /api/campaigns` - Get a list of all campaigns (requires authentication).
- `GET /api/campaigns/{id}` - Get a single campaign by ID (requires authentication and ownership).
- `POST /api/campaigns` - Create a new campaign (requires authentication) ( params: `name`, `budget`, `description`, `stock_quantity` ).
- `PUT /api/campaigns/{id}` - Update an existing campaign (requires authentication and ownership and header must contain `Content-Type: application/x-www-form-urlencoded`) ( params: `name`, `budget`, `description`, `stock_quantity` ).
- `DELETE /api/campaigns/{id}` - Delete an existing campaign (requires authentication and ownership).
- `PATCH /api/campaigns/{id}/status` - Update Statys of an existing campaign (requires authentication and ownership) ( params: `status` ).
- `POST /api/campaigns/{id}/join` - Join an existing campaign (requires authentication) ( params: `quantity` ), after placed an order this route will hit with the quantity of order.
- `POST /api/campaigns/{id}/leave` - Leave an existing campaign (requires authentication).

### Orders

- `GET /api/campaigns/all/orders` - Get a list of all orders of the user (requires authentication and ownership).
- `POST /api/campaigns/{id}/orders` - Place a new order for a campaign (requires authentication) ( params: `quantity` ).
- `PUT /api/campaigns/{id}/orders/{order_id}` - Update an existing order (requires authentication) ( params: `quantity`, `decrease=true/false` ).
- `DELETE /api/campaigns/{id}/orders/{order_id}` - Cancel an existing order (requires authentication).

### Users

#### POST /api/register

Registers a new user.

#### POST /api/login

Logs in an existing user.

#### POST /api/logout

Logs out the currently authenticated user.

## Authentication

This API uses JSON Web Tokens (JWT) for authentication. To access protected endpoints, you must include a valid JWT in the `Authorization` header of your HTTP requests. 

To obtain a JWT, you must first register a new user by sending a `POST` request to `/api/register` with your name, email, and password in the request body. Then, send a `POST` request to `/api/login` with your email and password to receive a JWT. 

Include the JWT in the `Authorization` header of your requests like this: Authorization: Bearer {JWT}

# Thanks
