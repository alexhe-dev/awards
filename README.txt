Award Banking and Redeeming Application
April 2025
By: Alex He
-------------------------------------------------------------------------------------------
Description:

This is a PHP-based web applications that allows managers and employees to
login into their accounts, and perform various tasks specific to their roles.
It stores and retrieves data using mysql.
-------------------------------------------------------------------------------------------
Important Notes:

Please use PHP 7.0 or newer.
Employee's total points get updated dynamically.
Activity log keeps track of point addition and point redemption.
If conn with database is not secure, program will not work.
If there are issues with database connection, please inspect db.php
to verify that the parameters are correct for your device.
If you already have a database named GP25, you would need to drop that database
for the .sql file to work properly.
Activity logs are on delete cascade.
I tried to add good amount of comments, I apologize if it does not look very tidy.
-------------------------------------------------------------------------------------------
Instructions for Setup:

Setup Database
1. Make sure initialize_database.sql is accessible (go to directory with the file).
2. Log into mysql (root or another user): mysql -u root -p < initialize_database.sql
3. This automatically loads the default database associated with this application.

Run the PHP code
1. Once the database is set up, make sure parameters in db.php is in order.
2. You can directly access the website through an IDE or your local server.
3. This can be done through any of the .php files, best if index.php (Login page)
4. Access the site via a browser and boom! You are good to go!

Login (quick reference)
Manager - User ID: Evan  Password: Wallis
Employee - User ID: Joyce  Password: English

-------------------------------------------------------------------------------------------
Files:

index.php - Login page
employee.php - Employee dashboard
manager.php - Manager dashboard
db.php - Contains database connection code
activity.php - Displays a log of all employee activities (points earned/redeemed)
addPoints.php - Allows managers to add points to employees’ accounts
addRemoveEmployees.php - Interface for managers to add or remove employees from the system
logout.php - Safely logs users out
modifyProducts.php - Allows managers to update product quantity
redeem.php - Handles the redemption of products by employees based on available points
-------------------------------------------------------------------------------------------
Schema Structure:

CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    group_no INT,
    userid VARCHAR(50) UNIQUE,
    password VARCHAR(50),
    initial_balance INT
);

CREATE TABLE managers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    userid VARCHAR(50) UNIQUE,
    password VARCHAR(50)
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100),
    points_required INT,
    quantity INT
);

CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    activity_type ENUM('earned', 'redeemed') NOT NULL,
    points INT DEFAULT 0,
    activity_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    info VARCHAR(50),
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);
-------------------------------------------------------------------------------------------
Default Data:

Employees
(name, group_no, userid, password, initial_balance)
('Joyce English', 5, 'Joyce', 'English', 0),
('Ahmad Jabbar', 5, 'Ahmad', 'Jabbar', 500),
('Andy Vile', 7, 'Andy', 'Vile', 100),
('Jill Jarvis', 8, 'Jill', 'Jarvis', 1000),
('Billie King', 8, 'Billie', 'King', 300);

Managers
(name, userid, password)
('Evan Wallis', 'Evan', 'Wallis');

Products
(product_name, points_required, quantity)
('Mug', 100, 3),
('T-shirt (one size)', 650, 1),
('Backpack', 250, 1);
-------------------------------------------------------------------------------------------






