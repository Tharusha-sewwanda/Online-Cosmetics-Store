# Bellina Cosmetics — Online Cosmetics Store

A full-stack web application built for **SENG 21253 — Web Application Development (Practical)**.
Stack: **HTML / CSS / Vanilla JavaScript** (frontend) + **PHP / MySQL** (backend) — no frontend or backend frameworks, as required.

---

## 1. Features

### Customer
- Homepage catalog with product name, brand, shade/variant, price, and image (`index.php`)
- Live search (by name/brand) and filter by category (Skincare/Makeup/Fragrance/Accessories) and skin type (Oily/Dry/Combination), powered by a vanilla-JS AJAX call to `api/search.php`
- Product detail page (`product.php`) with add-to-cart
- Session-based shopping cart (`cart.php`) — update quantity / remove items via AJAX
- Checkout (`checkout.php`) with customer details form + a **simulated payment gateway** (Card or Cash on Delivery). On success, an order + order_items rows are created and stock is decremented.
- Order confirmation page (`order_success.php`)

### Administrator
- Secure login (bcrypt-hashed password, PHP sessions) — `admin/login.php`
- Dashboard with key stats (products, low stock, orders, revenue) — `admin/dashboard.php`
- Inventory CRUD: add / edit / delete products — `admin/products.php`, `admin/product_form.php`, `admin/product_delete.php`
- Order management: view all orders, view line items, update payment/order status — `admin/orders.php`, `admin/order_detail.php`

---

## 2. Database Schema (`database.sql`)

| Table         | Purpose                                                        |
|---------------|------------------------------------------------------------------|
| `admins`      | Admin login credentials (bcrypt hash)                          |
| `products`    | Catalog: name, brand, category, skin_type, shade/variant, price, stock, image, description |
| `orders`      | One row per checkout: customer info, total, payment method/status, order status |
| `order_items` | Line items per order (snapshot of product name/price at time of sale) |

Relationships: `orders 1—N order_items`, `order_items N—1 products` (nullable FK, so deleting a product doesn't break historical orders).

---

## 3. Setup Instructions (XAMPP / WAMP / LAMP)

1. Copy the `cosmetics-store` folder into your server's web root (e.g. `htdocs/`).
2. Create the database by importing `database.sql` (via phpMyAdmin **Import**, or):
   ```bash
   mysql -u root -p < database.sql
   ```
3. Edit `config/db.php` if your MySQL username/password differ from the defaults (`root` / empty password).
4. **Important:** run `admin/seed_admin.php` **once** in your browser to correctly set the admin password hash for your machine (bcrypt hashes are salted/random, so the placeholder in `database.sql` cannot be reused as-is):
   ```
   http://localhost/cosmetics-store/admin/seed_admin.php
   ```
   This creates/updates the admin login: **username: `admin`  /  password: `admin123`**. Delete this file afterwards for production use.
5. Visit `http://localhost/cosmetics-store/index.php` for the storefront, and `http://localhost/cosmetics-store/admin/login.php` for the admin panel.

This was tested end-to-end on PHP 8.3 + MySQL 8.0 using PHP's built-in server (`php -S`) as well.

---

## 4. Project Structure

```
cosmetics-store/
├── database.sql              # Schema + seed data
├── config/db.php             # PDO MySQL connection
├── includes/                 # functions.php (cart/product helpers), auth.php, header/footer
├── assets/css/style.css      # Global stylesheet (pure CSS, no framework)
├── assets/js/main.js         # AJAX search/filter, cart updates, checkout validation
├── api/search.php            # JSON endpoint consumed by main.js
├── index.php, product.php, cart.php, checkout.php, order_success.php
└── admin/
    ├── login.php, logout.php, seed_admin.php
    ├── dashboard.php, products.php, product_form.php, product_delete.php
    ├── orders.php, order_detail.php
    └── includes/admin_header.php, admin_footer.php
```

---

## 5. Notes on the Payment Gateway

Since a real payment provider account isn't available for coursework, `checkout.php` implements a **simulated gateway**: card details are validated for presence (not charged anywhere), the order is marked `Paid` immediately, and stock is decremented. This isolates the checkout flow so a real gateway (e.g. Stripe, PayHere) could later be swapped in by replacing the block marked `Mock payment gateway` in `checkout.php` with an actual API call — the rest of the order-creation logic wouldn't need to change.

---

## 6. Team Collaboration

- Create a GitHub repository, `git init` inside this folder, and push.
- Each member should work on a separate feature branch (e.g. `feature/checkout`, `feature/admin-crud`, `feature/search-filter`) and merge via pull requests so the commit history reflects individual contributions.
- Suggested split of work for the presentation's "System Architecture" + "AI Usage" sections: one member covers DB schema, one covers customer-facing flow, one covers admin flow, one covers the live demo/payment simulation.
