CREATE TABLE IF NOT EXISTS `projects_tag_logs` (
 `tag_id` int NOT NULL AUTO_INCREMENT,
 `tag_name` varchar(100) NOT NULL,
 `project_id` int NOT NULL DEFAULT '0',
 `created_by` int NOT NULL DEFAULT '0',
 `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`tag_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;