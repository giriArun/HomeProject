CREATE TABLE IF NOT EXISTS `project_users` (
 `project_users_id` int NOT NULL AUTO_INCREMENT,
 `project_id` int NOT NULL,
 `user_id` int NOT NULL,
 `created_by` int NOT NULL DEFAULT '0',
 `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`project_users_id`),
 UNIQUE KEY `uniq_project_users_project_user` (`project_id`,`user_id`),
 KEY `idx_project_users_project_id` (`project_id`),
 KEY `idx_project_users_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
