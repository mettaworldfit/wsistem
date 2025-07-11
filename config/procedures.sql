# Base de datos
#-------------------------------------------------------------------



# Datos por principales
#-------------------------------------------------------------------

insert into roles values (null,'administrador');
insert into roles values (null,'cajero');
insert into roles values (null,'tecnico');


insert into estados_generales values (null,'Activo');
insert into estados_generales values (null,'Inactivo');
insert into estados_generales values (null,'Pagada');
insert into estados_generales values (null,'Por Cobrar');
insert into estados_generales values (null,'Anulada');

insert into estados_generales values (null,'Pendiente');
insert into estados_generales values (null,'Entregado');
insert into estados_generales values (null,'En Proceso');
insert into estados_generales values (null,'Listo');
insert into estados_generales values (null,'No se pudo');
insert into estados_generales values (null,'Ordenado');
insert into estados_generales values (null,'Facturado');

insert into estados_generales values (null,'disponible');
insert into estados_generales values (null,'vendido');


insert into metodos_de_pagos values (null,'Efectivo');
insert into metodos_de_pagos values (null,'Tarjeta de credito');
insert into metodos_de_pagos values (null,'Tarjeta de debito');
insert into metodos_de_pagos values (null,'Transferencia');
insert into metodos_de_pagos values (null,'cheque');

insert into usuarios values (null,1,1,'Wilmin Jose','Sanchez','local','1234',curdate());

insert into almacenes values (null,1,'Principal','',curdate());
insert into almacenes values (null,1,'Los Hidalgos','',curdate());

insert into lista_de_precios values (null,1,'Minimo 1','',curdate());
insert into lista_de_precios values (null,1,'Minimo 2','',curdate());
insert into lista_de_precios values (null,1,'Por mayor','',curdate());

insert into impuestos values (null,1,'Itbis',18,'',curdate());

insert into clientes values (null,1,null,'Consumidor Final',null,null,null,null,null,curdate());
                            
insert into proveedores values (null,1,'a&b technologic',null,null,null,null,null,curdate());
insert into proveedores values (null,1,'jam comunicaciones',null,null,null,null,null,curdate());
insert into proveedores values (null,1,'señal celulares',null,null,null,null,null,curdate());
insert into proveedores values (null,1,'mb comunicaciones',null,null,null,null,null,curdate());
insert into proveedores values (null,1,'7seven cell',null,null,null,null,null,curdate());

insert into marcas values (null,'Samsung',curdate());
insert into marcas values (null,'Lg',curdate());
insert into marcas values (null,'Motorola',curdate());
insert into marcas values (null,'Alcatel',curdate());
insert into marcas values (null,'Apple',curdate());
insert into marcas values (null,'Altise',curdate());
insert into marcas values (null,'Xiaomi',curdate());
insert into marcas values (null,'Zte',curdate());
insert into marcas values (null,'Nokia',curdate());
insert into marcas values (null,'Vidvie',curdate());
insert into marcas values (null,'Coolpad',curdate());
insert into marcas values (null,'Claro',curdate());
insert into marcas values (null,'Ipro',curdate());
insert into marcas values (null,'Lenovo',curdate());
insert into marcas values (null,'Kyocera',curdate());
insert into marcas values (null,'OtuxOne',curdate());
insert into marcas values (null,'RIO',curdate());
insert into marcas values (null,'amazon',curdate());
insert into marcas values (null,'asus',curdate());
insert into marcas values (null,'vankyo',curdate());
insert into marcas values (null,'ktec',curdate());
insert into marcas values (null,'sandisk',curdate());
insert into marcas values (null,'kingston',curdate());
insert into marcas values (null,'oneplus',curdate());

insert into equipos values (null,5,'Iphone 7 plus',null);

insert into posiciones values (null,1,'A1','',curdate());
insert into posiciones values (null,1,'A2','',curdate());
insert into posiciones values (null,1,'A3','',curdate());
insert into posiciones values (null,1,'B1','',curdate());
insert into posiciones values (null,1,'B2','',curdate());
insert into posiciones values (null,1,'B3','',curdate());
insert into posiciones values (null,1,'C1','',curdate());
insert into posiciones values (null,1,'C2','',curdate());
insert into posiciones values (null,1,'C3','',curdate());

insert into categorias values (null,1,'Celular','',curdate());
insert into categorias values (null,1,'Protector','',curdate());
insert into categorias values (null,1,'Bateria','',curdate());
insert into categorias values (null,1,'Pantalla','',curdate());
insert into categorias values (null,1,'tablet','',curdate());
insert into categorias values (null,1,'reloj','',curdate());

insert into condiciones values (null,1,'Red bloqueada',curdate()),
                               (null,1,'Pantalla rota',curdate()),
							   (null,1,'Pantalla en blanco',curdate()),
                               (null,1,'Pantalla azul',curdate()),
                               (null,1,'Pantalla mojada',curdate()),
							   (null,1,'No enciende',curdate()),
                               (null,1,'No enciende blueetoth',curdate()),
							   (null,1,'No enciende WIFI',curdate()),
                               (null,1,'No coge señal',curdate()),
							   (null,1,'Housing dañado',curdate()),
                               (null,1,'Maquina doblada',curdate()),
                               (null,1,'Maquina dañada',curdate()),
                               (null,1,'Pin de carga dañado',curdate()),
                               (null,1,'Conector de auricular dañado',curdate()),
                               (null,1,'botón de inicio dañado',curdate()),
                               (null,1,'botón de encender dañado',curdate()),
                               (null,1,'botón de subir volumen dañado',curdate()),
                               (null,1,'botón de bajar volumen dañado',curdate()),
                               (null,1,'botones de volumen dañados',curdate()),
                               (null,1,'Problema pin de carga',curdate()),
                               (null,1,'Problema de software',curdate()),
                               (null,1,'Problema de camara frontal',curdate()),
                               (null,1,'Problema de camara delantera',curdate()),
                               (null,1,'Problema de pantalla',curdate());
                               
                               
                               
-- insert into servicios values (null,1,'Chequeo',curdate()),
						  --   (null,1,'Mantenimiento',curdate()),
                           --  (null,1,'Desbloqueo',curdate()),
						  --   (null,1,'Jompeo de bateria',curdate());
						
