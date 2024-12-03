CREATE SCHEMA IF NOT EXISTS `jobstreet`;
USE `jobstreet` ;
CREATE TABLE IF NOT EXISTS `jobstreet`.`categories` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `jobstreet`.`companies` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `company_name` VARCHAR(150) NOT NULL,
  `logo` VARCHAR(255) NOT NULL,
  `scale` VARCHAR(20) NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `website` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(250) NOT NULL,
  `token` LONGTEXT NULL DEFAULT NULL,
  `address` VARCHAR(250) NOT NULL,
  `number_phone` VARCHAR(20) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `companies_company_name_unique` (`company_name` ASC) VISIBLE,
  UNIQUE INDEX `companies_email_unique` (`email` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 15
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `jobstreet`.`jobs` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `company_id` INT NOT NULL,
  `cat_id` INT NOT NULL,
  `position` VARCHAR(50) NOT NULL,
  `salary` VARCHAR(50) NOT NULL,
  `type` VARCHAR(255) NOT NULL,
  `description` LONGTEXT NOT NULL,
  `status` VARCHAR(30) NOT NULL,
  `close_day` DATETIME NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `jobs_company_id_foreign` (`company_id` ASC) VISIBLE,
  INDEX `jobs_cat_id_foreign` (`cat_id` ASC) VISIBLE,
  CONSTRAINT `jobs_cat_id_foreign`
    FOREIGN KEY (`cat_id`)
    REFERENCES `jobstreet`.`categories` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `jobs_company_id_foreign`
    FOREIGN KEY (`company_id`)
    REFERENCES `jobstreet`.`companies` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 18
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `jobstreet`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `avatar` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `token` LONGTEXT NULL DEFAULT NULL,
  `number_phone` VARCHAR(20) NOT NULL,
  `address` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `users_email_unique` (`email` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 21
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `jobstreet`.`applications` (
  `user_id` INT NOT NULL,
  `job_id` INT NOT NULL,
  `cv` VARCHAR(255) NOT NULL,
  `status` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  INDEX `applications_job_id_foreign` (`job_id` ASC) VISIBLE,
  INDEX `applications_user_id_foreign` (`user_id` ASC) VISIBLE,
  CONSTRAINT `applications_job_id_foreign`
    FOREIGN KEY (`job_id`)
    REFERENCES `jobstreet`.`jobs` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `applications_user_id_foreign`
    FOREIGN KEY (`user_id`)
    REFERENCES `jobstreet`.`users` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `jobstreet`.`posts` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NULL DEFAULT NULL,
  `company_id` INT NULL DEFAULT NULL,
  `title` VARCHAR(255) NOT NULL,
  `body` VARCHAR(255) NOT NULL,
  `image` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `posts_user_id_foreign` (`user_id` ASC) VISIBLE,
  INDEX `company_id` (`company_id` ASC) VISIBLE,
  CONSTRAINT `posts_ibfk_1`
    FOREIGN KEY (`company_id`)
    REFERENCES `jobstreet`.`companies` (`id`),
  CONSTRAINT `posts_user_id_foreign`
    FOREIGN KEY (`user_id`)
    REFERENCES `jobstreet`.`users` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 53
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `jobstreet`.`comments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NULL DEFAULT NULL,
  `post_id` INT NOT NULL,
  `content` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `company_id` INT NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `comments_user_id_foreign` (`user_id` ASC) VISIBLE,
  INDEX `comments_post_id_foreign` (`post_id` ASC) VISIBLE,
  INDEX `company_id` (`company_id` ASC) VISIBLE,
  CONSTRAINT `comments_post_id_foreign`
    FOREIGN KEY (`post_id`)
    REFERENCES `jobstreet`.`posts` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `comments_user_id_foreign`
    FOREIGN KEY (`user_id`)
    REFERENCES `jobstreet`.`users` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `company_id`
    FOREIGN KEY (`company_id`)
    REFERENCES `jobstreet`.`companies` (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 38
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `jobstreet`.`contacts` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `content` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `contacts_user_id_foreign` (`user_id` ASC) VISIBLE,
  CONSTRAINT `contacts_user_id_foreign`
    FOREIGN KEY (`user_id`)
    REFERENCES `jobstreet`.`users` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `jobstreet`.`migrations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `batch` INT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 10
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `jobstreet`.`personal_access_tokens` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` VARCHAR(255) NOT NULL,
  `tokenable_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `token` VARCHAR(64) NOT NULL,
  `abilities` TEXT NULL DEFAULT NULL,
  `last_used_at` TIMESTAMP NULL DEFAULT NULL,
  `expires_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `personal_access_tokens_token_unique` (`token` ASC) VISIBLE,
  INDEX `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type` ASC, `tokenable_id` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
