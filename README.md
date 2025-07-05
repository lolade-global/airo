# Quotation API

This project is a simple quotation pricing API built with Laravel 12 (back-end) and vanilla JS (front-end). It implements **JWT authentication**, calculates quotations based on age and trip duration, and ensures clean RESTful response standards.

---

## Features

- **JWT Authentication** (Register/Login)
- **Quotation logic via Service Layer**
- **Data persistence** with Eloquent models
- **Request validation** with FormRequest
- **API Resource responses**
- **Unit test coverage**
- **REST-compliant structure**
- **Protected endpoints**
- **Swagger (OpenAPI) Documentation**
- Simple front-end with toast alerts and session handling

---

## Tech Stack

- **Laravel 12**
- **JWT Auth** (php-open-source-saver/jwt-auth)
- **MySQL**
- **HTML + JS Frontend**
- **Postman / Swagger for API testing**

---

## Getting Started

Follow the steps below to spin up the project locally:

### 1. Clone the Repository

```bash
git clone https://github.com/lolade-global/airo.git
cd airo
```

### 2. Install Backend Dependencies

```bash
composer install
```

### 3. Copy `.env` and Generate App Key

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database

Edit your `.env` file:

```
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

FIXED_RATE=3
```

Then run:

```bash
php artisan migrate
```

### 5. Install JWT Auth Package
```bash
php artisan jwt:secret
```

### 6. Serve the App

```bash
php artisan serve
```

Then Register via `http://localhost:8000/register.html` or Login via `http://localhost:8000/index.html`

---

## API Endpoints

### Auth Routes

 - Method  Endpoint              Description
 - POST    /api/auth/register    User Registration
 - POST    /api/auth/login       Login & get token

### Quotation Route

 - Method  Endpoint              Description
 - POST    /api/quotation   Generate a quotation *(Protected)*

---

## Quotation Logic

- Fixed Rate: **€3.00/day**
- Age Loadings:
  - 18–30: 0.6x
  - 31–40: 0.7x
  - 41–50: 0.8x
  - 51–60: 0.9x
  - 61–70: 1.0x
- Total = `fixed_rate * age_load * number_of_days`

---

## Assumptions

- All ages are provided as a comma-separated string of integers.
- `currency_id` supports only ISO 4217 values: `"EUR"`, `"GBP"`, or `"USD"`.
- Dates must follow ISO 8601 format: `YYYY-MM-DD`. (Laravel date format by default)
- Trip must be at least 1 day and end date should be greater than start date.
- The token must be included in the `Authorization: Bearer <token>` header for protected endpoints.
- User inputs are persisted in a DB
- The app was built with room for scalability, reusability and it's modular

---

## Running Tests

```bash
php artisan test
```

Covers:

- Happy path calculations
- Age group boundaries
- Invalid age exceptions

---

## Sample Request

**POST** `/api/quotation`

```json
{
  "age": "28,35",
  "currency_id": "EUR",
  "start_date": "2020-10-01",
  "end_date": "2020-10-30"
}
```

### Response

```json
{
  "message": "quotation fetched successfully",
  "data": {
    "quotation_id": 10,
    "total": "198.00",
    "currency_id": "EUR"
  }
}
```

---

## Swagger Doc

```yaml
openapi: 3.0.0
info:
  title: Quotation API
  version: 1.0.0
  description: API to calculate airo quotations
servers:
  - url: http://localhost:8000/api

paths:
  /auth/register:
    post:
      summary: Register a new user
      tags:
        - Auth
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              required:
                - name
                - email
                - password
                - password_confirmation
              properties:
                name:
                  type: string
                email:
                  type: string
                password:
                  type: string
                password_confirmation:
                  type: string
      responses:
        '201':
          description: User registered successfully
        '422':
          description: Validation error

  /auth/login:
    post:
      summary: Authenticate and receive JWT
      tags:
        - Auth
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              required:
                - email
                - password
              properties:
                email:
                  type: string
                  example: user@example.com
                password:
                  type: string
                  example: yourpassword
      responses:
        '200':
          description: Login successful
        '401':
          description: Unauthorized
  /quotation:
    post:
      tags:
        - Quotation
      summary: Generate a new quotation
      security:
        - bearerAuth: []
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              required: [age, currency_id, start_date, end_date]
              properties:
                age:
                  type: string
                  example: "28,35"
                currency_id:
                  type: string
                  enum: [EUR, USD, GBP]
                  example: "EUR"
                start_date:
                  type: string
                  format: date
                  example: "2020-10-01"
                end_date:
                  type: string
                  format: date
                  example: "2020-10-30"
      responses:
        '200':
          description: Success
          content:
            application/json:
              schema:
                type: object
                properties:
                  data:
                    type: object
                    properties:
                      quotation_id:
                        type: integer
                      total:
                        type: string
                      currency_id:
                        type: string
        '401':
          description: Unauthorized
        '422':
          description: Validation error

components:
  securitySchemes:
    bearerAuth:
      type: http
      scheme: bearer
      bearerFormat: JWT

```

---

## Frontend Usage

Assuming you served the application on port 8000:

- Login via `http://localhost:8000/index.html` or Register via `http://localhost:8000/register.html`
- Token stored in `localStorage`
- Protected quotation form at `http://localhost:8000/quotation.html`
- Toasts for success/errors
- Auto-redirect if token is missing or expired

---

## DB Schema

```sql
CREATE TABLE quotations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  age VARCHAR(255),
  currency_id VARCHAR(10),
  start_date DATE,
  end_date DATE,
  total DECIMAL(10, 2),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

---

## My Approach

Althouth this is a simple task that could be completed in one or two method, I took a modular and scalable approach to building this application by separating concerns and ensuring reusability, I love to use technologies/stack the way they are intended for efficiency:

- **Service Layer Pattern**: Business logic is encapsulated in a `QuotationService`, making the code more testable and clean.
- **Form Request Validation**: Used Laravel’s FormRequest classes to validate incoming data before processing.
- **API Resource**: Structured response with a `QuotationResource` to ensure consistent JSON output format.
- **JWT Authentication**: Implemented using `php-open-source-saver/jwt-auth` to secure endpoints and authorized access.
- **Frontend**: Plain HTML and JavaScript with `fetch` for API calls, `localStorage` for token management, and toast notifications.
- **Edge Case Handling**: Proper error messages and validation for age groups, trip length, and currency support.
- **RESTful Compliance**: Endpoints follow REST standards including status codes, naming, and structure.
- I saved the fixed rates and age group in config/quotation.php for abstraction and can be changed without making changes to logic. Fixed rates can be changed with a simple env update.

This makes the project easy to extend (e.g., adding more quote rules, new resources).

### Improvements

Currently all request are persisted but we could make it idempotent depending on the objective of the app. We could avoid duplicate records and return the existing quotation for the same sets of inputs.

---