insert into motivos values (null,1,'Gastos por impuestos',curdate());
insert into motivos values (null,1,'Otros gastos',curdate());
insert into motivos values (null,1,'Ajustes al inventario',curdate());
insert into motivos values (null,1,'Costo de los servicios vendidos',curdate());
insert into motivos values (null,1,'Gastos de administración',curdate());
insert into motivos values (null,1,'Gastos generales',curdate());
insert into motivos values (null,1,'Comisiones, honorarios y servicios',curdate());
insert into motivos values (null,1,'Servicios públicos',curdate());
insert into motivos values (null,1,'Papelería',curdate());
insert into motivos values (null,1,'Servicios de aseo, cafetería, restaurante y lavandería',curdate());
insert into motivos values (null,1,'Publicidad',curdate());
insert into motivos values (null,1,'Vigilancia',curdate());
insert into motivos values (null,1,'Seguros generales',curdate());
insert into motivos values (null,1,'Combustibles y lubricantes',curdate());
insert into motivos values (null,1,'Otros gastos administrativos',curdate());
insert into motivos values (null,1,'Gastos del local',curdate());
insert into motivos values (null,1,'Renta o alquiler',curdate());
insert into motivos values (null,1,'Sueldos y salarios',curdate());
insert into motivos values (null,1,'Sueldos',curdate());
insert into motivos values (null,1,'Devoluciones en ventas',curdate());
insert into motivos values (null,1,'Cuentas por pagar',curdate());
insert into motivos values (null,1,'Cuentas por pagar a proveedores',curdate());
insert into motivos values (null,1,'Anticipo recibido de clientes',curdate());
insert into motivos values (null,1,'Otras cuentas por pagar',curdate());
insert into motivos values (null,1,'Devoluciones de clientes',curdate());
insert into motivos values (null,1,'Prestaciones laborales',curdate());
insert into motivos values (null,1,'Préstamos por pagar',curdate());
insert into motivos values (null,1,'Tarjetas de crédito',curdate());
insert into motivos values (null,1,'Tarjeta de crédito empresarial',curdate());
insert into motivos values (null,1,'Impuestos por pagar',curdate());
insert into motivos values (null,1,'Préstamos a terceros',curdate());
insert into motivos values (null,1,'Inversiones',curdate());
insert into motivos values (null,1,'Compra de bateria',curdate());
insert into motivos values (null,1,'Compra de pantalla',curdate());
insert into motivos values (null,1,'Compra de pieza',curdate());
insert into motivos values (null,1,'Compra de flexible',curdate());
insert into motivos values (null,1,'Compra de celulares o equipos',curdate());

insert into colores values (null,"negro con azul"),
(null,"negro con rojo");
insert into colores values (null,"negro");
insert into colores values (null,"rojo vino");
insert into colores values (null,"marron");
insert into colores values (null,"gradiente");
insert into colores values (null,"verde plateado");
insert into colores values (null,"verde");
insert into colores values (null,"morado");
insert into colores values (null,"morado claro");
insert into colores values (null,"rosado pastel");
insert into colores values (null,"blanco"),
(null,"azul"),
(null,"rojo"),
(null,"gris"),
(null,"plateado"),
(null,"rosa"),
(null,"oro"),
(null,"oro rosa");

insert into bono_config values (null,1,2,'15000',200,curdate());

insert into configuraciones (config_id) VALUES (null);
						


# Store Procedures
#-------------------------------------------------------------------

-- SELECT CONCAT('DROP PROCEDURE IF EXISTS `', ROUTINE_NAME, '`;') AS drop_statement
-- FROM INFORMATION_SCHEMA.ROUTINES
-- WHERE ROUTINE_TYPE='PROCEDURE' AND ROUTINE_SCHEMA = 'proyecto';

# -------------- Usuarios -----------------

DELIMITER $$
CREATE PROCEDURE `us_mostrarUsuarios` ()
BEGIN
select *from usuarios;
END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `us_mostrarUsuarios_y_mas` ()
BEGIN

SELECT * FROM usuarios u
INNER JOIN estados_generales s ON s.estado_id = u.estado_id
INNER JOIN roles r ON r.rol_id = u.rol_id;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `us_verificarUsuario` (in username varchar(50))
BEGIN

SELECT * FROM usuarios u 
INNER JOIN roles r ON r.rol_id = u.rol_id
INNER JOIN estados_generales s ON s.estado_id = u.estado_id 
WHERE u.username = username AND s.nombre_estado = 'Activo';

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `us_actualizarUsuario` (in rol_id int, in nombre varchar(100),
in apellidos varchar(100), in username varchar(50), in password varchar(50), in usuario_id int)
BEGIN

 DECLARE EXIT HANDLER FOR 1062 SELECT 'Duplicate keys error encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
    
    UPDATE usuarios u SET u.rol_id = rol_id , u.nombre = nombre, 
	u.apellidos = apellidos, u.username = username, u.password = password
	WHERE u.usuario_id = usuario_id;
    SELECT 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `us_agregarUsuario` (in rol_id int, in nombre varchar(100),
in apellidos varchar(100), in username varchar(50), in password varchar(50))
BEGIN

 DECLARE EXIT HANDLER FOR 1062 SELECT 'Duplicate keys error encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 
    INSERT INTO usuarios VALUES (null,rol_id,1,nombre,apellidos,username,password,curdate());
    SELECT 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `us_eliminarUsuario` (in id int)
BEGIN

DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

DELETE FROM usuarios WHERE usuario_id = id;
select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `us_cambiarEstado` (in id int, in accion varchar(50))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

BEGIN
 declare estado_id int;
END;

IF (accion = "desactivar") THEN 

SET @estado_id = (SELECT estado_id FROM estados_generales WHERE nombre_estado = 'Inactivo');
UPDATE usuarios SET estado_id = @estado_id WHERE usuario_id = id;
select 'ready' AS msg;

ELSEIF (accion = 'activar') THEN

SET @estado_id = (SELECT estado_id FROM estados_generales WHERE nombre_estado = 'Activo');
UPDATE usuarios SET estado_id = @estado_id WHERE usuario_id = id;
select 'ready' AS msg;

END IF ;

END $$
DELIMITER ;

# -------------- Impuestos -----------------

DELIMITER $$
CREATE PROCEDURE `im_agregarImpuesto` (in usuario_id int, in nombre varchar(50), 
in valor int, in descripcion varchar(150))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

INSERT INTO impuestos VALUES (null,usuario_id,nombre,valor,descripcion,curdate());
select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `im_actualizarImpuesto` (in id int, in nombre varchar(50), 
in valor int, in descripcion varchar(150))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

