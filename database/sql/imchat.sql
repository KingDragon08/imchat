CREATE SCHEMA `imchat` DEFAULT CHARACTER SET utf8mb4 ;

CREATE TABLE `imchat`.`user` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nickname` VARCHAR(200) NULL,
  `username` VARCHAR(200) NOT NULL,
  `avatar` VARCHAR(200) NULL,
  `password` VARCHAR(200) NULL,
  `jifen` DOUBLE NOT NULL DEFAULT 0,
  `bonus` DOUBLE NOT NULL DEFAULT 0,
  `created_at` VARCHAR(20) NOT NULL,
  `updated_at` VARCHAR(20),
  PRIMARY KEY (`id`));

CREATE TABLE `imchat`.`groups` (
  `id` INT NOT NULL,
  `groupId` VARCHAR(200) NOT NULL,
  `avatar` VARCHAR(200) NOT NULL,
  `created_at` VARCHAR(45) NULL,
  `updated_at` VARCHAR(45) NULL,
  PRIMARY KEY (`id`));

CREATE TABLE `imchat`.`chatrooms` (
  `id` INT NOT NULL,
  `roomId` VARCHAR(200) NOT NULL,
  `avatar` VARCHAR(200) NOT NULL,
  `created_at` VARCHAR(45) NULL,
  `updated_at` VARCHAR(45) NULL,
  PRIMARY KEY (`id`));