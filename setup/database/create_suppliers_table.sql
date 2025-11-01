-- Create suppliers table if it doesn't exist
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) NOT NULL,
  `contact_person` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_name` (`company_name`),
  UNIQUE KEY `phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data (optional)
INSERT IGNORE INTO `suppliers` (`company_name`, `contact_person`, `phone`, `email`, `created_at`) VALUES
('TechSource Pvt Ltd', 'Nimal Perera', '0771234567', 'sales@techsource.lk', NOW()),
('GreenLeaf Trading', 'Sithara Fernando', '0715558899', 'contact@greenleaf.lk', NOW()),
('SilverLine Imports', 'Kasun Jayasinghe', '0702223344', 'hello@silverline.lk', NOW());

