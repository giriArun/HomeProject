CREATE TABLE IF NOT EXISTS `projects` (
 `project_id` int NOT NULL AUTO_INCREMENT,
 `project_name` varchar(255) NOT NULL,
 `project_start_year` year DEFAULT NULL,
 `project_end_year` year DEFAULT NULL,
 `is_active` tinyint(1) NOT NULL DEFAULT '1',
 `project_tags` text COMMENT 'comma separated values',
 `created_by` int DEFAULT NULL,
 `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `modified_by` int DEFAULT NULL,
 `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;