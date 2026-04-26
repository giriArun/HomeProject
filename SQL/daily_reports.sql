CREATE TABLE IF NOT EXISTS `daily_reports` (
 `daily_report_id` int NOT NULL AUTO_INCREMENT,
 `price` int NOT NULL,
 `date` date NOT NULL,
 `customer_id` int DEFAULT '0',
 `project_id` int NOT NULL,
 `user_id` int DEFAULT '0',
 `is_credit` tinyint(1) NOT NULL DEFAULT '0',
 `tags` varchar(255) DEFAULT NULL,
 `notes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
 `created_by` int DEFAULT NULL,
 `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `modified_by` int DEFAULT NULL,
 `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`daily_report_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;