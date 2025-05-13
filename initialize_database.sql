-- 1. Create the Database
CREATE DATABASE IF NOT EXISTS GP25;

-- 2. Use the Created Database
USE GP25;

-- 3. Create the 'employees' Table
CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    group_no INT,
    userid VARCHAR(50) UNIQUE,
    password VARCHAR(50),
    initial_balance INT
    );

-- 4. Create the 'managers' Table
CREATE TABLE IF NOT EXISTS managers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    userid VARCHAR(50) UNIQUE,
    password VARCHAR(50)
    );

-- 5. Create the 'products' Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100),
    points_required INT,
    quantity INT
    );

-- 6. Create the 'activity_log' Table
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    activity_type ENUM('earned', 'redeemed') NOT NULL,
    points INT DEFAULT 0,
    activity_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    info VARCHAR(50),
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
    );

-- Insert Test Data

-- Insert example employees
INSERT INTO employees (name, group_no, userid, password, initial_balance)
VALUES
    ('Joyce English', 5, 'Joyce', 'English', 0),
    ('Ahmad Jabbar', 5, 'Ahmad', 'Jabbar', 500),
    ('Andy Vile', 7, 'Andy', 'Vile', 100),
    ('Jill Jarvis', 8, 'Jill', 'Jarvis', 1000),
    ('Billie King', 8, 'Billie', 'King', 300);

-- Insert example managers
INSERT INTO managers (name, userid, password)
VALUES ('Evan Wallis', 'Evan', 'Wallis');

-- Insert example products into the products table
INSERT INTO products (product_name, points_required, quantity)
VALUES
    ('Mug', 100, 3),
    ('T-shirt (one size)', 650, 1),
    ('Backpack', 250, 1);
