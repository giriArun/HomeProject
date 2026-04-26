-- Create the customers table only if it does not already exist.
CREATE TABLE IF NOT EXISTS `customers` (
 `customer_id` int NOT NULL AUTO_INCREMENT,
 `customer_name` varchar(100) NOT NULL,
 `customer_address` varchar(255) DEFAULT NULL,
 `customer_phone` varchar(12) DEFAULT NULL,
 `is_active` tinyint(1) NOT NULL DEFAULT '1',
 `created_by` int DEFAULT NULL,
 `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `modified_by` int DEFAULT NULL,
 `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
