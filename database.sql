CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    location VARCHAR(100) NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    profile_picture VARCHAR(255),
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

CREATE TABLE `service_requests` (
  `request_id` INT NOT NULL AUTO_INCREMENT,
  `special_instructions` TEXT,
  `quantity` DECIMAL(10,2) NOT NULL,
  `waste_type` VARCHAR(50) NOT NULL,
  `collection_time` VARCHAR(20) NOT NULL,
  `collection_date` VARCHAR(20) NOT NULL,
  `request_date` VARCHAR(20) NOT NULL,
  `assigned_driver_id` VARCHAR(20) NOT NULL,
  `status` VARCHAR(20) DEFAULT 'Pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `drivers` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(100) NOT NULL,
  `vehicle_type` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `vehicle_number` VARCHAR(20) NOT NULL,
  `license_number` VARCHAR(50) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `registration_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `vehicle_number` (`vehicle_number`),
  UNIQUE KEY `license_number` (`license_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id VARCHAR(50) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    transaction_date DATETIME DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE `feedback` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `comments` TEXT NOT NULL,
  `rating` INT NOT NULL,
  `email` VARCHAR(100),
  `date_added` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;




