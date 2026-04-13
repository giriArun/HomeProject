-- Create the user_meta table only if it does not already exist.
CREATE TABLE IF NOT EXISTS `user_meta` (
  `meta_id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `meta_key` VARCHAR(255) NOT NULL,
  `meta_value` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `idx_user_meta_user_id` (`user_id`),
  KEY `idx_user_meta_key` (`meta_key`),
  CONSTRAINT `fk_user_meta_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
