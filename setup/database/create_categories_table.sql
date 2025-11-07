-- Create categories table if it doesn't exist
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data (optional)
INSERT IGNORE INTO `categories` (`name`, `description`, `created_at`) VALUES
('Electronics', 'Electronic devices and gadgets', NOW()),
('Accessories', 'Tech accessories and peripherals', NOW()),
('Cables', 'Various types of cables and adapters', NOW()),
('Furniture', 'Office and home furniture items', NOW()),
('Clothing', 'Apparel and fashion items', NOW());








