-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mouyart_epi_dore
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema mouyart_epi_dore
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `mouyart_epi_dore` DEFAULT CHARACTER SET utf8 ;
USE `mouyart_epi_dore` ;

-- -----------------------------------------------------
-- Table `mouyart_epi_dore`.`clients`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mouyart_epi_dore`.`clients` (
  `id_clients` INT NOT NULL,
  `prenom` VARCHAR(45) NULL,
  `nom` VARCHAR(45) NULL,
  `email` VARCHAR(45) NULL,
  `tel` VARCHAR(10) NULL,
  PRIMARY KEY (`id_clients`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mouyart_epi_dore`.`produits`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mouyart_epi_dore`.`produits` (
  `id_produits` INT NOT NULL,
  `nom` VARCHAR(45) NULL,
  `description` VARCHAR(255) NULL,
  `quantite` SMALLINT NULL,
  `prix` DECIMAL(5,2) NULL,
  PRIMARY KEY (`id_produits`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mouyart_epi_dore`.`commandes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mouyart_epi_dore`.`commandes` (
  `id_commandes` INT NOT NULL,
  `date_commande` DATE NULL,
  `fk_client` INT NULL,
  PRIMARY KEY (`id_commandes`),
  INDEX `fk_commandes_clients1_idx` (`fk_client` ASC) VISIBLE,
  CONSTRAINT `fk_commandes_clients1`
    FOREIGN KEY (`fk_client`)
    REFERENCES `mouyart_epi_dore`.`clients` (`id_clients`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mouyart_epi_dore`.`ligne_commande`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mouyart_epi_dore`.`ligne_commande` (
  `id_ligne_commande` INT NOT NULL,
  `fk_commande` INT NULL,
  `fk_produit` INT NULL,
  `quantite` VARCHAR(45) NULL,
  PRIMARY KEY (`id_ligne_commande`),
  INDEX `fk_ligne_commande_commandes1_idx` (`fk_commande` ASC) VISIBLE,
  INDEX `fk_ligne_commande_articles1_idx` (`fk_produit` ASC) VISIBLE,
  CONSTRAINT `fk_ligne_commande_commandes1`
    FOREIGN KEY (`fk_commande`)
    REFERENCES `mouyart_epi_dore`.`commandes` (`id_commandes`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ligne_commande_articles1`
    FOREIGN KEY (`fk_produit`)
    REFERENCES `mouyart_epi_dore`.`produits` (`id_produits`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
