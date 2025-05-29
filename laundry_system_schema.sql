-- Drop tables if they exist (for development/testing)
DROP TABLE IF EXISTS order_addons;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS addons;
DROP TABLE IF EXISTS customers;

-- Customers table
CREATE TABLE customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact_number VARCHAR(30) NOT NULL,
    UNIQUE KEY unique_contact (contact_number)
);

-- Add-ons table
CREATE TABLE addons (
    addon_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL
);

-- Orders table
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    order_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    order_reference VARCHAR(20) NOT NULL,
    order_weight DECIMAL(5,2) NOT NULL,
    delivery_type ENUM('Pickup', 'Delivery') NOT NULL,
    delivery_location VARCHAR(255),
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
    delivery_fee DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    payment_status ENUM('Not yet Paid', 'Paid') NOT NULL DEFAULT 'Not yet Paid',
    order_status ENUM('Not Started', 'In Progress', 'Done') NOT NULL DEFAULT 'Not Started',
    retrieval_status ENUM('To be Claimed', 'To be Delivered', 'Claimed', 'Delivered', 'Completed') NOT NULL DEFAULT 'To be Claimed',
    is_completed TINYINT(1) NOT NULL DEFAULT 0,
    completed_at DATETIME NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
);

-- Order Add-ons table (many-to-many relationship)
CREATE TABLE order_addons (
    order_addon_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    addon_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (addon_id) REFERENCES addons(addon_id)
);

-- Insert default add-ons
INSERT INTO addons (name, price) VALUES
('Towels', 30.00),
('Bedsheets/Pillowcases', 50.00),
('Comforters', 75.00);