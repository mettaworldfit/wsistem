CREATE DATABASE 
IF NOT EXISTS tenant_auth CHARACTER SET utf8 COLLATE utf8_general_ci;
USE tenant_auth;

CREATE TABLE empresas (
  id INT NOT NULL AUTO_INCREMENT,
  rnc VARCHAR(20) NULL UNIQUE,
  nombre VARCHAR(100) NOT NULL,
  db_host VARCHAR(100) NOT NULL DEFAULt "localhost",
  db_name VARCHAR(100) NOT NULL,
  db_user VARCHAR(100) NOT NULL,
  db_pass VARCHAR(100) NULL,
  
  PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE usuarios (
  user_id INT NOT NULL AUTO_INCREMENT,
  empresa_id INT NOT NULL,
  username VARCHAR(50) NOT NULL,
  
  PRIMARY KEY (user_id),
  CONSTRAINT usuarios_empresa FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


INSERT INTO empresas VALUES 
(null,131870198,'Codev','localhost','proyecto','root',''),
(null,'','Powerfit','localhost','powerfit','root','');

insert into usuarios values
(null,1,'local'),
(null,2,'wilmin');

select * from usuarios;
