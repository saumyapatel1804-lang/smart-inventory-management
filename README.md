
# Smart Inventory Management API

A Laravel 11 REST API for managing products, categories, warehouses, stock adjustments, and inventory reporting.

## 1. Technology Stack

- PHP 8.1+
- Laravel 11
- MySQL 8+
- Redis
- Predis

## 2. Installation and Setup

### Requirements

- PHP 8.1 or higher
- Composer
- MySQL 8+ *** Note: I developed and tested this project using my local WAMP setup. My local environment uses MySQL 5.7.40. ***
- Redis-compatible server

### Installation

Clone the repository:

```bash
git clone https://github.com/saumyapatel1804-lang/smart-inventory-management.git
```

Install dependencies:

```bash
composer install
```

Create environment configuration:

```bash
cp .env.example .env
php artisan key:generate
```

Configure database settings in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_inventory_management
DB_USERNAME=root
DB_PASSWORD=
```

Configure Redis:

```env
CACHE_STORE=redis
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

Run migrations and seeders:

```bash
php artisan migrate --seed
```

Clear cached configuration:

```bash
php artisan optimize:clear
```

Start the application:

```bash
php artisan serve
```

Base URL:

`http://127.0.0.1:8000`

## 3. API Reference

    *** url and request info ***
        1. Category Tree
        - Method: GET
        - URL: /api/categories/tree
        - Description: Returns the complete nested category hierarchy, excluding inactive categories.
        Request JSON: Not required.

        2. Product Listing
        - Method: GET
        - URL: /api/products
        - Description: Returns a paginated list of products with filtering and sorting.
        Query example:
        /api/products?category_id=2&warehouse_id=1&available=true&min_price=100&max_price=1000&search=laptop&sort_by=price&sort_order=asc&per_page=10


        3. Stock Adjustment
        - Method: POST
        - URL: /api/stock/adjust
        - Description: Adjusts product stock, handles reservations, and records stock movements.
        Request JSON:
        {
            "product_id": 1,
            "warehouse_id": 1,
            "movement_type": "stock_in",
            "quantity": 10,
            "note": "New stock received"
        }


        4. Inventory Summary
        - Method: GET
        - URL: /api/inventory/summary
        - Description: Returns total quantity, reserved quantity, available quantity, highest-stock warehouse, and zero-stock status.
        Request JSON: Not required.

        5. Product Movement History
        - Method: GET
        - URL: /api/products/{product}/movements
        - Description: Returns paginated stock movement history and movement summary for a product.
        Query example:
        /api/products/1/movements?date_from=2026-11-01&date_to=2026-11-23&per_page=20


        6. Low Stock Alert
        - Method: GET
        - URL: /api/inventory/low-stock
        - Description: Returns products with low available stock, ordered by available quantity (ascending).
        Query example:
        /api/inventory/low-stock?threshold=10&page=1

### Category Tree

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/categories/tree` | Returns nested category hierarchy |

Inactive categories are excluded. Their active children are placed under the nearest active ancestor.

### Product Listing

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/products` | Returns paginated products |

Supported query parameters:

- `product_id`
- `category_id`
- `warehouse_id`
- `available`
- `min_price`
- `max_price`
- `search`
- `sort_by` (name, price, available_stock)
- `sort_order` (asc, desc)
- `per_page`

### Stock Adjustment

| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/stock/adjust` | Adjusts stock and records movement |

Request example:

```json
{
    "product_id": 1,
    "warehouse_id": 1,
    "movement_type": "stock_in",
    "quantity": 10,
    "note": "New stock received"
}
```

Accepted movement types:

- stock_in
- stock_out
- reservation
- reservation_release

### Inventory Summary

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/inventory/summary` | Returns inventory summary across products and warehouses |

Includes total quantity, reserved quantity, available quantity, highest-stock warehouse, and zero-stock status.

### Product Movement History

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/products/{id}/movements` | Returns paginated movement history |

Query parameters:

- `date_from`
- `date_to`
- `per_page` (default: 20)

Includes database-level movement aggregation.

### Low Stock Alert

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/inventory/low-stock` | Returns products with low available stock |

Query parameters:

- `threshold` (default: 10)
- `page`

Results are ordered by available quantity, ascending.

## 4. Caching Strategy

Redis is used as the application cache store.

### Cache Keys

| Cache key | Purpose | TTL |
|---|---|---|
| `categories:tree` | Category hierarchy | 1 hour |
| `inventory:summary` | Inventory summary | Check implementation |
| `inventory:low-stock:v1:version:{version}:threshold:{threshold}:page:{page}:per_page:{perPage}` | Low-stock results | 1 hour |

### Cache Invalidation

- Category tree: Invalidated when a category is saved or deleted.
- Inventory summary: Invalidated after a successful stock adjustment.
- Low-stock results: Cache version is incremented after a successful stock adjustment.

Empty result sets and error responses are not cached.

### Cache Warming

The category tree endpoint is warmed using:

```bash
php artisan cache:warm-category-tree
```

This preloads the category hierarchy into Redis so the first API request does not need to build it.

## 5. Indexing Decisions

| Table | Index | Reason |
|---|---|---|
| categories | parent_id, is_active | Category hierarchy lookup |
| categories | slug (unique) | Unique category slugs |
| products | category_id, is_active | Active products by category |
| products | name | Name sorting/search support |
| products | base_price | Price filtering and sorting |
| products | sku (unique) | Unique SKU lookup |
| stock | product_id, warehouse_id (unique) | One stock record per product and warehouse |
| stock | warehouse_id, product_id | Warehouse-first lookup |
| stock_movements | product_id, moved_at | Product movement history and date filtering |
| stock_movements | reference_type, reference_id | Polymorphic reference lookup |

## 6. Database Seeding

The seeders generate:

- 11 categories with nested hierarchy.
- 50 products.
- 3 warehouses.
- 150 stock records.
- 200 stock movement records.

Seed data includes zero-stock products, products across multiple warehouses, inactive categories with active children, and categories without products.

## 7. Architecture

The project follows Laravel's MVC architecture.

- Models: Database relationships, casts, and query scopes.
- Controllers: Handle incoming API requests.
- Form Requests: Validate request input.
- Services: Handle business logic.
- API Resources: Format API responses.
- Migrations: Define database structure, constraints, and indexes.
- Seeders: Generate sample inventory data.

## 8. Error Handling

- Validation errors: HTTP 422.
- Business rule violations: HTTP 422.
- Unexpected errors: HTTP 500 without exposing internal details in production.

## 9. Assumptions

- Stock quantity and reserved quantity are maintained separately.
- Available quantity is calculated as quantity minus reserved quantity.
- Stock adjustments are performed within database transactions.
- Stock movement history is retained as an immutable record.
- Product listing defaults to 10 records per page.
- Movement history defaults to 20 records per page.
