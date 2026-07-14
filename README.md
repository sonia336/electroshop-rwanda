# ElectroShop Rwanda 🛒⚡

A full-stack E-Commerce web application for an electronics shop in Rwanda, built with **PHP** and **MySQL**, containerized with **Docker**, and deployed with an automated **CI/CD pipeline** via GitHub Actions.

> Final Project — EWA408510: E-Commerce and Web Application
> Instructor: Eric Maniraguha

---

## 🌐 Live Demo

- **Live Application:** _[Add your Render deployment URL here after deploying]_
- **GitHub Repository:** _[Add your GitHub repo URL here]_

---

## 📋 Features

### Customer-Facing
- Responsive, mobile-friendly homepage with category navigation
- Product listing with **search** and **category filtering**
- Product details page with stock indication
- Session-based shopping cart (add / update quantity / remove / clear)
- Multi-step checkout with server-side form validation
- Order confirmation page with full order summary
- Customer registration & login (secure password hashing)
- Order history for logged-in customers

### Technical / DevOps
- MySQL database with normalized schema (`categories`, `products`, `users`, `orders`, `order_items`)
- Prepared statements (PDO) throughout — protects against SQL injection
- CSRF tokens on all forms that mutate data
- Password hashing via PHP's `password_hash()` / `password_verify()`
- Dockerized application (PHP-Apache + MySQL + phpMyAdmin via `docker-compose`)
- GitHub Actions CI/CD pipeline: lint → automated smoke tests → Docker build → deploy trigger

---

## 🛠️ Technologies Used

| Layer            | Technology                       |
|-------------------|-----------------------------------|
| Backend           | PHP 8.2 (procedural, PDO)         |
| Database          | MySQL 8.0                         |
| Frontend          | HTML5, CSS3, vanilla JavaScript   |
| Containerization  | Docker, Docker Compose            |
| CI/CD             | GitHub Actions                    |
| Deployment        | Render (Docker-based web service) |

---

## 🗂️ Project Structure

```
electroshop/
├── assets/
│   ├── css/style.css
│   ├── js/script.js
│   └── images/
├── includes/
│   ├── db.php            # PDO database connection
│   ├── functions.php      # Helpers: auth, cart, security
│   ├── header.php
│   └── footer.php
├── sql/
│   └── schema.sql        # Database schema + sample data
├── tests/
│   └── smoke_test.php    # Lightweight automated tests
├── .github/workflows/
│   └── ci-cd.yml          # CI/CD pipeline definition
├── index.php              # Homepage
├── products.php           # Product listing + search/filter
├── product-details.php
├── cart.php
├── cart-actions.php        # Handles add/update/remove/clear
├── checkout.php
├── process-order.php       # Order transaction logic
├── order-confirmation.php
├── register.php / login.php / logout.php
├── orders.php              # Customer order history
├── Dockerfile
├── docker-compose.yml
├── .env.example
└── README.md
```

---

## 🗄️ Database Design

**Entities:**
- `categories` (1) → (many) `products`
- `users` (1) → (many) `orders`
- `orders` (1) → (many) `order_items`
- `products` (1) → (many) `order_items`

See [`sql/schema.sql`](sql/schema.sql) for full DDL and sample seed data.

---

## 🚀 Running Locally with Docker (Recommended)

**Prerequisites:** Docker & Docker Compose installed.

```bash
# 1. Clone the repository
git clone <your-repo-url>
cd electroshop

# 2. Build and start all services
docker-compose up --build

# 3. Visit the app
# App:         http://localhost:8080
# phpMyAdmin:  http://localhost:8081  (server: db, user: electro_user, pass: electro_pass)
```

The MySQL database is automatically initialized with `sql/schema.sql` on first run (via the `docker-entrypoint-initdb.d` mechanism).

To stop:
```bash
docker-compose down
```

To reset the database completely (wipes volume):
```bash
docker-compose down -v
```

---

## 💻 Running Locally without Docker

1. Install PHP 8.2+, Apache/Nginx (or use PHP's built-in server), and MySQL 8.
2. Import `sql/schema.sql` into your MySQL server.
3. Copy `.env.example` to `.env` and update credentials, or export environment variables:
   ```bash
   export DB_HOST=localhost
   export DB_NAME=electroshop
   export DB_USER=root
   export DB_PASS=yourpassword
   ```
4. Serve the app:
   ```bash
   php -S localhost:8000
   ```
5. Visit `http://localhost:8000`

---

## ✅ Running Automated Tests

```bash
php tests/smoke_test.php
```

This validates core helper functions (money formatting, output escaping, cart logic) and is run automatically in the CI pipeline on every push.

---

## 🔄 CI/CD Pipeline

Defined in [`.github/workflows/ci-cd.yml`](.github/workflows/ci-cd.yml). On every push/PR to `main`:

1. **Build & Test job**
   - Checks out code
   - Sets up PHP 8.2 with required extensions
   - Lints every `.php` file for syntax errors
   - Runs the automated smoke test suite
   - Builds the Docker image
   - Runs the container and confirms it starts successfully
2. **Deploy job** (only on push to `main`, only if build-and-test passes)
   - Sends a POST request to a Render **Deploy Hook URL** stored as a GitHub secret (`RENDER_DEPLOY_HOOK_URL`), triggering a fresh deployment

**To enable auto-deploy:** in your GitHub repo, go to `Settings → Secrets and variables → Actions` and add `RENDER_DEPLOY_HOOK_URL` (copy this from your Render service's Settings → Deploy Hook).

---

## 🐳 Docker Implementation

- `Dockerfile`: builds a PHP 8.2 + Apache image with `pdo_mysql` enabled
- `docker-compose.yml`: orchestrates three services — `app` (PHP), `db` (MySQL 8), `phpmyadmin` (DB admin UI)
- Environment variables (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`) connect the app container to the database container by service name (`db`), demonstrating container networking

---

## ☁️ Deployment (Render)

This project deploys to Render as a **Docker-based Web Service**:

1. Push the repository to GitHub.
2. On Render: **New → Web Service → Build and deploy from a Git repository**.
3. Select **Docker** as the environment (Render detects the `Dockerfile` automatically).
4. Add a managed MySQL database (Render or an external provider like PlanetScale/Aiven), and set environment variables `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` in the Render dashboard to match.
5. Import `sql/schema.sql` into that database once, manually (via a MySQL client).
6. Copy the Render **Deploy Hook URL** into the GitHub secret `RENDER_DEPLOY_HOOK_URL` to enable automatic redeploys from CI/CD.

---

## 🔐 Security Measures Implemented

- All SQL queries use PDO **prepared statements** (no string-concatenated SQL)
- Passwords hashed with `password_hash()` (bcrypt), verified with `password_verify()`
- **CSRF tokens** validated on every state-changing form submission
- All output escaped with `htmlspecialchars()` to prevent XSS
- Server-side validation duplicated on top of client-side `required`/`pattern` attributes (never trust the client)
- Generic authentication error messages (prevents user enumeration)
- Order totals recalculated server-side from the database — cart/session data is never trusted for pricing

---

## 🧩 Future Enhancements

- Payment gateway integration (MTN/Airtel Mobile Money, Stripe)
- Admin dashboard for managing products/orders
- Product reviews and ratings
- Email notifications on order placement
- Wishlist functionality
- Progressive Web App (PWA) support

---

## 👤 Author

Developed as an individual final project for **EWA408510 — E-Commerce and Web Application**.
