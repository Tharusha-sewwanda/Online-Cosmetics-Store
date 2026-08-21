-- =========================================================
-- Online Cosmetics Store - Database Schema
-- SENG 21253 - Web Application Development (Practical)
-- =========================================================

CREATE DATABASE IF NOT EXISTS cosmetics_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cosmetics_store;

-- ---------------------------------------------------------
-- Table: admins
-- Stores administrator (store manager) login credentials
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,  -- bcrypt hash
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------
-- Table: products
-- Stores the cosmetics catalog
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    brand VARCHAR(100) NOT NULL,
    category ENUM('Skincare', 'Makeup', 'Fragrance', 'Accessories') NOT NULL,
    skin_type ENUM('Oily', 'Dry', 'Combination', 'All') NOT NULL DEFAULT 'All',
    shade_variant VARCHAR(100) DEFAULT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    image_url VARCHAR(255) DEFAULT 'assets/images/placeholder.png',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------
-- Table: orders
-- Stores each customer checkout/purchase
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address VARCHAR(255) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('Card', 'Cash on Delivery') NOT NULL DEFAULT 'Card',
    payment_status ENUM('Pending', 'Paid', 'Failed') NOT NULL DEFAULT 'Pending',
    order_status ENUM('Processing', 'Shipped', 'Delivered', 'Cancelled') NOT NULL DEFAULT 'Processing',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------
-- Table: order_items
-- Line items belonging to an order (snapshot of product at time of sale)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT DEFAULT NULL,
    product_name VARCHAR(150) NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- ---------------------------------------------------------
-- Seed data: default admin (username: admin / password: admin123)
-- Password hash below corresponds to 'admin123' via PHP password_hash()
-- ---------------------------------------------------------
INSERT INTO admins (username, password, full_name) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Store Administrator');
-- NOTE: The hash above is a PLACEHOLDER pattern length-correct bcrypt hash.
-- Run: php -r "echo password_hash('admin123', PASSWORD_DEFAULT);"
-- and REPLACE the value above with the freshly generated hash before using,
-- OR simply run admin/seed_admin.php included in this project once.

-- ---------------------------------------------------------
-- Seed data: sample products
-- ---------------------------------------------------------
INSERT INTO products (name, brand, category, skin_type, shade_variant, price, stock_quantity, image_url, description) VALUES
('Hydrating Facial Serum', 'GlowLab', 'Skincare', 'Dry', '30ml', 3500.00, 25, 'https://picsum.photos/seed/serum1/400/400', 'A lightweight hyaluronic acid serum that deeply hydrates and plumps the skin.'),
('Oil Control Face Wash', 'PureSkin', 'Skincare', 'Oily', '150ml', 1200.00, 40, 'https://picsum.photos/seed/facewash1/400/400', 'Foaming cleanser formulated to control excess oil and prevent breakouts.'),
('Matte Liquid Lipstick', 'Bellina', 'Makeup', 'All', 'Ruby Red', 1800.00, 30, 'https://picsum.photos/seed/lipstick1/400/400', 'Long-lasting, transfer-proof matte liquid lipstick.'),
('Matte Liquid Lipstick', 'Bellina', 'Makeup', 'All', 'Nude Blush', 1800.00, 22, 'https://picsum.photos/seed/lipstick2/400/400', 'Long-lasting, transfer-proof matte liquid lipstick.'),
('Full Coverage Foundation', 'Bellina', 'Makeup', 'Combination', 'Shade 220 - Warm Beige', 2900.00, 18, 'https://picsum.photos/seed/foundation1/400/400', 'Buildable full-coverage foundation with a natural matte finish.'),
('Citrus Bloom Eau de Parfum', 'AromaHouse', 'Fragrance', 'All', '50ml', 6500.00, 15, 'https://picsum.photos/seed/perfume1/400/400', 'A fresh citrus fragrance with notes of bergamot, jasmine, and musk.'),
('Velvet Rose Eau de Parfum', 'AromaHouse', 'Fragrance', 'All', '50ml', 6800.00, 12, 'https://picsum.photos/seed/perfume2/400/400', 'A romantic floral fragrance with rose, peony, and sandalwood.'),
('Aloe Vera Moisturizing Gel', 'GlowLab', 'Skincare', 'Combination', '200ml', 1450.00, 35, 'https://picsum.photos/seed/aloegel1/400/400', 'Soothing, non-greasy gel moisturizer suitable for all skin types.'),
('Charcoal Clay Mask', 'PureSkin', 'Skincare', 'Oily', '100g', 1650.00, 20, 'https://picsum.photos/seed/claymask1/400/400', 'Deep-cleansing clay mask that draws out impurities and excess oil.'),
('Makeup Brush Set (12pcs)', 'Bellina', 'Accessories', 'All', 'Rose Gold', 2500.00, 10, 'https://picsum.photos/seed/brushset1/400/400', 'Professional 12-piece makeup brush set with soft synthetic bristles.'),
('Volumizing Mascara', 'Bellina', 'Makeup', 'All', 'Jet Black', 1350.00, 28, 'https://picsum.photos/seed/mascara1/400/400', 'Smudge-proof mascara for dramatic volume and length.'),
('Gentle Micellar Water', 'PureSkin', 'Skincare', 'Dry', '400ml', 1550.00, 33, 'https://picsum.photos/seed/micellar1/400/400', 'Alcohol-free micellar water that removes makeup and impurities gently.');
