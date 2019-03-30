-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema time_travel
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `time_travel` ;

-- -----------------------------------------------------
-- Schema time_travel
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `time_travel` DEFAULT CHARACTER SET utf8 ;
USE `time_travel` ;

-- -----------------------------------------------------
-- Table `time_travel`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `time_travel`.`users` ;

CREATE TABLE IF NOT EXISTS `time_travel`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `origin` ENUM('facebook', 'google') NOT NULL,
  `social_id` VARCHAR(45) NOT NULL,
  `email` VARCHAR(128) NOT NULL,
  `names` VARCHAR(255) NOT NULL,
  `avatar` VARCHAR(100) NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

CREATE UNIQUE INDEX `uq_user_cred` ON `time_travel`.`users` (`origin` ASC, `social_id` ASC, `email` ASC);


-- -----------------------------------------------------
-- Table `time_travel`.`users_tokens`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `time_travel`.`users_tokens` ;

CREATE TABLE IF NOT EXISTS `time_travel`.`users_tokens` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `token` LONGTEXT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`, `user_id`),
  CONSTRAINT `fk_users_tokens_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `time_travel`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE RESTRICT)
ENGINE = InnoDB;

CREATE INDEX `fk_users_tokens_users_idx` ON `time_travel`.`users_tokens` (`user_id` ASC);


-- -----------------------------------------------------
-- Table `time_travel`.`locations`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `time_travel`.`locations` ;

CREATE TABLE IF NOT EXISTS `time_travel`.`locations` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(128) NOT NULL,
  `latitude` DOUBLE NOT NULL,
  `longitude` DOUBLE NOT NULL,
  `description` TEXT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `time_travel`.`games`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `time_travel`.`games` ;

CREATE TABLE IF NOT EXISTS `time_travel`.`games` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `location_id` INT NOT NULL,
  `name` VARCHAR(128) NOT NULL,
  `status` ENUM('pending', 'running', 'finished') NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`, `location_id`),
  CONSTRAINT `fk_games_locations1`
    FOREIGN KEY (`location_id`)
    REFERENCES `time_travel`.`locations` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB;

CREATE INDEX `fk_games_locations1_idx` ON `time_travel`.`games` (`location_id` ASC);


-- -----------------------------------------------------
-- Table `time_travel`.`users_games`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `time_travel`.`users_games` ;

CREATE TABLE IF NOT EXISTS `time_travel`.`users_games` (
  `user_id` INT NOT NULL,
  `game_id` INT NOT NULL,
  PRIMARY KEY (`user_id`, `game_id`),
  CONSTRAINT `fk_users_teams_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `time_travel`.`users` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_users_teams_games1`
    FOREIGN KEY (`game_id`)
    REFERENCES `time_travel`.`games` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB;

CREATE INDEX `fk_users_teams_games1_idx` ON `time_travel`.`users_games` (`game_id` ASC);


-- -----------------------------------------------------
-- Table `time_travel`.`markers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `time_travel`.`markers` ;

CREATE TABLE IF NOT EXISTS `time_travel`.`markers` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `location_id` INT NOT NULL,
  `qr_code` VARCHAR(128) NOT NULL,
  `description` TEXT NOT NULL,
  `points` DOUBLE NOT NULL DEFAULT 10,
  `latitude` DOUBLE NOT NULL,
  `longitude` DOUBLE NOT NULL,
  PRIMARY KEY (`id`, `location_id`),
  CONSTRAINT `fk_markers_locations1`
    FOREIGN KEY (`location_id`)
    REFERENCES `time_travel`.`locations` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB;

CREATE INDEX `fk_markers_locations1_idx` ON `time_travel`.`markers` (`location_id` ASC);


-- -----------------------------------------------------
-- Table `time_travel`.`games_markers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `time_travel`.`games_markers` ;

CREATE TABLE IF NOT EXISTS `time_travel`.`games_markers` (
  `game_id` INT NOT NULL,
  `marker_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`game_id`, `marker_id`, `user_id`),
  CONSTRAINT `fk_games_markers_games1`
    FOREIGN KEY (`game_id`)
    REFERENCES `time_travel`.`games` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_games_markers_markers1`
    FOREIGN KEY (`marker_id`)
    REFERENCES `time_travel`.`markers` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_games_markers_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `time_travel`.`users` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB;

CREATE INDEX `fk_games_markers_markers1_idx` ON `time_travel`.`games_markers` (`marker_id` ASC);

CREATE INDEX `fk_games_markers_users1_idx` ON `time_travel`.`games_markers` (`user_id` ASC);


-- -----------------------------------------------------
-- Table `time_travel`.`chat_messages`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `time_travel`.`chat_messages` ;

CREATE TABLE IF NOT EXISTS `time_travel`.`chat_messages` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `game_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `text` TEXT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`, `game_id`, `user_id`),
  CONSTRAINT `fk_chat_messages_games1`
    FOREIGN KEY (`game_id`)
    REFERENCES `time_travel`.`games` (`id`)
    ON DELETE CASCADE
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_chat_messages_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `time_travel`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_chat_messages_games1_idx` ON `time_travel`.`chat_messages` (`game_id` ASC);

CREATE INDEX `fk_chat_messages_users1_idx` ON `time_travel`.`chat_messages` (`user_id` ASC);


-- -----------------------------------------------------
-- Table `time_travel`.`test_questions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `time_travel`.`test_questions` ;

CREATE TABLE IF NOT EXISTS `time_travel`.`test_questions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `marker_id` INT NOT NULL,
  `question` TEXT NOT NULL,
  PRIMARY KEY (`id`, `marker_id`),
  CONSTRAINT `fk_test_question_markers1`
    FOREIGN KEY (`marker_id`)
    REFERENCES `time_travel`.`markers` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_test_question_markers1_idx` ON `time_travel`.`test_questions` (`marker_id` ASC);


-- -----------------------------------------------------
-- Table `time_travel`.`test_answers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `time_travel`.`test_answers` ;

CREATE TABLE IF NOT EXISTS `time_travel`.`test_answers` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `question_id` INT NOT NULL,
  `answer` VARCHAR(400) NOT NULL,
  `is_correct` TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`, `question_id`),
  CONSTRAINT `fk_test_answers_test_question1`
    FOREIGN KEY (`question_id`)
    REFERENCES `time_travel`.`test_questions` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_test_answers_test_question1_idx` ON `time_travel`.`test_answers` (`question_id` ASC);


-- -----------------------------------------------------
-- Table `time_travel`.`users_answers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `time_travel`.`users_answers` ;

CREATE TABLE IF NOT EXISTS `time_travel`.`users_answers` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `game_id` INT NOT NULL,
  `answer_id` INT NOT NULL,
  PRIMARY KEY (`id`, `user_id`, `game_id`, `answer_id`),
  CONSTRAINT `fk_users_answers_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `time_travel`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_users_answers_games1`
    FOREIGN KEY (`game_id`)
    REFERENCES `time_travel`.`games` (`id`)
    ON DELETE CASCADE
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_users_answers_test_answers1`
    FOREIGN KEY (`answer_id`)
    REFERENCES `time_travel`.`test_answers` (`id`)
    ON DELETE CASCADE
    ON UPDATE RESTRICT)
ENGINE = InnoDB;

CREATE INDEX `fk_users_answers_users1_idx` ON `time_travel`.`users_answers` (`user_id` ASC);

CREATE INDEX `fk_users_answers_games1_idx` ON `time_travel`.`users_answers` (`game_id` ASC);

CREATE INDEX `fk_users_answers_test_answers1_idx` ON `time_travel`.`users_answers` (`answer_id` ASC);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
