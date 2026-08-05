-- SQL Migration Script for Customer Management
-- Run this in your mart_pos_system database

-- Create online_customers table if it doesn't exist
CREATE TABLE IF NOT EXISTS `online_customers` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `phone` VARCHAR(20),
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_active` BOOLEAN DEFAULT TRUE,
  INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add customer_id column to online_orders if it doesn't exist
ALTER TABLE `online_orders` 
ADD COLUMN `customer_id` INT NULL AFTER `id`,
ADD FOREIGN KEY (`customer_id`) REFERENCES `online_customers`(`id`) ON DELETE CASCADE;

-- Update existing online_orders to have timestamps if needed
ALTER TABLE `online_orders` 
ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP IF NOT EXISTS,
ADD COLUMN `order_status` VARCHAR(50) DEFAULT 'pending' IF NOT EXISTS;

-- Add address field if it doesn't exist
ALTER TABLE `online_orders` 
ADD COLUMN `customer_address` VARCHAR(500) IF NOT EXISTS;

-- Create order_items table if it doesn't exist  
CREATE TABLE IF NOT EXISTS `online_order_items` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `price` DECIMAL(10, 2) NOT NULL,
  `quantity` INT NOT NULL,
  `subtotal` DECIMAL(10, 2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `online_orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT,
  INDEX idx_order_id (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
