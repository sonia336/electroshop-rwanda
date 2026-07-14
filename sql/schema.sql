-- ============================================================
-- ElectroShop Rwanda - Database Schema
-- E-Commerce and Web Application Final Project
-- ============================================================

CREATE DATABASE IF NOT EXISTS electroshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE electroshop;

-- ------------------------------------------------------------
-- Table: categories
-- ------------------------------------------------------------
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255) DEFAULT NULL
);

-- ------------------------------------------------------------
-- Table: products
-- ------------------------------------------------------------
CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    image_url VARCHAR(255) DEFAULT 'assets/images/placeholder.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL
);

-- ------------------------------------------------------------
-- Table: users (customers)
-- ------------------------------------------------------------
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'customer',
    phone VARCHAR(20) DEFAULT NULL,
    address VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- Table: orders
-- ------------------------------------------------------------
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    customer_name VARCHAR(150) NOT NULL,
    customer_email VARCHAR(150) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    shipping_address VARCHAR(255) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
);

-- ------------------------------------------------------------
-- Table: order_items
-- ------------------------------------------------------------
CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE RESTRICT
);

-- ============================================================
-- Sample data
-- ============================================================

INSERT INTO categories (name, description) VALUES
('Smartphones', 'Mobile phones and accessories'),
('Laptops', 'Laptops and notebooks'),
('Audio', 'Headphones, earbuds and speakers'),
('Accessories', 'Chargers, cables and other gadgets');

INSERT INTO products (category_id, name, description, price, stock_quantity, image_url) VALUES
(1, 'Tecno Spark 20', 'Affordable smartphone with 128GB storage and 50MP camera.', 145000.00, 25, 'assets/images/phone1.jpg'),
(1, 'Samsung Galaxy A15', '6.5-inch display, 4GB RAM, 128GB storage.', 210000.00, 15, 'assets/images/phone2.jpg'),
(2, 'HP Pavilion 15', 'Intel Core i5, 8GB RAM, 512GB SSD laptop.', 650000.00, 10, 'assets/images/laptop1.jpg'),
(2, 'Lenovo IdeaPad 3', 'Intel Core i3, 8GB RAM, 256GB SSD.', 480000.00, 12, 'assets/images/laptop2.jpg'),
(3, 'JBL Tune 510BT', 'Wireless on-ear headphones with 40h battery life.', 45000.00, 30, 'assets/images/audio1.jpg'),
(3, 'Oraimo FreePods 3', 'True wireless earbuds with charging case.', 25000.00, 40, 'assets/images/audio2.jpg'),
(4, 'Anker 20W Charger', 'Fast USB-C wall charger.', 15000.00, 50, 'assets/images/acc1.jpg'),
(4, 'USB-C Cable 1m', 'Durable braided charging cable.', 5000.00, 60, 'assets/images/acc2.jpg');