UPDATE impuestos SET nombre_impuesto = nombre, valor = valor, descripcion = descripcion WHERE impuesto_id = id;
select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `im_eliminarImpuesto` (in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

DELETE FROM impuestos WHERE impuesto_id = id;

END $$
DELIMITER ;

# -------------- Posiciones -----------------

DELIMITER $$
CREATE PROCEDURE `pos_agregarPosicion` (in usuario_id int, in referencia varchar(50), 
in descripcion varchar(150))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

INSERT INTO posiciones VALUES (null,usuario_id,referencia,descripcion,curdate());
select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `pos_actualizarPosicion` (in id int, in referencia varchar(50), 
in descripcion varchar(150))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

UPDATE posiciones SET referencia = referencia, descripcion = descripcion WHERE posicion_id = id;
select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `pos_eliminarPosicion` (in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

DELETE FROM posiciones WHERE posicion_id = id;

END $$
DELIMITER ;

# -------------- Categorías -----------------

DELIMITER $$
CREATE PROCEDURE `ca_agregarCategoria` (in usuario_id int, in nombre varchar(50), 
in descripcion varchar(150))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

INSERT INTO categorias VALUES (null,usuario_id,nombre,descripcion,curdate());
select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `ca_eliminarCategoria` (in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

DELETE FROM categorias WHERE categoria_id = id;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `ca_actualizarCategoria` (in id int, in nombre varchar(50), 
in descripcion varchar(150))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

UPDATE categorias SET nombre_categoria = nombre, descripcion = descripcion WHERE categoria_id = id;
select 'ready' AS msg;

END $$
DELIMITER ;

# -------------- Almacenes -----------------

DELIMITER $$
CREATE PROCEDURE `al_agregarAlmacen` (in usuario_id int, in nombre varchar(50), 
in descripcion varchar(150))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

INSERT INTO almacenes VALUES (null,usuario_id,nombre,descripcion,curdate());
select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `al_actualizarAlmacen` (in id int, in nombre varchar(50), 
in descripcion varchar(150))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

UPDATE almacenes SET nombre_almacen = nombre, descripcion = descripcion WHERE almacen_id = id;
select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `al_eliminarAlmacen` (in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

DELETE FROM almacenes WHERE almacen_id = id;

END $$
DELIMITER ;

# -------------- Ofertas -----------------

DELIMITER $$
CREATE PROCEDURE `of_agregarOferta` (in usuario_id int, in nombre varchar(50), 
in valor int, in descripcion varchar(150))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

INSERT INTO ofertas VALUES (null,usuario_id,nombre,valor,descripcion,curdate());
select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `of_actualizarOferta` (in id int, in nombre varchar(50), 
in valor int, in descripcion varchar(150))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

UPDATE ofertas SET nombre_oferta = nombre, valor = valor, descripcion = descripcion WHERE oferta_id = id;
select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `of_eliminarOferta` (in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

DELETE FROM ofertas WHERE oferta_id = id;

END $$
DELIMITER ;

# -------------- Clientes -----------------

DELIMITER $$
CREATE PROCEDURE `cl_agregarCliente` (in usuario_id int, in nombre varchar(50), in apellidos varchar(100),
in codigo varchar(11), in tel1 varchar(15), in tel2 varchar(15),in direccion varchar(150), in email varchar(50))
BEGIN

 DECLARE EXIT HANDLER FOR 1062 SELECT 'Duplicate keys error encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

   IF (codigo != '') THEN
   
   INSERT INTO clientes VALUES (null,usuario_id,codigo,nombre,apellidos,tel1,tel2,email,direccion,curdate());
   select 'ready' AS msg;
   
   ELSE 
   
   INSERT INTO clientes VALUES (null,usuario_id,null,nombre,apellidos,tel1,tel2,email,direccion,curdate());
   select 'ready' AS msg;
   
   END IF ;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `cl_actualizarCliente` (in id int, in nombre varchar(50), in apellidos varchar(100), in codigo varchar(11),
in tel1 varchar(15), in tel2 varchar(15), in email varchar(50), in direccion varchar(150))
BEGIN

 DECLARE EXIT HANDLER FOR 1062 SELECT 'Duplicate keys error encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 
 IF (codigo != '') THEN
 
 UPDATE clientes SET nombre = nombre, apellidos = apellidos, cedula = codigo,
		   telefono1 = tel1, telefono2 = tel2, email = email, direccion = direccion
           WHERE cliente_id = id;
SELECT 'ready' AS msg;

ELSE

UPDATE clientes SET nombre = nombre, apellidos = apellidos, cedula = null,
		   telefono1 = tel1, telefono2 = tel2, email = email, direccion = direccion
           WHERE cliente_id = id;
SELECT 'ready' AS msg;
    
 END IF ;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `cl_eliminarCliente` (in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

DELETE FROM clientes WHERE cliente_id = id;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `cl_eliminarBono` (in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

DELETE FROM bonos WHERE bono_id = id;

END $$
DELIMITER ;

# -------------- Proveedores -----------------

DELIMITER $$
CREATE PROCEDURE `pv_agregarProveedor` (in usuario_id int, in nombre varchar(50), in apellidos varchar(100),
in tel1 varchar(15), in tel2 varchar(15),in direccion varchar(150), in email varchar(50))
BEGIN

 DECLARE EXIT HANDLER FOR 1062 SELECT 'Duplicate keys error encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

INSERT INTO proveedores VALUES (null,usuario_id,nombre,apellidos,tel1,tel2,email,direccion,curdate());
select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `pv_actualizarProveedor` (in id int, in nombre varchar(50), in apellidos varchar(100),
in tel1 varchar(15), in tel2 varchar(15),in email varchar(50), in direccion varchar(150))
BEGIN

 DECLARE EXIT HANDLER FOR 1062 SELECT 'Duplicate keys error encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 UPDATE proveedores SET nombre_proveedor = nombre, apellidos = apellidos, telefono1 = tel1, 
        telefono2 = tel2, email = email, direccion = direccion
		WHERE proveedor_id = id;
SELECT 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `pv_eliminarProveedor` (in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

DELETE FROM proveedores WHERE proveedor_id = id;

END $$
DELIMITER ;


# -------------- Productos -----------------


DELIMITER $$
CREATE PROCEDURE `pr_agregarProducto` (in usuario_id int, in almacen_id int, in codigo varchar(100),
in nombre varchar(100), in costo decimal(19,2), in precio int, in cantidad int, in cantidad_min int, 
in categoria_id int, in posicion_id int, in impuesto_id int, in oferta_id int, in marca_id int,
in proveedor_id int, in imagen varchar(255))
BEGIN

 DECLARE EXIT HANDLER FOR 1062 SELECT 'Duplicate keys error encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 
 BEGIN
 DECLARE last_id int;
 END ;

 IF (codigo != '') THEN
   INSERT INTO productos VALUES (null,usuario_id,1,almacen_id,codigo,nombre,costo,precio,cantidad,cantidad_min,'',curdate());
   select last_insert_id() AS msg;
ELSE 
   INSERT INTO productos VALUES (null,usuario_id,1,almacen_id,null,nombre,costo,precio,cantidad,cantidad_min,'',curdate());
   select last_insert_id() AS msg;
END IF ;

SET @last_id = last_insert_id();

	IF(categoria_id > 0) THEN
	INSERT INTO productos_con_categorias VALUES (@last_id,categoria_id);
	END IF ;
	IF (posicion_id > 0) THEN
	INSERT INTO productos_con_posiciones VALUES (@last_id,posicion_id);
	END IF ;
	IF (impuesto_id > 0) THEN
	INSERT INTO productos_con_impuestos VALUES (@last_id,impuesto_id);
	END IF ;
	IF (oferta_id > 0) THEN
	INSERT INTO productos_con_ofertas VALUES (@last_id,oferta_id);
	END IF ;
	IF (marca_id > 0) THEN
	INSERT INTO productos_con_marcas VALUES (@last_id,marca_id);
	END IF ;
	IF (proveedor_id > 0) THEN
	INSERT INTO productos_con_proveedores VALUES (@last_id,proveedor_id);
	END IF ;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `pr_cambiarEstado` (in id int, in accion varchar(50))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

BEGIN
 declare estado_id int;
END;

IF (accion = "desactivar") THEN 

SET @estado_id = (SELECT estado_id FROM estados_generales WHERE nombre_estado = 'Inactivo');
UPDATE productos SET estado_id = @estado_id WHERE producto_id = id;

ELSEIF (accion = 'activar') THEN

SET @estado_id = (SELECT estado_id FROM estados_generales WHERE nombre_estado = 'Activo');
UPDATE productos SET estado_id = @estado_id WHERE producto_id = id;

END IF ;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `pr_editarProducto` (in productoId int, in almacen_id int, in codigo varchar(100),
in nombre varchar(100), in costo decimal(19,2), in precio int, in cantidad int, in cantidad_min int, 
in categoria_id int, in posicion_id int, in impuesto_id int, in ofertaId int, in marca_id int,
in proveedor_id int, in imagen varchar(255))
BEGIN

 DECLARE EXIT HANDLER FOR 1062 SELECT 'Duplicate keys error encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;


 IF (codigo != '') THEN
 
   update productos set nombre_producto = nombre, cod_producto = codigo, almacen_id = almacen_id, precio_unitario = precio,
   precio_costo = costo, cantidad = cantidad, cantidad_min = cantidad_min, imagen = imagen where producto_id = productoId;
   
   select "ready" AS msg;
ELSEIF(codigo = '') THEN
 
   update productos set nombre_producto = nombre, cod_producto = null, almacen_id = almacen_id, precio_unitario = precio,
   precio_costo = costo, cantidad = cantidad, cantidad_min = cantidad_min, imagen = imagen where producto_id = productoId;
   
   select "ready" AS msg;
ELSE 
   update productos set nombre_producto = nombre, almacen_id = almacen_id, precio_unitario = precio,
   precio_costo = costo, cantidad = cantidad, cantidad_min = cantidad_min, imagen = imagen where producto_id = productoId;
   
   select "ready" AS msg;
END IF ;

	  IF(exists(select * from productos_con_categorias where producto_id = productoId) AND categoria_id != 0) THEN
       update productos_con_categorias set categoria_id = categoria_id 
	   where producto_id = productoId;
      ELSEIF (categoria_id != 0) THEN
       insert into productos_con_categorias values (productoId,categoria_id);
	  ELSE 
       delete from productos_con_categorias where producto_id = productoId;
 	END IF ;
    
    IF (exists(select * from productos_con_posiciones where producto_id = productoId) AND posicion_id != 0) THEN
	   update productos_con_posiciones set posicion_id = posicion_id 
	   where producto_id = productoId;
	ELSEIF (posicion_id != 0) THEN
        insert into productos_con_posiciones values (productoId,posicion_id);
	ELSE 
       delete from productos_con_posiciones where producto_id = productoId;
	END IF ;

    IF (exists(select * from productos_con_impuestos where producto_id = productoId) AND impuesto_id != 0) THEN
		 update productos_con_impuestos set impuesto_id = impuesto_id 
		 where producto_id = productoId;
	ELSEIF (impuesto_id != 0) THEN
        insert into productos_con_impuestos values (productoId,impuesto_id);
	ELSE 
       delete from productos_con_impuestos where producto_id = productoId;
	END IF ;
    
    IF (exists(select * from productos_con_ofertas where producto_id = productoId) AND ofertaId != 0) THEN
	   update productos_con_ofertas set oferta_id = ofertaId 
	   where producto_id = productoId;
	ELSEIF (ofertaId != 0) THEN
        insert into productos_con_ofertas values (productoId,ofertaId);
	ELSE 
       delete from productos_con_ofertas where producto_id = productoId;
	END IF ;

   IF (exists(select * from productos_con_marcas where producto_id = productoId) AND marca_id != 0) THEN
		update productos_con_marcas set marca_id = marca_id 
		where producto_id = productoId;
	ELSEIF (marca_id != 0) THEN
        insert into productos_con_marcas values (productoId,marca_id);
	ELSE 
        delete from productos_con_marcas where producto_id = productoId ;
	END IF ;

    IF (exists(select * from productos_con_proveedores where producto_id = productoId) AND proveedor_id != 0) THEN
		 update productos_con_proveedores set proveedor_id = proveedor_id 
		 where producto_id = productoId;
    ELSEIF (proveedor_id != 0) THEN
        insert into productos_con_proveedores values (productoId,proveedor_id);
	ELSE 
        delete from productos_con_proveedores where producto_id = productoId;
	END IF ;


END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `pr_asignarVariante` (in id int, in color_id int, in proveedor_id int, in tipo varchar(30) , in sabor varchar(45),
in serial varchar(20),in costo_unitario decimal(19,2),in caja varchar(2))
BEGIN

 DECLARE EXIT HANDLER FOR 1062 SELECT 'Duplicate keys error encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 IF (serial != '') THEN
      INSERT INTO variantes VALUES (null,id,13,tipo,null,serial,costo_unitario,caja,curdate());
      select last_insert_id() AS msg;
ELSE 
       INSERT INTO variantes VALUES (null,id,13,tipo,sabor,null,costo_unitario,caja,curdate());
       select last_insert_id() AS msg;
END IF ;
    
    SET @last_id = last_insert_id();

	IF(proveedor_id > 0) THEN
	INSERT INTO variantes_con_proveedores VALUES (@last_id,proveedor_id);
	END IF ;
    IF(color_id > 0) THEN
	INSERT INTO variantes_con_colores VALUES (@last_id,color_id);
	END IF ;


END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `pr_eliminarVariante` (in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 delete from variantes where variante_id = id;
 select 'ready' AS msg;
 
END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `pr_eliminarProducto` (in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 delete from productos where producto_id = id;
 select 'ready' AS msg;
 
END $$
DELIMITER ;

# -------------- Lista de precios -----------------

DELIMITER $$
CREATE PROCEDURE `lp_crearListaDePrecio` (in usuario_id int, in nombre varchar(50), in observacion varchar(150))
BEGIN

 DECLARE EXIT HANDLER FOR 1062 SELECT 'Duplicate keys error encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 insert into lista_de_precios values (null,usuario_id,nombre,observacion,curdate());
 select 'ready' AS msg;
 
END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `lp_eliminarLista` (in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 delete from lista_de_precios where lista_id = id;
 select 'ready' AS msg;
 
END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `lp_actualizarListaDePrecio` (in id int, in nombre varchar(50), in observacion varchar(150))
BEGIN

 DECLARE EXIT HANDLER FOR 1062 SELECT 'Duplicate keys error encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 update lista_de_precios set nombre_lista = nombre, descripcion = observacion where lista_id = id;
 select 'ready' AS msg;
 
END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `lp_asignarListaDePrecio` (in id int, in lista_id int, in valor int, in tipo varchar(50))
BEGIN

 DECLARE EXIT HANDLER FOR 1062 SELECT 'Duplicate keys error encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

IF (tipo = 'producto') THEN
    INSERT INTO productos_con_lista_de_precios VALUES (null,id,lista_id,valor,curdate());
    select 'ready' AS msg;
ELSEIF (tipo = 'pieza') THEN
    INSERT INTO piezas_con_lista_de_precios VALUES (null,id,lista_id,valor,curdate());
    select 'ready' AS msg;
END IF;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `lp_desasignarListaDePrecio` (in id int, in tipo varchar(50))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

IF (tipo = 'producto') THEN
	DELETE FROM productos_con_lista_de_precios WHERE producto_lista_id = id ;
	select 'ready' AS msg;
ELSEIF (tipo = 'pieza') THEN
	DELETE FROM piezas_con_lista_de_precios WHERE pieza_lista_id = id;
	select 'ready' AS msg;
END IF;


END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `lp_editarListaDePrecioAproducto` (in productoID int, in listaID int, in valor int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

BEGIN
DECLARE verify int;
END;

set @verify = (select count(producto_lista_id) from productos_con_lista_de_precios 
where producto_id = productoID and lista_id = listaID);

IF (@verify = 0) THEN 
	insert into productos_con_lista_de_precios values (null,productoID,listaID,valor,curdate());
	select last_insert_id() AS msg;
ELSE 
 select 'duplicate' AS msg;
END IF ;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `lp_editarListaDePrecioApieza` (in piezaID int, in listaID int, in valor int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

BEGIN
DECLARE verify int;
END;

set @verify = (select count(pieza_lista_id) from piezas_con_lista_de_precios 
where pieza_id = piezaID and lista_id = listaID);

IF (@verify = 0) THEN 
	insert into piezas_con_lista_de_precios values (null,piezaID,listaID,valor,curdate());
	select last_insert_id() AS msg;
ELSE 
 select 'duplicate' AS msg;
END IF ;

END $$
DELIMITER ;

# -------------- Piezas -----------------


DELIMITER $$
CREATE PROCEDURE `pz_agregarPieza` (in usuario_id int, in almacen_id int, in codigo varchar(100),
in nombre varchar(100), in costo decimal(19,2), in precio int, in cantidad int, in cantidad_min int, 
in categoria_id int, in posicion_id int, in oferta_id int, in marca_id int,
in proveedor_id int, in imagen varchar(255))
BEGIN

 DECLARE EXIT HANDLER FOR 1062 SELECT 'Duplicate keys error encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 
 BEGIN
 DECLARE last_id int;
 END ;

 IF (codigo != '') THEN
   INSERT INTO piezas VALUES (null,usuario_id,1,almacen_id,codigo,nombre,costo,precio,cantidad,cantidad_min,imagen,curdate());
   select last_insert_id() AS msg;
ELSE 
   INSERT INTO piezas VALUES (null,usuario_id,1,almacen_id,null,nombre,costo,precio,cantidad,cantidad_min,imagen,curdate());
   select last_insert_id() AS msg;
END IF ;

SET @last_id = last_insert_id();

	IF(categoria_id > 0) THEN
	INSERT INTO piezas_con_categorias VALUES (@last_id,categoria_id);
	END IF ;
	IF (posicion_id > 0) THEN
	INSERT INTO piezas_con_posiciones VALUES (@last_id,posicion_id);
	END IF ;
	IF (oferta_id > 0) THEN
	INSERT INTO piezas_con_ofertas VALUES (@last_id,oferta_id);
	END IF ;
	IF (marca_id > 0) THEN
	INSERT INTO piezas_con_marcas VALUES (@last_id,marca_id);
	END IF ;
	IF (proveedor_id > 0) THEN
	INSERT INTO piezas_con_proveedores VALUES (@last_id,proveedor_id);
	END IF ;


END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `pz_editarPieza` (in id int, in almacen_id int, in codigo varchar(100),
in nombre varchar(100), in costo int, in precio int, in cantidad int, in cantidad_min int, 
in categoria_id int, in posicion_id int, in ofertaId int, in marca_id int,
in proveedor_id int, in imagen varchar(255))
BEGIN

 DECLARE EXIT HANDLER FOR 1062 SELECT 'Duplicate keys error encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;


 IF (codigo != '') THEN
 
   update piezas set nombre_pieza = nombre, cod_pieza = codigo, almacen_id = almacen_id, precio_unitario = precio,
   precio_costo = costo, cantidad = cantidad, cantidad_min = cantidad_min where pieza_id = id;
   
   select "1" AS msg;
ELSEIF (codigo = '') THEN
	update piezas set nombre_pieza = nombre, cod_pieza = null, almacen_id = almacen_id, precio_unitario = precio,
   precio_costo = costo, cantidad = cantidad, cantidad_min = cantidad_min where pieza_id = id;
   
   select "1" AS msg;
ELSE 
   update piezas set nombre_pieza = nombre, almacen_id = almacen_id, precio_unitario = precio,
   precio_costo = costo, cantidad = cantidad, cantidad_min = cantidad_min where pieza_id = id;
   
   select "1" AS msg;
END IF ;

	  IF(exists(select * from piezas_con_categorias where pieza_id = id) AND categoria_id != 0) THEN
       update piezas_con_categorias set categoria_id = categoria_id 
	   where pieza_id = id;
      ELSEIF (categoria_id != 0) THEN
       insert into piezas_con_categorias values (id,categoria_id);
	  ELSE 
       delete from piezas_con_categorias where pieza_id = id;
 	END IF ;
    
    IF (exists(select * from piezas_con_posiciones where pieza_id = id) AND posicion_id != 0) THEN
	   update piezas_con_posiciones set posicion_id = posicion_id 
	   where pieza_id = id;
	ELSEIF (posicion_id != 0) THEN
        insert into piezas_con_posiciones values (id,posicion_id);
	ELSE 
       delete from piezas_con_posiciones where pieza_id = id;
	END IF ;
    
    IF (exists(select * from piezas_con_ofertas where pieza_id = id) AND ofertaId != 0) THEN
	   update piezas_con_ofertas set oferta_id = ofertaId 
	   where pieza_id = id;
	ELSEIF (ofertaId != 0) THEN
        insert into piezas_con_ofertas values (id,ofertaId);
	ELSE 
       delete from piezas_con_ofertas where pieza_id = id;
	END IF ;

   IF (exists(select * from piezas_con_marcas where pieza_id = id) AND marca_id != 0) THEN
		update piezas_con_marcas set marca_id = marca_id 
		where pieza_id = id;
	ELSEIF (marca_id != 0) THEN
        insert into piezas_con_marcas values (id,marca_id);
	ELSE 
        delete from piezas_con_marcas where pieza_id = id ;
	END IF ;

    IF (exists(select * from piezas_con_proveedores where pieza_id = id) AND proveedor_id != 0) THEN
		 update piezas_con_proveedores set proveedor_id = proveedor_id 
		 where pieza_id = id;
    ELSEIF (proveedor_id != 0) THEN
        insert into piezas_con_proveedores values (id,proveedor_id);
	ELSE 
        delete from piezas_con_proveedores where pieza_id = id;
	END IF ;


END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `pz_cambiarEstado` (in id int, in accion varchar(50))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

BEGIN
 declare estado_id int;
END;

IF (accion = "desactivar") THEN 

SET @estado_id = (SELECT estado_id FROM estados_generales WHERE nombre_estado = 'Inactivo');
UPDATE piezas SET estado_id = @estado_id WHERE pieza_id = id;

ELSEIF (accion = "activar") THEN

SET @estado_id = (SELECT estado_id FROM estados_generales WHERE nombre_estado = 'Activo');
UPDATE piezas SET estado_id = @estado_id WHERE pieza_id = id;

END IF ;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `pz_eliminarPieza` (in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 delete from piezas where pieza_id = id;
 select 'ready' AS msg;
 
END $$
DELIMITER ;


# -------------- Ventas -----------------

DELIMITER $$
CREATE PROCEDURE `vt_crearDetalleTemporal` (in producto_id int, in pieza_id int, in servicio_id int, 
in descripcion varchar(100), in usuario_id int, in cantidad int,in costo decimal(10,2), in precio int, 
in impuesto int, in descuento int)
BEGIN
 
 DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        SELECT 'Error general de SQL (SQLEXCEPTION)' AS msg;
    END;
    DECLARE EXIT HANDLER FOR SQLSTATE '23000' 
    BEGIN
        SELECT 'Violación de integridad referencial o restricción única (SQLSTATE 23000)' AS msg;
    END;
    DECLARE EXIT HANDLER FOR 1062 
    BEGIN
        SELECT 'Error: clave duplicada (1062)' AS msg;
    END;

 IF (producto_id > 0) THEN
  insert into detalle_temporal values (null,usuario_id,producto_id,0,0,descripcion,cantidad,costo,precio,impuesto,descuento,curtime(),curdate());
  select last_insert_id() AS msg;
ELSEIF (pieza_id > 0) THEN
  insert into detalle_temporal values (null,usuario_id,0,pieza_id,0,descripcion,cantidad,costo,precio,impuesto,descuento,curtime(),curdate());
  select last_insert_id() AS msg;
ELSEIF (servicio_id > 0) THEN
 insert into detalle_temporal values (null,usuario_id,0,0,servicio_id,descripcion,1,costo,precio,impuesto,descuento,curtime(),curdate());
 select last_insert_id() AS msg;
 END IF;

END $$
DELIMITER ; 


DELIMITER $$
CREATE PROCEDURE `vt_variantesFacturadas` (in variante_id int, in detalle_id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 DECLARE EXIT HANDLER FOR 1062 SELECT 'Duplicate keys error encountered' AS msg;

  insert into variantes_facturadas values (variante_id,detalle_id);
  select 'ready' AS msg;

END $$
DELIMITER ; 


DELIMITER $$
CREATE PROCEDURE `vt_detalleVarianteTemp` (in variante_id int, in detalle_temporal_id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 DECLARE EXIT HANDLER FOR 1062 SELECT 'Duplicate keys error encountered' AS msg;

  insert into detalle_variantes_temporal values (detalle_temporal_id,variante_id);
  select 'ready' AS msg;

END $$
DELIMITER ; 


DELIMITER $$
CREATE PROCEDURE `vt_crearDetalleVenta` (in producto_id int, in pieza_id int, in servicio_id int, 
in factura_id int, in usuario_id int, in cantidad int,in costo decimal(10,2),in precio int, in impuesto int, in descuento int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 DECLARE EXIT HANDLER FOR 1062 
 BEGIN
  SELECT 'Duplicate keys error encountered' AS msg;
  DELETE FROM detalle_facturas_ventas WHERE detalle_venta_id = @last_id;
 END;
  
 begin
 declare last_id int;
 end;

    insert into detalle_facturas_ventas values (null,factura_id,usuario_id,cantidad,costo,precio,impuesto,descuento,curdate());
    select last_insert_id() AS msg;
    SET @last_id = (select last_insert_id() AS msg);
 
IF (producto_id > 0) THEN
  insert into detalle_ventas_con_productos values (@last_id,producto_id,factura_id);

ELSEIF (pieza_id > 0) THEN
  insert into detalle_ventas_con_piezas values (@last_id,pieza_id,factura_id);
  
ELSEIF (servicio_id > 0) THEN
 insert into detalle_ventas_con_servicios values (@last_id,servicio_id,factura_id);

 END IF;
 
END $$
DELIMITER ; 


DELIMITER $$
CREATE PROCEDURE `vt_eliminarDetalleTemporal` (in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

delete from detalle_temporal where detalle_temporal_id = id;
select 'ready' AS msg;

END $$
DELIMITER ; 


DELIMITER $$
CREATE PROCEDURE `vt_eliminarDetalleVenta` (in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

delete from detalle_facturas_ventas where detalle_venta_id = id;
select 'ready' AS msg;

END $$
DELIMITER ; 


DELIMITER $$
CREATE PROCEDURE `vt_facturaVenta` (in cliente_id int, in metodo_id int, in total int, in bono int, 
in usuario_id int, in descripcion varchar(150), in fecha date)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 
 begin 
 declare totalx int;
 declare min_factura int;
 declare valor_bono int;
 declare bono_config_estado int;
 end;
 
 SET @totalx = (0 - bono);
 SET @min_factura = (select min_factura from bono_config where bono_config_id = 1);
 SET @valor_bono = (select valor from bono_config where bono_config_id = 1);
 SET @bono_config_estado = (select estado_id from bono_config where bono_config_id = 1);

 -- Crear factura

 insert into facturas_ventas values (null,usuario_id,cliente_id,3,metodo_id,@totalx,total,0,bono,descripcion,fecha);
 select last_insert_id() AS msg;
 
 -- Aplicar bono
 
 IF (total >= @min_factura AND @bono_config_estado = 1 AND cliente_id != 1) THEN 
  insert into bonos values (null,usuario_id,cliente_id,@valor_bono,curdate());
 END IF ;
 
 -- Eliminar bono
 
 IF (bono > 0) THEN 
   delete from bonos where cliente_id = cliente_id;
 END IF ;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `vt_facturaAcredito` (in cliente_id int, in metodo_id int, in total int, in pago int, in pendiente int, in usuario_id int, in descripcion varchar(150), in fecha date)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 insert into facturas_ventas values (null,usuario_id,cliente_id,4,metodo_id,0,pago,pendiente,0,descripcion,fecha);
 select last_insert_id() AS msg;
 

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `vt_eliminarFacturaVenta` (in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

delete from detalle_facturas_ventas where factura_venta_id = id;
delete from facturas_ventas where factura_venta_id = id;
select 'ready' AS msg;

END $$
DELIMITER ; 


DELIMITER $$
CREATE PROCEDURE `vt_actualizarFactura` (in id int, in cliente_id int, in descripcion varchar(150), in metodo_id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 update facturas_ventas set cliente_id = cliente_id, metodo_pago_id = metodo_id, 
 descripcion = descripcion where factura_venta_id = id;
 select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `vt_actualizarDineroFactura` (in id int, in recibido int, in pendiente int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 update facturas_ventas set recibido = recibido, pendiente = pendiente where factura_venta_id = id;
 select 'ready' AS msg;

END $$
DELIMITER ;

# -------------- Pagos -----------------


DELIMITER $$
CREATE PROCEDURE `pg_crearPago` (in usuario_id int, in cliente_id int, in recibido int, in factura_id int, in facturaRP_id int, in metodo int, in descripcion varchar(150), in fecha date)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 begin
 declare last_id int;
 end;

insert into pagos values (null,usuario_id,cliente_id,metodo,recibido,descripcion,fecha);
SET @last_id = (select last_insert_id() AS msg);

IF (factura_id > 0) THEN
  insert into pagos_a_facturas_ventas values (@last_id,factura_id);
ELSEIF (facturaRP_id > 0) THEN
  insert into pagos_a_facturasRP values (@last_id,facturaRP_id);
END IF;
 
select last_insert_id() AS msg;

END $$
DELIMITER ; 


DELIMITER $$
CREATE PROCEDURE `pg_eliminarPago` (in id int, in factura_id int, in facturaRP_id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 
 
 IF (factura_id > 0) THEN
   delete from pagos_a_facturas_ventas where pago_id = id;
ELSEIF (facturaRP_id > 0) THEN
  delete from pagos_a_facturasRP where pago_id = id;
END IF;

select 'ready' AS msg;

END $$
DELIMITER ; 


DELIMITER $$
CREATE PROCEDURE `pg_pagarFactura` (in usuario_id int, in proveedor_id int, in recibido int, 
in factura_id int, in metodo int, in descripcion varchar(150))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

insert into pagos_proveedores values (null,factura_id,usuario_id,proveedor_id,metodo,recibido,descripcion,curdate());
select 'ready' AS msg;

END $$
DELIMITER ; 


DELIMITER $$
CREATE PROCEDURE `pg_eliminarPagoProveedor` (in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 
   delete from pagos_proveedores where pago_factura_id = id;
   select 'ready' AS msg;

END $$
DELIMITER ; 


# -------------- Ordenes de reparaciones -----------------

DELIMITER $$
CREATE PROCEDURE `rp_crearOrdenRP` (in usuario_id int, in cliente_id int, in equipo_id int, in serie varchar(50), in imei int, in observacion varchar(254))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;


 insert into ordenes_rp values (null,usuario_id,cliente_id,6,equipo_id,serie,imei,observacion,curdate(),null);
 select last_insert_id() AS msg;

 
END $$
DELIMITER ;

 
DELIMITER $$
CREATE PROCEDURE `rp_agregarCondiciones` (in condicion_id int, in orden_id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 
 insert into ordenes_rp_con_condiciones values (condicion_id,orden_id);
 select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `rp_crearCondicion` (in usuario_id int ,in condicion varchar(100))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 
 insert into condiciones values (null,usuario_id,condicion,curdate());
 select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `rp_crearEquipo` (in marca_id int ,in nombre_modelo varchar(50), in modelo varchar(50))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 
 insert into equipos values (null,marca_id,nombre_modelo,modelo);
 select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `rp_eliminarOrdenRP` (in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 
 delete from ordenes_rp where orden_rp_id = id;
 select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `rp_crearDetalleOrdenRP` (in usuario_id int, in pieza_id int, in orden_id int, 
in servicio_id int, in descripcion varchar(50), in cantidad int,in costo DECIMAL(10,2),in precio int, in descuento int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 DECLARE EXIT HANDLER FOR 1062 SELECT 'Duplicate keys error encountered' AS msg;
 
 begin
 declare last_id int;
 end;
 
    insert into detalle_ordenRP values (null,usuario_id,orden_id,descripcion,cantidad,costo,precio,descuento,curdate());
    
    SET @last_id = (select last_insert_id() AS msg);
     
    IF (pieza_id > 0) THEN 
     insert into detalle_ordenRP_con_piezas values (@last_id,pieza_id);
	ELSEIF (servicio_id > 0) THEN 
     insert into detalle_ordenRP_con_servicios values (@last_id,servicio_id);
    END IF;

select @last_id AS msg;

END $$
DELIMITER ; 


DELIMITER $$
CREATE PROCEDURE `rp_eliminarDetalleOrdenRP` (in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

delete from detalle_ordenRP where detalle_ordenRP_id = id;
select 'ready' AS msg;

END $$
DELIMITER ; 


DELIMITER $$
CREATE PROCEDURE `rp_actualizarEstadoOrden` (in estado_id int, in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

update ordenes_rp set estado_id = estado_id where orden_rp_id = id;
IF (estado_id = 7) THEN
 update ordenes_rp set fecha_salida = curdate() where orden_rp_id = id;
END IF;

select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `rp_facturaVenta` (in cliente_id int, in orden_id int, in metodo_id int, in total int, in usuario_id int,in descripcion varchar(150), in fecha date)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 insert into facturasRP values (null,orden_id,usuario_id,cliente_id,metodo_id,3,total,total,0,descripcion,fecha);
 select last_insert_id() AS msg;
 
END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `rp_facturaAcredito` (in cliente_id int, in orden_id int, in metodo_id int, 
in total int, in pago int, in pendiente int, in usuario_id int,in descripcion varchar(150), in fecha date)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 
 begin 
 declare pendienteX int;
 end;

SET @pendientX = pendiente - pago;

 insert into facturasRP values (null,orden_id,usuario_id,cliente_id,metodo_id,4,total,pago,@pendientX,descripcion,fecha);
 select last_insert_id() AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `rp_eliminarFactura` (in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 delete from facturasRP where facturaRP_id = id;
 select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `rp_actualizarFactura` (in id int, in cliente_id int, in metodo_id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 update facturasRP set cliente_id = cliente_id, metodo_pago_id = metodo_id where orden_rp_id = id;
 select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `rp_actualizarDineroFactura` (in id int, in recibido int, in pendiente int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 update facturasRP set recibido = recibido, pendiente = pendiente where facturaRP_id = id;
 select 'ready' AS msg;

END $$
DELIMITER ;

# -------------- Ordenes de compras -----------------

DELIMITER $$
CREATE PROCEDURE `or_ordenCompra` (in usuario_id int, in estado_id int, in proveedor_id int, 
in fecha date, in expiracion date, in observacion varchar(150))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

insert into ordenes_compras values (null,usuario_id,proveedor_id,estado_id,observacion,fecha,expiracion);
select last_insert_id() AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `or_detalleCompra` (in usuario_id int, in producto_id int, in pieza_id int, in orden_id int, in precio int, in cantidad int, 
in descuento int, in impuesto int, in observacion varchar(150))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
  DECLARE EXIT HANDLER FOR 1062 
 BEGIN
  SELECT 'Duplicate keys error encountered' AS msg;
  DELETE FROM detalle_compra WHERE detalle_compra_id = @last_id;
 END;
 
 begin
 declare last_id int;
 end ;

insert into detalle_compra values (null,usuario_id,orden_id,cantidad,precio,impuesto,descuento,observacion,curdate());
SET @last_id = (select last_insert_id() AS msg);

IF (producto_id > 0) THEN
 insert into detalle_compra_con_productos values (@last_id,producto_id,orden_id);
ELSEIF (pieza_id > 0) THEN
  insert into detalle_compra_con_piezas values (@last_id,pieza_id,orden_id);
END IF ;

select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `or_eliminarOrdenComp` (in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 
 delete from ordenes_compras where orden_id = id;
 select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `or_actualizarOrdenCompra` (in id int, in proveedor_id int,
in observacion varchar(150), in creacion date, in expiracion date)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

update ordenes_compras set proveedor_id = proveedor_id,   
observacion = observacion, fecha = creacion, expiracion = expiracion where orden_id = id;
select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `or_eliminarItemDetalleCompra` (in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 
 delete from detalle_compra where detalle_compra_id = id;
 select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `or_facturaCompra` (in proveedor_id int, in ordenId int, in metodo_id int, 
in total int, in usuario_id int, in fecha date, in observacion varchar(150))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 insert into facturas_proveedores values (null,usuario_id,proveedor_id,ordenId,metodo_id,3,total,total,0,observacion,fecha);
 update ordenes_compras set estado_id = 12 where orden_id = ordenId;
 select last_insert_id() AS msg;
 
END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `or_facturaAcredito` (in proveedor_id int, in ordenId int, in metodo_id int, 
in total int, in pago int, in pendiente int, in usuario_id int, in fecha date, in observacion varchar(150))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 
 begin 
 declare pendienteX int;
 end;

SET @pendientX = pendiente - pago;

 insert into facturas_proveedores values (null,usuario_id,proveedor_id,ordenId,metodo_id,4,total,pago,@pendientX,observacion,fecha);
 update ordenes_compras set estado_id = 12 where orden_id = ordenId;
 select last_insert_id() AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `or_actualizarFacturaCompra` (in id int, in proveedor_id int,
in observacion varchar(150), in creacion date)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

update facturas_proveedores set proveedor_id = proveedor_id,   
observacion = observacion, fecha = creacion where factura_proveedor_id = id;
select 'ready' AS msg;

END $$


DELIMITER $$
CREATE PROCEDURE `or_eliminarFactura` (in id int, in ordenId int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 
 delete from facturas_proveedores where factura_proveedor_id = id;
 update ordenes_compras set estado_id = 6 where orden_id = ordenId;
 select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `or_ordenGasto` (in proveedor_id int, in usuario_id int,in origen varchar(12) ,in fecha date)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 insert into ordenes_gastos values (null,usuario_id,proveedor_id,origen,fecha);
 select last_insert_id() AS msg;
 
END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `or_registrarGasto` (in proveedor_id int, in orden_id int, in total int, 
in usuario_id int, in observacion varchar(150), in fecha date)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 insert into gastos values (null,usuario_id,proveedor_id,orden_id,total,total,observacion,fecha);
 select 'ready' AS msg;
 
END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `or_detalleGasto` (in motivo_id int, in orden_id int, in cantidad int, in precio int, in impuestos int, 
in usuario_id int, in observacion varchar(150))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 insert into detalle_gasto values (null,usuario_id,motivo_id,orden_id,cantidad,precio,impuestos,observacion,curdate());
 select 'ready' AS msg;
 
END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `or_eliminarGasto` (in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 
 delete from ordenes_gastos where orden_id = id;
 select 'ready' AS msg;

END $$
DELIMITER ;

# -------------- Motivos de gastos -----------------

DELIMITER $$
CREATE PROCEDURE `g_agregar_motivo` (in usuario_id int , in descripcion varchar(60))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

INSERT INTO motivos VALUES (null,usuario_id,descripcion,curdate());
select 'ready' AS msg;

END $$
DELIMITER ;

# -------------- Servicios -----------------

DELIMITER $$
CREATE PROCEDURE `sv_agregarServicio` (in usuario_id int, in nombre varchar(70), in costo decimal(10,2), in precio int)
BEGIN

 DECLARE EXIT HANDLER FOR 1062 SELECT 'Duplicate keys error encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

INSERT INTO servicios VALUES (null,usuario_id,nombre,costo,precio,curdate());
select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `sv_actualizarServicio` (in id int, in _nombre varchar(70), in _costo decimal(10,2), in _precio int)
BEGIN

 DECLARE EXIT HANDLER FOR 1062 SELECT 'Duplicate keys error encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

UPDATE servicios SET nombre_servicio = _nombre, precio = _precio, costo = _costo WHERE servicio_id = id;
select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `sv_eliminarServicio` (in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

DELETE FROM servicios WHERE servicio_id = id;
select 'ready' AS msg;

END $$
DELIMITER ;



# -------------- Marcas -----------------


DELIMITER $$
CREATE PROCEDURE `m_eliminarMarca` (in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 delete from marcas where marca_id = id;
 select 'ready' AS msg;
 
END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `m_crearMarca` (in nombre varchar(50))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 insert into marcas values (null,nombre,curdate());
 select 'ready' AS msg;
 
END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `m_actualizarMarca` (in nombre varchar(50),in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 update marcas set nombre_marca = nombre where marca_id = id;
 select 'ready' AS msg;
 
END $$
DELIMITER ;


#--------------- Cotizaciones ------------------


DELIMITER $$
CREATE PROCEDURE `ct_cotizacion`(in cliente_id int, in usuario_id int, in total int, 
in descripcion varchar(150), in fecha date)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 insert into cotizaciones values (null,usuario_id,cliente_id,total,descripcion,fecha);
 select last_insert_id() AS msg;
 
END $$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `ct_eliminarCotizacion`(in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

delete from cotizaciones where cotizacion_id = id;
select 'ready' AS msg;

END $$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `ct_eliminarDetalle`(in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

delete from detalle_cotizaciones where detalle_id = id;
select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `ct_actualizarCotizacion`(in cliente_id int,in id int, in descripcion varchar(150), in fecha date)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

UPDATE cotizaciones SET cliente_id = cliente_id, total = total, 
fecha = fecha, descripcion = descripcion WHERE cotizacion_id = id;

 select "ready" AS msg;
 
END $$
DELIMITER ;


# -------------- Configuración -----------------

DELIMITER $$
CREATE PROCEDURE `cf_bono_config` (in usuario_id int, in min_factura int, in valor int, in estado_id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

update bono_config set usuario_id = usuario_id, min_factura = min_factura,
valor = valor, estado_id = estado_id where bono_config_id = 1;
select 'ready' AS msg;

END $$
DELIMITER ; 


DELIMITER $$
CREATE PROCEDURE `cf_factElectronica` (in logo varchar(100),in empresa varchar(50), in email varchar(50), in password varchar(80),
in host varchar(35),in smtps varchar(3), in port int, in link_fb varchar(100), in link_ws varchar(100), in link_ig varchar(100))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

update configuraciones set empresa = empresa, email = email, password = password,
host = host, smtps = smtps, puerto = port, link_fb = link_fb, link_ig = link_ig, link_ws = link_ws, logo_url = logo 
where config_id = 1;
select 'ready' AS msg;

END $$
DELIMITER ; 


DELIMITER $$
CREATE PROCEDURE `cf_factPDF` (in logo varchar(50), in slogan varchar(100), in direccion varchar(100),
in tel varchar(20), in condiciones varchar(400), in titulo varchar(200))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

update configuraciones set logo_pdf = logo, slogan = slogan, direccion = direccion,
tel = tel, condiciones = condiciones, titulo = titulo where config_id = 1;
select 'ready' AS msg;

END $$
DELIMITER ; 

# -------------- Cierre de caja -----------------


DELIMITER $$
CREATE PROCEDURE `c_aperturaCaja` (IN usuario_id INT, IN fecha_apertura DATETIME, IN saldo_inicial DECIMAL(10,2))
BEGIN
    DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

    -- Insertar los datos en la tabla cierres_caja como apertura
    INSERT INTO cierres_caja (usuario_id,fecha_apertura,saldo_inicial,estado) 
    VALUES (usuario_id,fecha_apertura,saldo_inicial,'abierto');

    -- Retornar el ID del nuevo registro de apertura
    SELECT LAST_INSERT_ID() AS msg;
END$$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `c_cierreCaja` (IN _usuario_id INT,IN _fecha_cierre DATETIME,IN _saldo_inicial DECIMAL(10,2),
IN _ingresos_efectivo DECIMAL(10,2),IN _ingresos_tarjeta DECIMAL(10,2),IN _ingresos_transferencia DECIMAL(10,2),
IN _ingresos_cheque DECIMAL(10,2),IN _egresos_caja DECIMAL(10,2),IN _egresos_fuera DECIMAL(10,2),IN _retiros DECIMAL(10,2),
IN _reembolsos DECIMAL(10,2),IN _total_real DECIMAL(10,2),IN _efectivo_caja DECIMAL(10,2),IN _observaciones TEXT)
BEGIN
    DECLARE _total_esperado DECIMAL(10,2);
    DECLARE _cierre_id INT;
	DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
    
    -- Calcular el total esperado
    SET _total_esperado = _saldo_inicial + _ingresos_efectivo - _egresos_caja - _retiros - _reembolsos;

    -- Obtener el ID del último registro de apertura de hoy
    SELECT cierre_id INTO _cierre_id FROM cierres_caja
    WHERE DATE(fecha_apertura) = CURDATE() AND estado = 'abierto'
    ORDER BY cierre_id DESC LIMIT 1;

    -- Actualizar el registro existente
    UPDATE cierres_caja SET
        usuario_id = _usuario_id,
        fecha_cierre = _fecha_cierre,
        ingresos_efectivo = _ingresos_efectivo,
        ingresos_tarjeta = _ingresos_tarjeta,
        ingresos_transferencia = _ingresos_transferencia,
        ingresos_cheque = _ingresos_cheque,
        egresos_caja = _egresos_caja,
        egresos_fuera = _egresos_fuera,
        retiros = _retiros,
        reembolsos = _reembolsos,
        total_esperado = _total_esperado,
        total_real = _total_real,
        diferencia = _efectivo_caja - _total_esperado,
        observaciones = _observaciones,
        estado = 'cerrado'
    WHERE cierre_id = _cierre_id;

    -- Retornar el ID del nuevo cierre
	SELECT _cierre_id AS msg;
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `c_eliminarCierre` (IN _cierre_id INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
    
    DELETE FROM cierres_caja WHERE cierre_id = _cierre_id;
    select 'ready' AS msg;
    
END$$
DELIMITER ;