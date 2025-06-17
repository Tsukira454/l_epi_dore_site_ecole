-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema mouyart_epi_dore
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema mouyart_epi_dore
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `mouyart_epi_dore` DEFAULT CHARACTER SET utf8mb4 ;
USE `mouyart_epi_dore` ;

-- -----------------------------------------------------
-- Table `mouyart_epi_dore`.`clients`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mouyart_epi_dore`.`clients` (
  `id_clients` INT(11) NOT NULL,
  `prenom` VARCHAR(45) NULL DEFAULT NULL,
  `nom` VARCHAR(45) NULL DEFAULT NULL,
  `email` VARCHAR(45) NULL DEFAULT NULL,
  `tel` VARCHAR(10) NULL DEFAULT NULL,
  `mdp` VARCHAR(255) NULL DEFAULT NULL,
  `token` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id_clients`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `mouyart_epi_dore`.`sandwichs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mouyart_epi_dore`.`sandwichs` (
  `id_sandwichs` INT(11) NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(45) NULL DEFAULT NULL,
  `description` VARCHAR(255) NULL DEFAULT NULL,
  `prix` DECIMAL(5,2) NULL DEFAULT NULL,
  `stock` INT(11) NULL DEFAULT 0,
  PRIMARY KEY (`id_sandwichs`))
ENGINE = InnoDB
AUTO_INCREMENT = 11
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `mouyart_epi_dore`.`ligne_commande`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mouyart_epi_dore`.`ligne_commande` (
  `id_ligne_commande` INT(11) NOT NULL,
  `fk_produit` INT(11) NOT NULL,
  `fk_client` INT(11) NOT NULL,
  `quantite` VARCHAR(45) NULL,
  PRIMARY KEY (`id_ligne_commande`),
  INDEX `fk_ligne_commande_articles1_idx` (`fk_produit` ASC) VISIBLE,
  INDEX `fk_ligne_commande_clients1_idx` (`fk_client` ASC) VISIBLE,
  CONSTRAINT `fk_ligne_commande_articles1`
    FOREIGN KEY (`fk_produit`)
    REFERENCES `mouyart_epi_dore`.`sandwichs` (`id_sandwichs`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ligne_commande_clients1`
    FOREIGN KEY (`fk_client`)
    REFERENCES `mouyart_epi_dore`.`clients` (`id_clients`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
