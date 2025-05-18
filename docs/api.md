# API Documentation

This document provides a detailed specification of the API endpoints for the project.

## STORY-001: Implement Resource Listing with Filterable API Endpoints

### Resource: "Products" (Example)

*   **Route:** `GET /api/products`
*   **HTTP Method:** `GET`
*   **Parameters (Query):**
    *   `page` (integer, optional): Page number for pagination. Default: 1.
    *   `per_page` (integer, optional): Number of items per page. Default: 15.
    *   `filter[name]` (string, optional): Filter by product name (partial, case-insensitive search).
    *   `filter[category_id]` (integer, optional): Filter by product category ID.
    *   `filter[status]` (string, optional): Filter by product status (e.g., 'active', 'inactive').
    *   _(Other filters as needed - minimum 3 total)._
*   **Required Headers:**
    *   `Accept: application/json`
    *   `X-Requested-With: XMLHttpRequest` (automatically added by Inertia)
*   **Response Formats (Success):** `200 OK`
    ```json
    {
      "data": [
        {
          "id": 1,
          "name": "Laptop Pro",
          "category": { "id": 1, "name": "Electronics" },
          "status": "active",
          "price": 7500.00,
          "created_at": "2024-05-08T10:00:00.000000Z"
        }
        // ... other products
      ],
      "links": {
        "first": "/api/products?page=1",
        "last": "/api/products?page=5",
        "prev": null,
        "next": "/api/products?page=2"
      },
      "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 5,
        "links": [ /* ... detailed pagination links ... */ ],
        "path": "/api/products",
        "per_page": 15,
        "to": 15,
        "total": 75,
        "filters": { // To return active filters to the frontend
            "name": "Laptop",
            "category_id": 1,
            "status": "active"
        }
      }
    }
    ```
    _(The response structure will follow Laravel's standard for paginated API Resources)_
*   **Response Formats (Error):**
    *   `422 Unprocessable Entity` (Filter parameter validation failure)
        ```json
        {
          "message": "The given data was invalid.",
          "errors": {
            "filter.category_id": ["The category filter must be an integer."],
            "filter.status": ["The selected status is invalid."]
          }
        }
        ```
    *   `500 Internal Server Error` (Unexpected server error)
        ```json
        {
          "message": "Internal server error."
        }
        ```
*   **HTTP Status Codes:**
    *   `200 OK`: Successfully retrieved resources.
    *   `422 Unprocessable Entity`: Parameter validation failure.
    *   `500 Internal Server Error`: Server error.


## STORY-002: Implement User Registration with Real-Time Email Validation

### Resource: "Email Validation"

*   **Route:** `POST /api/validate-email`
*   **HTTP Method:** `POST`
*   **Parameters (Request Body - JSON):**
    *   `email` (string, required, valid email format): The email address to validate.
    ```json
    {
      "email": "test@example.com"
    }
    ```
*   **Required Headers:**
    *   `Accept: application/json`
    *   `Content-Type: application/json`
    *   `X-Requested-With: XMLHttpRequest`
*   **Response Formats (Success):**
    *   `200 OK` (Email is valid and available)
        ```json
        {
          "exists": false
        }
        ```
*   **Response Formats (Error):**
    *   `422 Unprocessable Entity` (Validation failure - e.g., invalid email format, email already taken)
        ```json
        {
          "message": "The given data was invalid.",
          "errors": {
            "email": [
              "The email has already been taken." // Or "The email must be a valid email address."
            ]
          }
        }
        ```
    *   `500 Internal Server Error` (Unexpected server error)
        ```json
        {
          "message": "Internal server error."
        }
        ```
*   **HTTP Status Codes:**
    *   `200 OK`: Email is valid and available.
    *   `422 Unprocessable Entity`: Validation failure (invalid format or email taken).
    *   `500 Internal Server Error`: Server error.

### Data Models

**Model: `Product`**

*   `id` (BIGINT, Primary Key, Auto Increment)
*   `name` (VARCHAR(255), Required, Unique)
*   `description` (TEXT, Optional)
*   `price` (DECIMAL(10, 2), Required, Non-negative)
*   `category_id` (BIGINT, Foreign Key to `categories.id`, Required)
*   `status` (VARCHAR(50), Required, e.g., 'active', 'inactive', 'discontinued'. Default: 'active')
*   `created_at` (TIMESTAMP)
*   `updated_at` (TIMESTAMP)

**Model: `Category`**

*   `id` (BIGINT, Primary Key, Auto Increment)
*   `name` (VARCHAR(255), Required, Unique)
*   `slug` (VARCHAR(255), Required, Unique)
*   `created_at` (TIMESTAMP)
*   `updated_at` (TIMESTAMP)

**Relationships:**

*   `Product` belongs to a `Category` (`belongsTo`).
*   `Category` has many `Products` (`hasMany`).
