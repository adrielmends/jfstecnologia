-- Execute this in your MySQL database (e.g., via phpMyAdmin)

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `cpf` VARCHAR(20) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `balance` DECIMAL(10,2) DEFAULT 0.00,
  `role` VARCHAR(20) DEFAULT 'user',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `external_ref` VARCHAR(100) NOT NULL,
  `channel` VARCHAR(50) DEFAULT 'WEB',
  `locker_id` VARCHAR(100) DEFAULT NULL,
  `device_id` VARCHAR(100) DEFAULT NULL,
  `customer_name` VARCHAR(255) DEFAULT NULL,
  `customer_cpf` VARCHAR(20) DEFAULT NULL,
  `necessity` VARCHAR(255) DEFAULT NULL,
  `packaging` VARCHAR(255) DEFAULT NULL,
  `service` VARCHAR(255) DEFAULT NULL,
  `total_value` DECIMAL(10,2) NOT NULL,
  `status` VARCHAR(50) DEFAULT 'pending',
  `asaas_id` VARCHAR(100) DEFAULT NULL,
  `sender_cep` VARCHAR(10) DEFAULT NULL,
  `sender_doc` VARCHAR(20) DEFAULT NULL,
  `sender_name` VARCHAR(255) DEFAULT NULL,
  `sender_street` VARCHAR(255) DEFAULT NULL,
  `sender_number` VARCHAR(50) DEFAULT NULL,
  `sender_complement` VARCHAR(255) DEFAULT NULL,
  `sender_neighborhood` VARCHAR(100) DEFAULT NULL,
  `sender_city_uf` VARCHAR(100) DEFAULT NULL,
  `receiver_cep` VARCHAR(10) DEFAULT NULL,
  `receiver_doc` VARCHAR(20) DEFAULT NULL,
  `receiver_name` VARCHAR(255) DEFAULT NULL,
  `receiver_contact` VARCHAR(255) DEFAULT NULL,
  `receiver_street` VARCHAR(255) DEFAULT NULL,
  `receiver_number` VARCHAR(50) DEFAULT NULL,
  `receiver_complement` VARCHAR(255) DEFAULT NULL,
  `receiver_neighborhood` VARCHAR(100) DEFAULT NULL,
  `receiver_city_uf` VARCHAR(100) DEFAULT NULL,
  `confirmed_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `payments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `provider` VARCHAR(50) DEFAULT NULL,
  `method` VARCHAR(50) DEFAULT NULL,
  `provider_reference` VARCHAR(100) DEFAULT NULL,
  `status` VARCHAR(50) DEFAULT 'PENDING',
  `amount` DECIMAL(10,2) NOT NULL,
  `raw_payload` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
