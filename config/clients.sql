CREATE DATABASE 

IF NOT EXISTS central_config CHARACTER SET utf8 COLLATE utf8_general_ci;

USE central_config;

CREATE TABLE clientes (
  id int NOT NULL AUTO_INCREMENT,
  usuario varchar(50) NOT NULL,
  empresa varchar(100) NOT NULL,
  db_host varchar(100) NOT NULL,
  db_nombre varchar(100) NOT NULL,
  db_user varchar(100) NOT NULL,
  db_pass varchar(100) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY usuario (usuario)
  ) ENGINE = InnoDB;