CREATE TABLE IF NOT EXISTS `project_timeline` (
 `project_timeline_id` int NOT NULL AUTO_INCREMENT,
 `project_id` int NOT NULL,
 `change_type` enum('date_change','cost_update','status_change','renewal') NOT NULL,
 `project_status` tinyint(1) DEFAULT NULL,
 `start_date` date DEFAULT NULL,
 `end_date` date DEFAULT NULL,
 `new_cost` int NOT NULL DEFAULT '0',
 `description` varchar(255) DEFAULT NULL,
 `created_by` int NOT NULL,
 `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`project_timeline_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;