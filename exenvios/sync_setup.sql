-- Database synchronization setup for services, locations, and lockers

-- 1. Locations Table
CREATE TABLE IF NOT EXISTS `locations` (
    `id` VARCHAR(50) PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `address` TEXT,
    `sched_info` VARCHAR(255), -- Example: "Seg a Sex às 18h"
    `express_info` VARCHAR(255), -- Example: "Em até 30 min"
    `type` VARCHAR(50) DEFAULT 'Unidade',
    `totens` VARCHAR(50) DEFAULT '01',
    `occupancy` VARCHAR(50) DEFAULT '5/10',
    `status` VARCHAR(20) DEFAULT 'online',
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Shipping Services Table (Coleta types, packaging, etc)
CREATE TABLE IF NOT EXISTS `shipping_services` (
    `id` VARCHAR(50) PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `category` ENUM('empacotamento', 'coleta', 'fretes') NOT NULL,
    `method` ENUM('fixo', 'markup', 'faixa') NOT NULL,
    `price` DECIMAL(10,2) DEFAULT 0.00,
    `base_price` DECIMAL(10,2) DEFAULT 0.00,
    `markup` DECIMAL(10,2) DEFAULT 0.00,
    `description` TEXT,
    `status` VARCHAR(20) DEFAULT 'active',
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Lockers Table
CREATE TABLE IF NOT EXISTS `lockers` (
    `id` VARCHAR(50) PRIMARY KEY,
    `label` VARCHAR(100) NOT NULL, -- Example: "T-0428"
    `location_id` VARCHAR(50),
    `hardware_ref` VARCHAR(100),
    `type` VARCHAR(50) DEFAULT 'Hibrido',
    `status` ENUM('online', 'offline', 'manutencao') DEFAULT 'online',
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`location_id`) REFERENCES `locations`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Initial Seed Data
INSERT INTO `shipping_services` (`id`, `name`, `category`, `method`, `price`, `description`) VALUES
('col-01', 'Coleta Programada', 'coleta', 'fixo', 12.00, 'Defina dia e horário.'),
('col-02', 'Coleta Express', 'coleta', 'fixo', 5.00, 'Receba em minutos.'),
('col-03', 'Locker Inteligente', 'coleta', 'fixo', 0.00, 'Deixe no box seguro.');

INSERT INTO `locations` (`id`, `name`, `address`, `sched_info`, `express_info`) VALUES
('loc-01', 'EX-ENVIOS MATRIZ', 'Av. Afonso Pena, 1234', 'Seg a Sex às 18h', 'Em até 30 min'),
('loc-02', 'EX-ENVIOS SHOPPING', 'Rua Amazonas, 567', 'Seg a Sáb às 20h', 'Em até 60 min');

INSERT INTO `lockers` (`id`, `label`, `location_id`, `status`) VALUES
('t-01', 'T-0428', 'loc-01', 'online');
