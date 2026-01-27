# Postman Collection Guide

## Import the Collection

1. Open Postman
2. Click **Import** → **Upload Files**
3. Select `Backend_API.postman_collection.json`
4. Collection is now ready to use

## Setup Environment Variables

The collection uses two variables:

- **`base_url`** - API server URL (default: `http://127.0.0.1:8000`)
- **`token`** - Bearer token from login response

### Steps to Set Token:

1. **Run "Login" request** in the Auth folder
2. Copy the `token` value from the response
3. Click the **Environment** button (top-right)
4. Paste token into the `token` variable
5. All subsequent requests will use this token automatically

## Requests Included

### Auth (3 requests)
- **Login** - Get bearer token
- **Get Current User** - Retrieve authenticated user
- **Logout** - Invalidate token

### Products (10 requests)
- **List Products** - Basic pagination
- **Filter by Category** - Category exact match
- **Filter by Price Range** - Min/max price filtering
- **Search** - Full-text search in title/description
- **Sort by Price** - Sort descending (use `-price` for desc, `price` for asc)
- **Include Creator** - Load creator relationship
- **Get Product by ID** - Single product details
- **Create Product** - Admin only
- **Update Product** - Admin or creator
- **Delete Product** - Admin only
- **Upload Thumbnail** - File upload (admin or creator)

## Test Credentials

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

## Quick Start

1. Import collection
2. Run **Login** with admin@example.com/password
3. Copy token to environment
4. Run any product request
5. Modify query params or body as needed

## Share the Collection

This collection is fully self-contained and can be shared directly:
- Share the `Backend_API.postman_collection.json` file
- Recipients just need to import it and set the `base_url` and `token` variables

## Tips

- **Pagination**: Use `page=1&per_page=15` (max per_page is 100)
- **Multiple Sorts**: `sort=-price,title,stock` works
- **Combining Filters**: `?filter[category]=beauty&filter[price_min]=10&filter[price_max]=100`
- **Field Selection** (bonus): `?fields=id,title,price` (requires `allowedFields` configured)
