# Laravel Versioned REST API - Technical Test

A comprehensive, well-tested REST API built with Laravel 12 featuring token-based authentication, product CRUD operations, advanced filtering/sorting, file uploads, and proper authorization.

## Features

✅ **API Versioning** - All routes prefixed with `/api/v1`  
✅ **Token-Based Authentication** - Laravel Sanctum  
✅ **Product CRUD** - Full Create, Read, Update, Delete operations  
✅ **Advanced Filtering** - Category, price range, full-text search  
✅ **Sorting & Pagination** - Flexible sorting on multiple fields with pagination  
✅ **File Uploads** - Product thumbnail uploads with validation  
✅ **Authorization** - Role-based access control (Admin/User)  
✅ **Clean Architecture** - Action classes, DTOs, and API Resources  
✅ **Comprehensive Tests** - 26+ Pest tests covering all endpoints  
✅ **Soft Deletes** - Products can be restored  

## Stack

- **Framework**: Laravel 12
- **Database**: SQLite (configurable to MySQL/PostgreSQL)
- **Authentication**: Laravel Sanctum
- **Query Builder**: Spatie Laravel Query Builder
- **Data Transfer Objects**: Spatie Laravel Data
- **Testing**: Pest PHP
- **API Resources**: Laravel JSON Resources

## Installation

```bash
# 1. Install dependencies
composer install

# 2. Configure environment
cp .env.example .env
php artisan key:generate

# 3. Run migrations
php artisan migrate

# 4. Seed the database
php artisan db:seed

# 5. Start the development server
php artisan serve

# 6. Run tests
php artisan test
```

## API Endpoints

### Authentication

```
POST   /api/v1/auth/login          - Login and get token
POST   /api/v1/auth/logout         - Logout (requires auth)
GET    /api/v1/auth/me             - Get current user (requires auth)
```

### Products

```
GET    /api/v1/products            - Get paginated list (requires auth)
POST   /api/v1/products            - Create product (admin only)
GET    /api/v1/products/{id}       - Get product details (requires auth)
PATCH  /api/v1/products/{id}       - Update product (admin or creator)
DELETE /api/v1/products/{id}       - Delete product (admin only)
POST   /api/v1/products/{id}/thumbnail - Upload thumbnail
```

## Usage Examples

### 1. Login

```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

Response:
```json
{
  "data": {
    "token": "abc123...",
    "token_type": "Bearer",
    "user": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@example.com",
      "role": "admin"
    }
  }
}
```

### 2. Get Products (Paginated)

```bash
curl -X GET "http://localhost:8000/api/v1/products?page=1&per_page=15" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 3. Filter Products

```bash
# By category
curl -X GET "http://localhost:8000/api/v1/products?filter[category]=beauty" \
  -H "Authorization: Bearer YOUR_TOKEN"

# By price range
curl -X GET "http://localhost:8000/api/v1/products?filter[price_min]=10&filter[price_max]=200" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Search
curl -X GET "http://localhost:8000/api/v1/products?filter[search]=mascara" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 4. Sort Products

```bash
# Sort by price ascending
curl -X GET "http://localhost:8000/api/v1/products?sort=price" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Sort by price descending and stock ascending
curl -X GET "http://localhost:8000/api/v1/products?sort=-price,stock" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 5. Create Product (Admin Only)

```bash
curl -X POST http://localhost:8000/api/v1/products \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "New Product",
    "description": "Product description",
    "category": "beauty",
    "price": 29.99,
    "stock": 50,
    "discount_percentage": 10,
    "rating": 4.5
  }'
```

### 6. Update Product

```bash
curl -X PATCH http://localhost:8000/api/v1/products/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "price": 39.99,
    "stock": 25
  }'
```

### 7. Delete Product (Admin Only)

```bash
curl -X DELETE http://localhost:8000/api/v1/products/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 8. Upload Thumbnail

```bash
curl -X POST http://localhost:8000/api/v1/products/1/thumbnail \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "thumbnail=@/path/to/image.jpg"
```

## Query Parameters

### Pagination
- `page` - Page number (default: 1)
- `per_page` - Items per page (default: 15, max: 100)

### Filtering
- `filter[category]` - Filter by category (exact match)
- `filter[price_min]` - Minimum price
- `filter[price_max]` - Maximum price
- `filter[search]` - Search in title and description

### Sorting
- `sort=field` - Sort ascending (e.g., `sort=price`)
- `sort=-field` - Sort descending (e.g., `sort=-price`)
- Multiple sorts: `sort=-price,stock,title`

### Includes
- `include=creator` - Include the user who created the product

## Authorization Rules

### Product Creation
- **Admin**: Can create products
- **User**: Cannot create products (403)

### Product Update
- **Admin**: Can update any product
- **Creator**: Can update their own products
- **Others**: Cannot update (403)

### Product Deletion
- **Admin**: Can delete products
- **User**: Cannot delete products (403)

### Thumbnail Upload
- **Admin**: Can upload for any product
- **Creator**: Can upload for their own products
- **Others**: Cannot upload (403)

## Test User Credentials

```
Admin Account:
Email: admin@example.com
Password: password
Role: admin

Regular User:
Email: user@example.com
Password: password
Role: user
```

## Project Structure

```
app/
├── Actions/                 # Business logic (Action classes)
│   ├── Auth/
│   │   └── LoginAction.php
│   └── Products/
│       ├── CreateProductAction.php
│       ├── UpdateProductAction.php
│       ├── DeleteProductAction.php
│       └── UploadThumbnailAction.php
├── Data/                    # Data Transfer Objects
│   ├── LoginData.php
│   └── ProductData.php
├── Http/
│   ├── Controllers/
│   │   └── Api/V1/
│   │       ├── AuthController.php
│   │       └── ProductController.php
│   └── Resources/           # API Resources
│       ├── UserResource.php
│       └── ProductResource.php
├── Models/
│   ├── User.php
│   └── Product.php
└── Policies/               # Authorization Policies
    └── ProductPolicy.php

database/
├── migrations/             # Database migrations
├── factories/              # Model factories
│   ├── UserFactory.php
│   └── ProductFactory.php
└── seeders/               # Database seeders
    └── DatabaseSeeder.php

routes/
└── api.php                 # API routes

tests/
├── Feature/
│   ├── AuthTest.php        # Auth endpoint tests
│   └── ProductTest.php     # Product endpoint tests
└── TestCase.php            # Base test case
```

## Testing

Run all tests:
```bash
php artisan test
```

Run specific test file:
```bash
php artisan test tests/Feature/AuthTest.php
php artisan test tests/Feature/ProductTest.php
```

Run with coverage:
```bash
php artisan test --coverage
```

## Test Coverage

✅ **Authentication Tests (7)**
- Login with valid credentials
- Login with invalid credentials
- Cannot login with non-existent email
- Access /me when authenticated
- Cannot access /me when unauthenticated
- Can logout when authenticated
- Cannot logout without authentication

✅ **Product Tests (19)**
- Product list is paginated
- Filter by category works
- Search functionality works
- Price range filtering works
- Sorting works on multiple fields
- Admin can create products
- Normal users cannot create products (403)
- Can retrieve single product
- Admin can update any product
- Creator can update their own products
- Other users cannot update products (403)
- Admin can delete products
- Non-admin users cannot delete products (403)
- Upload thumbnail stores file and returns URL
- Thumbnail upload validates file type (image only)
- Unauthorized requests fail with 401
- Include creator relationship works


Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
