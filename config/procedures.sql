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

insert into lista_de_precios values (null,1,'Minimo 1','',curdate());
insert into lista_de_precios values (null,1,'Minimo 2','',curdate());
insert into lista_de_precios values (null,1,'Por mayor','',curdate());


insert into clientes values (null,1,null,'Consumidor Final',null,null,null,null,null,curdate());
                            
insert into proveedores values (null,1,'Consumidor Final',null,null,null,null,null,curdate());


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
insert into motivos values (null,1,'Gastos fijos',curdate());
insert into motivos values (null,1,'Gastos operativos',curdate());

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

INSERT INTO configuraciones
    (config_key, config_value, descripcion) 
VALUES 
    ('empresa_name', 'Codevrd', 'Nombre de la empresa'),
    ('correo_servidor', 'example@host.com', 'Correo electrónico de contacto'),
    ('password', 'XXXX XXXX XXXX XXXX', 'Contraseña del servidor de correo'),
    ('servidor', 'localhost', 'Host del servidor'),
    ('smtps', '', 'SMTP habilitado (true/false)'),
    ('puerto', '587', 'Puerto del servidor SMTP'),
    ('logo_url', '', 'URL del logo para la factura por correo'),
    ('logo', 'public/imagen/sistem/pdf.png', 'Logo del sistema'),
    ('slogan', 'En la web, en todas partes', 'Slogan de la empresa'),
    ('direccion', '-', 'Dirección de la empresa'),
    ('link_facebook', '', 'Enlace a Facebook'),
    ('link_whatsapp', '', 'Enlace a WhatsApp'),
    ('link_instagram', '', 'Enlace a Instagram'),
    ('telefono', '000-000-0000', 'Número de teléfono de contacto de la empresa'),
    ('condiciones', '-', 'terminos y condiciones de la factura pdf'),
    ('titulo', '-', 'Título del sitio web'),
    ('correo_adm','','correo del administracion del sistema'),
    ('carpeta', 'wsistem/', 'carpeta de imagenes'),
    ('tinify_API_KEY', '', 'Clave para comprimir las imagenes'),
	('auto_cierre', 'true', 'Cierre continuo que cierra automaticamente a la 12:00 am'),
    ('modo_cierre', 'separado', 'estilo de cierre de caja normal/separado');

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

DROP PROCEDURE IF EXISTS `cl_agregar_cliente`;
DELIMITER $$
CREATE PROCEDURE `cl_agregar_cliente` (
    IN usuario_id INT, 
    IN nombre VARCHAR(50), 
    IN apellidos VARCHAR(100),
    IN codigo VARCHAR(11), 
    IN tel1 VARCHAR(15), 
    IN tel2 VARCHAR(15),
    IN direccion VARCHAR(150), 
    IN email VARCHAR(50)
)
BEGIN
	DECLARE EXIT HANDLER FOR 1062 SELECT 'Error de clave duplicada encontrado' AS msg;
	DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'Error SQL encontrado' AS msg;
	DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'Error SQLSTATE 23000' AS msg;

    -- Verificar si el código es vacío o no
    IF (codigo != '') THEN
        -- Insertar el cliente con código
	   INSERT INTO clientes (usuario_id, cedula, nombre, apellidos, telefono1, telefono2, email, direccion, fecha)
       VALUES (usuario_id, codigo, nombre, apellidos, tel1, tel2, email, direccion, CURDATE());
       SELECT 'Registrado correctamente' as msg;
    ELSE
        -- Insertar el cliente sin código
        INSERT INTO clientes (usuario_id, cedula, nombre, apellidos, telefono1, telefono2, email, direccion, fecha)
        VALUES (usuario_id, NULL, nombre, apellidos, tel1, tel2, email, direccion, CURDATE());
        SELECT 'Registrado correctamente' as msg;
    END IF;
    
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
SELECT 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `cl_eliminarBono` (in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

DELETE FROM bonos WHERE bono_id = id;
SELECT 'ready' AS msg;

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


DROP PROCEDURE IF EXISTS `pr_agregarProducto`;
DELIMITER $$
CREATE PROCEDURE `pr_agregarProducto` (in usuario_id int, in almacen_id int, in codigo varchar(100),
in nombre varchar(100), in costo decimal(19,2), in precio decimal(19,2), in cantidad decimal(19,2), 
in cantidad_min decimal(19,2), in categoria_id int, in posicion_id int, in impuesto_id int, in oferta_id int, 
in marca_id int, in proveedor_id int)
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
select "ready" AS msg;

ELSEIF (accion = 'activar') THEN

SET @estado_id = (SELECT estado_id FROM estados_generales WHERE nombre_estado = 'Activo');
UPDATE productos SET estado_id = @estado_id WHERE producto_id = id;
select "ready" AS msg;

END IF ;

END $$
DELIMITER ;


DROP PROCEDURE IF EXISTS `pr_editarProducto`;
DELIMITER $$
CREATE PROCEDURE `pr_editarProducto` (in productoId int, in almacen_id int, in codigo varchar(100),
in nombre varchar(100), in costo decimal(19,2), in precio decimal(19,2), in cantidad decimal(19,2), 
in cantidad_min decimal(19,2), in categoria_id int, in posicion_id int, in impuesto_id int, in ofertaId int, 
in marca_id int, in proveedor_id int)
BEGIN

 DECLARE EXIT HANDLER FOR 1062 SELECT 'Duplicate keys error encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 IF (codigo != '') THEN
 
   update productos set nombre_producto = nombre, cod_producto = codigo, almacen_id = almacen_id, precio_unitario = precio,
   precio_costo = costo, cantidad = cantidad, cantidad_min = cantidad_min where producto_id = productoId;
   
   select "ready" AS msg;
ELSEIF(codigo = '') THEN
 
   update productos set nombre_producto = nombre, cod_producto = null, almacen_id = almacen_id, precio_unitario = precio,
   precio_costo = costo, cantidad = cantidad, cantidad_min = cantidad_min where producto_id = productoId;
   
   select "ready" AS msg;
ELSE 
   update productos set nombre_producto = nombre, almacen_id = almacen_id, precio_unitario = precio,
   precio_costo = costo, cantidad = cantidad, cantidad_min = cantidad_min where producto_id = productoId;
   
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


DROP PROCEDURE IF EXISTS `pz_agregarPieza`;
DELIMITER $$
CREATE PROCEDURE `pz_agregarPieza` (in usuario_id int, in almacen_id int, in codigo varchar(100),
in nombre varchar(100), in costo decimal(19,2), in precio decimal(19,2), in cantidad int, in cantidad_min int, 
in categoria_id int, in posicion_id int, in oferta_id int, in marca_id int,
in proveedor_id int, in imagen varchar(255))
BEGIN

 DECLARE last_id int;

 DECLARE EXIT HANDLER FOR 1062 SELECT 'Duplicate keys error encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 
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


DROP PROCEDURE IF EXISTS `pz_editarPieza`;
DELIMITER $$
CREATE PROCEDURE `pz_editarPieza` (in id int, in almacen_id int, in codigo varchar(100),
in nombre varchar(100), in costo decimal(10,2), in precio decimal(10,2),in cantidad int, in cantidad_min int, 
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

 DECLARE estado_id int;

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

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

DROP PROCEDURE IF EXISTS `vt_crearDetalleTemporal`;
DELIMITER $$
CREATE PROCEDURE `vt_crearDetalleTemporal` (in producto_id int, in pieza_id int, in servicio_id int, 
in descripcion varchar(100), in usuario_id int, in cantidad decimal(10,2),in costo decimal(10,2), in precio decimal(10,2), 
in impuesto decimal(10,2), in descuento decimal(10,2))
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
 insert into detalle_temporal values (null,usuario_id,0,0,servicio_id,descripcion,cantidad,costo,precio,impuesto,descuento,curtime(),curdate());
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


DROP PROCEDURE IF EXISTS `vt_agregarDetalleVenta`;
DELIMITER $$
CREATE PROCEDURE `vt_agregarDetalleVenta`(IN p_factura_id INT,IN p_comanda_id INT,IN p_usuario_id INT,
IN p_cantidad INT,IN p_costo DECIMAL(10,2),IN p_precio int, IN p_impuesto int,
IN p_descuento int,IN p_producto_id INT,IN p_pieza_id INT,IN p_servicio_id INT)
BEGIN

  DECLARE p_detalle_id INT;
  DECLARE v_fecha DATE;

  -- 1. Intentar obtener la fecha de la factura
  SELECT fecha INTO v_fecha
  FROM facturas_ventas
  WHERE factura_venta_id = p_factura_id;

  -- 2. Si no se encontró o es NULL, usar CURDATE()
  IF v_fecha IS NULL THEN
    SET v_fecha = CURDATE();
  END IF;

  -- 3. Insertar en detalle_facturas_ventas con la fecha determinada
  INSERT INTO detalle_facturas_ventas (
    factura_venta_id, comanda_id, usuario_id, cantidad, costo, precio, impuesto, descuento, fecha
  )
  VALUES (
    p_factura_id, p_comanda_id, p_usuario_id, p_cantidad, p_costo, p_precio, p_impuesto, p_descuento, v_fecha
  );

  -- 4. Obtener el ID del nuevo detalle
  SET p_detalle_id = LAST_INSERT_ID();

  -- 5. Insertar en la tabla correspondiente según el tipo
  IF p_producto_id > 0 THEN
    INSERT INTO detalle_ventas_con_productos (
      detalle_venta_id, producto_id, factura_venta_id, comanda_id
    )
    VALUES (
      p_detalle_id, p_producto_id, p_factura_id, p_comanda_id
    );

  ELSEIF p_pieza_id > 0 THEN
    INSERT INTO detalle_ventas_con_piezas_ (
      detalle_venta_id, pieza_id, factura_venta_id, comanda_id
    )
    VALUES (
      p_detalle_id, p_pieza_id, p_factura_id, p_comanda_id
    );

  ELSEIF p_servicio_id > 0 THEN
    INSERT INTO detalle_ventas_con_servicios (
      detalle_venta_id, servicio_id, factura_venta_id, comanda_id
    )
    VALUES (
      p_detalle_id, p_servicio_id, p_factura_id, p_comanda_id
    );
  END IF;

  -- 6. Retornar el ID del detalle
  SELECT p_detalle_id AS msg;

END$$
DELIMITER ;


DROP PROCEDURE IF EXISTS `vt_crearDetalleVenta`;
DELIMITER $$
CREATE PROCEDURE `vt_crearDetalleVenta` (in producto_id int, in pieza_id int, in servicio_id int, 
in factura_id int, in usuario_id int, in cantidad decimal(10,2),in costo decimal(10,2),
in precio decimal(10,2),in impuesto decimal(10,2), in descuento decimal(10,2))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 DECLARE EXIT HANDLER FOR 1062 
 BEGIN
  SELECT 'Duplicate keys error encountered' AS msg;
  DELETE FROM detalle_facturas_ventas WHERE detalle_venta_id = @last_id;
 END;
  
 BEGIN
 DECLARE last_id INT;
 END;

    INSERT INTO detalle_facturas_ventas 
    (factura_venta_id, comanda_id, usuario_id, cantidad, costo, precio, impuesto, descuento, fecha) 
	VALUES (factura_id,usuario_id,cantidad,costo,precio,impuesto,descuento,curdate());
    
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


DROP PROCEDURE IF EXISTS `vt_facturaVenta`;
DELIMITER $$
CREATE PROCEDURE `vt_facturaVenta` (in cliente_id int, in metodo_id int, in total decimal(10,2), in bono decimal(10,2), 
in usuario_id int, in descripcion varchar(150), in fecha date)
BEGIN

 declare totalx decimal(10,2);
 declare min_factura int;
 declare valor_bono int;
 declare bono_config_estado int;

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 
 IF (bono IS NOT NULL AND bono > 0) THEN
    SET totalx = (total - bono);
ELSE 
    SET totalx = total;
END IF;

-- Verificar que existe la configuración de bono
    IF EXISTS (SELECT 1 FROM bono_config WHERE bono_config_id = 1) THEN
        -- Cargar configuración de bono
        SELECT min_factura, valor, estado_id
          INTO min_factura, valor_bono, bono_config_estado
          FROM bono_config
         WHERE bono_config_id = 1;
    ELSE
        -- Si no existe la configuración, devolver error
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La configuración de bono no existe';
    END IF;

 -- Crear factura

 INSERT INTO facturas_ventas (usuario_id, cliente_id, estado_id, metodo_pago_id, total, recibido, pendiente, bono, descripcion, fecha)
 VALUES (usuario_id, cliente_id, 3, metodo_id, totalx, total, 0, bono, descripcion, fecha);

 select last_insert_id() AS msg;
 
 -- Aplicar bono
IF (total >= min_factura AND bono_config_estado = 1 AND cliente_id != 1) THEN
    INSERT INTO bonos VALUES (null, usuario_id, cliente_id, valor_bono, curdate());
END IF;

-- Eliminar bono
IF (bono > 0) THEN
    DELETE FROM bonos WHERE cliente_id = cliente_id;
END IF;

END $$
DELIMITER ;


DROP PROCEDURE IF EXISTS vt_facturaAcredito;
DELIMITER $$
CREATE PROCEDURE `vt_facturaAcredito` (IN p_cliente_id INT,IN p_metodo_id INT,
IN p_total DECIMAL(10,3),IN p_pago DECIMAL(10,2),IN p_usuario_id INT,
IN p_descripcion VARCHAR(150),IN p_fecha DATE)
BEGIN
    DECLARE v_pendiente DECIMAL(10,2);

    -- Calcular pendiente
    SET v_pendiente = ROUND(p_total - p_pago, 2);

    -- Insertar la factura
    INSERT INTO facturas_ventas (factura_venta_id,usuario_id,cliente_id,
	estado_id,metodo_pago_id,total,recibido,pendiente,descripcion,fecha
    ) VALUES (NULL,p_usuario_id,p_cliente_id,4,p_metodo_id,p_total,
	p_pago,v_pendiente,p_descripcion,p_fecha);

    -- Retornar el ID generado
    SELECT LAST_INSERT_ID() AS msg;
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


-- DELIMITER $$
-- CREATE PROCEDURE `vt_actualizarDineroFactura` (in id int, in recibido int, in pendiente int)
-- BEGIN

--  DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
--  DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

--  update facturas_ventas set recibido = recibido, pendiente = pendiente where factura_venta_id = id;
--  select 'ready' AS msg;

-- END $$
-- DELIMITER ;

# -------------- Pagos -----------------


DROP PROCEDURE IF EXISTS `pg_crearPago`;
DELIMITER $$
CREATE PROCEDURE `pg_crearPago` (in usuario_id int, in cliente_id int, in recibido decimal(10,2), 
in factura_id int, in facturaRP_id int, in metodo int, in descripcion varchar(150), in fecha date)
BEGIN

 DECLARE last_id int;

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

INSERT INTO pagos (usuario_id, cliente_id, metodo_pago_id, recibido, observacion, fecha)
VALUES (usuario_id, cliente_id, metodo, recibido, descripcion, fecha);

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


DROP PROCEDURE IF EXISTS `pg_pagarFactura`;
DELIMITER $$
CREATE PROCEDURE `pg_pagarFactura` (in usuario_id int, in proveedor_id int, in recibido decimal(10,2), 
in factura_id int, in metodo int, in descripcion varchar(150))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

INSERT INTO pagos_proveedores (factura_proveedor_id,usuario_id,proveedor_id,metodo_pago_id,recibido,observacion,fecha) 
VALUES (factura_id,usuario_id,proveedor_id,metodo,recibido,descripcion,curdate());
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


DROP PROCEDURE IF EXISTS `rp_crearDetalleOrdenRP`;
DELIMITER $$
CREATE PROCEDURE `rp_crearDetalleOrdenRP` (in usuario_id int, in pieza_id int, in orden_id int, 
in servicio_id int, in descripcion varchar(50), in cantidad DECIMAL(10,2),in costo DECIMAL(10,2),
in precio DECIMAL(10,2), in descuento DECIMAL(10,2))
BEGIN

 DECLARE last_id int;

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 DECLARE EXIT HANDLER FOR 1062 SELECT 'Duplicate keys error encountered' AS msg;
 
    INSERT INTO detalle_ordenRP 
    (usuario_id,orden_rp_id,descripcion,cantidad,costo,precio,descuento,fecha) 
    VALUES (usuario_id,orden_id,descripcion,cantidad,costo,precio,descuento,curdate());
    
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


DROP PROCEDURE IF EXISTS `rp_facturaVenta`;
DELIMITER $$
CREATE PROCEDURE `rp_facturaVenta` (in _cliente_id int, in _orden_id int, in _metodo_id int, 
in _total decimal(10,2), in _usuario_id int,in _descripcion varchar(150), in _fecha date)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
 
 update detalle_ordenRP set fecha = _fecha where orden_rp_id = _orden_id;

 INSERT INTO facturasRP (orden_rp_id, usuario_id, cliente_id, metodo_pago_id, estado_id, total, recibido, pendiente, descripcion, fecha)
 VALUES (_orden_id, _usuario_id, _cliente_id, _metodo_id, 3, _total, _total, 0, _descripcion, _fecha);

 select last_insert_id() AS msg;
 
END $$
DELIMITER ;


DROP PROCEDURE IF EXISTS `rp_facturaAcredito`;
DELIMITER $$
CREATE PROCEDURE `rp_facturaAcredito` (in _cliente_id int, in _orden_id int, in _metodo_id int, 
in _total decimal(10,2), in _pago decimal(10,2), in _pendiente decimal(10,2), in _usuario_id int,
in _descripcion varchar(150), in _fecha date)
BEGIN

 DECLARE pendienteX int;
 
 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

SET @pendientX = pendiente - pago;

 update detalle_ordenRP set fecha = _fecha where orden_rp_id = _orden_id;


 INSERT INTO facturasRP (orden_rp_id, usuario_id, cliente_id, metodo_pago_id, estado_id, total, recibido, pendiente, descripcion, fecha)
 VALUES (_orden_id, _usuario_id, _cliente_id, _metodo_id, 4, _total, _pago, @pendientX, _descripcion, _fecha);

 select last_insert_id() AS msg;

END $$
DELIMITER ;


DROP PROCEDURE IF EXISTS `rp_eliminarFactura`;
DELIMITER $$
CREATE PROCEDURE `rp_eliminarFactura` (in orden_id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 -- 1. Eliminar los detalles de la orden
 delete from detalle_ordenRP where orden_rp_id = orden_id;
 
 -- 2. Eliminar la factura 
 delete from facturasRP where orden_rp_id = orden_id;
 
  -- 1. Devolver el mensaje
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


-- DELIMITER $$
-- CREATE PROCEDURE `rp_actualizarDineroFactura` (in id int, in recibido int, in pendiente int)
-- BEGIN

--  DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
--  DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

--  update facturasRP set recibido = recibido, pendiente = pendiente where facturaRP_id = id;
--  select 'ready' AS msg;

-- END $$
-- DELIMITER ;

# -------------- Ordenes de compras -----------------

DELIMITER $$
CREATE PROCEDURE `or_ordenCompra` (in usuario_id int, in estado_id int, in proveedor_id int, 
in fecha date, in expiracion date, in observacion varchar(150),in origen varchar(10))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

insert into ordenes_compras values (null,usuario_id,proveedor_id,estado_id,observacion,origen,fecha,expiracion);
select last_insert_id() AS msg;

END $$
DELIMITER ;


DROP PROCEDURE IF EXISTS `or_detalleCompra`;
DELIMITER $$
CREATE PROCEDURE `or_detalleCompra` (in usuario_id int, in producto_id int, in pieza_id int, 
in orden_id int, in precio decimal(10,2), in cantidad decimal(10,2), 
in descuento decimal(10,2), in impuesto decimal(10,2), in observacion varchar(150))
BEGIN

 declare last_id int;

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;
  DECLARE EXIT HANDLER FOR 1062 
 BEGIN
  SELECT 'Duplicate keys error encountered' AS msg;
  DELETE FROM detalle_compra WHERE detalle_compra_id = @last_id;
 END;

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


DROP PROCEDURE IF EXISTS `or_facturaCompra`;
DELIMITER $$
CREATE PROCEDURE `or_facturaCompra` (in proveedor_id int, in ordenId int, in metodo_id int, 
in total decimal(10,2), in usuario_id int, in fecha date, in observacion varchar(150))
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

  -- Crear factura de proveedor
 INSERT INTO facturas_proveedores (usuario_id,proveedor_id,orden_id,metodo_pago_id,estado_id,total,pagado,por_pagar,observacion,fecha)
 VALUES (usuario_id,proveedor_id,ordenId,metodo_id,3,total,total,0,observacion,fecha);
 
 -- Actualizar estado de la orden de compra
 UPDATE ordenes_compras SET estado_id = 12 WHERE orden_id = ordenId;
 
 select last_insert_id() AS msg;
 
END $$
DELIMITER ;


DROP PROCEDURE IF EXISTS `or_facturaAcredito`;
DELIMITER $$
CREATE PROCEDURE `or_facturaAcredito` (in proveedor_id int, in ordenId int, in metodo_id int, 
in total decimal(10,2), in pago decimal(10,2), in pendiente decimal(10,2), in usuario_id int, 
in fecha date, in observacion varchar(150))
BEGIN

 DECLARE pendienteX int;

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

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


DROP PROCEDURE IF EXISTS `or_registrarGasto`;
DELIMITER $$
CREATE PROCEDURE `or_registrarGasto` (in proveedor_id int, in orden_id int, in total decimal(10,2), 
in usuario_id int, in observacion varchar(150), in fecha date)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

 
 INSERT INTO gastos (usuario_id, proveedor_id, orden_id, total, pagado, observacion, fecha)
 VALUES (usuario_id, proveedor_id, orden_id, total, total, observacion, fecha);

 select 'ready' AS msg;
 
END $$
DELIMITER ;


DROP PROCEDURE IF EXISTS `or_detalleGasto`;
DELIMITER $$
CREATE PROCEDURE `or_detalleGasto` (in motivo_id int, in orden_id int, in cantidad decimal(10,2), 
in precio decimal(10,2), in impuestos decimal(10,2), in usuario_id int, in observacion varchar(150))
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


DROP PROCEDURE IF EXISTS `ct_cotizacion`;
DELIMITER $$
CREATE PROCEDURE `ct_cotizacion`(in cliente_id int, in usuario_id int, in total decimal(10,2), 
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


DROP PROCEDURE IF EXISTS `cf_factElectronica`;
DELIMITER $$
CREATE PROCEDURE `cf_factElectronica` (
    IN p_logo VARCHAR(100),
    IN p_empresa VARCHAR(50),
    IN p_email VARCHAR(50),
    IN p_password VARCHAR(80),
    IN p_host VARCHAR(35),
    IN p_smtps VARCHAR(3),
    IN p_port INT,
    IN p_link_fb VARCHAR(100),
    IN p_link_ws VARCHAR(100),
    IN p_link_ig VARCHAR(100)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
        SELECT 'SQLException encountered' AS msg;
    DECLARE EXIT HANDLER FOR SQLSTATE '23000' 
        SELECT 'SQLSTATE 23000' AS msg;

    -- Actualiza cada valor según su clave
    UPDATE configuraciones SET config_value = p_empresa  WHERE config_key = 'empresa_name';
    UPDATE configuraciones SET config_value = p_email    WHERE config_key = 'correo_servidor';
    UPDATE configuraciones SET config_value = p_password WHERE config_key = 'password';
    UPDATE configuraciones SET config_value = p_host     WHERE config_key = 'servidor';
    UPDATE configuraciones SET config_value = p_smtps    WHERE config_key = 'smtps';
    UPDATE configuraciones SET config_value = p_port     WHERE config_key = 'puerto';
    UPDATE configuraciones SET config_value = p_logo     WHERE config_key = 'logo_url';
    UPDATE configuraciones SET config_value = p_link_fb  WHERE config_key = 'link_facebook';
    UPDATE configuraciones SET config_value = p_link_ig  WHERE config_key = 'link_instagram';
    UPDATE configuraciones SET config_value = p_link_ws  WHERE config_key = 'link_whatsapp';

    SELECT 'ready' AS msg;
END $$
DELIMITER ;


DROP PROCEDURE IF EXISTS `cf_factPDF`;
DELIMITER $$
CREATE PROCEDURE `cf_factPDF` (
    IN p_logo VARCHAR(50),
    IN p_slogan VARCHAR(100),
    IN p_direccion VARCHAR(100),
    IN p_tel VARCHAR(20),
    IN p_condiciones VARCHAR(400),
    IN p_titulo VARCHAR(200)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
        SELECT 'SQLException encountered' AS msg;
    DECLARE EXIT HANDLER FOR SQLSTATE '23000' 
        SELECT 'SQLSTATE 23000' AS msg;

    UPDATE configuraciones SET config_value = p_logo       WHERE config_key = 'logo';
    UPDATE configuraciones SET config_value = p_slogan     WHERE config_key = 'slogan';
    UPDATE configuraciones SET config_value = p_direccion  WHERE config_key = 'direccion';
    UPDATE configuraciones SET config_value = p_tel        WHERE config_key = 'telefono';
    UPDATE configuraciones SET config_value = p_condiciones WHERE config_key = 'condiciones';
    UPDATE configuraciones SET config_value = p_titulo     WHERE config_key = 'titulo';

    SELECT 'ready' AS msg;
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


DROP PROCEDURE IF EXISTS `c_cierreCaja`;
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

    -- Obtener el ID del último registro de apertura de hoy con las horas de inicio y fin
    SELECT cierre_id
    INTO _cierre_id
    FROM cierres_caja
    WHERE estado = 'abierto'  -- Corregir el error de sintaxis (se agregó espacio después de WHERE)
    ORDER BY cierre_id DESC
    LIMIT 1;

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
        efectivo_caja = _efectivo_caja,
        total_esperado = _total_esperado,
        total_real = _total_real,
        diferencia = _efectivo_caja - _total_esperado,
        observaciones = _observaciones,
        estado = 'cerrado'
    WHERE cierre_id = _cierre_id;

    -- Retornar el ID del cierre actualizado
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


# ----- ordenes de ventas ---------

DELIMITER $$
CREATE PROCEDURE `ov_actualizarEstadoOrden` (in estado_id_ int, in id int)
BEGIN

 DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
 DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

update comandas set estado_id = estado_id_ where comanda_id = id;
select 'ready' AS msg;

END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `ov_eliminarOrden` (IN id INT)
BEGIN
    DECLARE factura_id INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
    DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

    START TRANSACTION;

    -- 1) Obtén el factura_id si existe
    SET factura_id = (SELECT factura_venta_id FROM detalle_facturas_ventas
         WHERE comanda_id = id LIMIT 1);

    -- 2) Borra todos los detalles de factura ligados a la comanda
    DELETE FROM detalle_facturas_ventas
	WHERE comanda_id = id;

    -- 3) Si había una factura, bórrala también
    IF factura_id IS NOT NULL THEN
        DELETE FROM facturas_ventas
         WHERE factura_venta_id = factura_id;
    END IF;

    -- 4) Borra la comanda
    DELETE FROM comandas WHERE comanda_id = id;

    COMMIT;

    SELECT 'ready' AS msg;
END$$

DELIMITER ;


DROP PROCEDURE IF EXISTS `ov_agregarOrden`;
DELIMITER $$
CREATE PROCEDURE `ov_agregarOrden` (
    IN o_cliente_id INT,
    IN o_usuario_id INT,
    IN o_estado_id INT,
    IN o_observacion TEXT,
    IN o_tipo_entrega VARCHAR(6),
    IN o_direccion_entrega TEXT,
    IN o_nombre_receptor VARCHAR(100),
    IN o_telefono_receptor VARCHAR(20)
)
BEGIN
    -- Declarar un manejador para los errores de SQL
    DECLARE exit handler for SQLEXCEPTION
    BEGIN
        -- Captura el error y devuelve un mensaje personalizado
        SELECT 'Error: No se pudo crear la orden' AS msg;
    END;

    -- Intentar insertar la nueva orden
    INSERT INTO comandas (cliente_id, usuario_id, estado_id, observacion, tipo_entrega,
    direccion_entrega, nombre_receptor, telefono_receptor, fecha) 
    VALUES (o_cliente_id, o_usuario_id, o_estado_id,
    o_observacion, o_tipo_entrega, o_direccion_entrega, o_nombre_receptor,
    o_telefono_receptor, NOW());

    -- Opcional: devolver el ID insertado si no hubo error
    SELECT LAST_INSERT_ID() AS msg;
END$$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE `ov_editarOrden` (IN o_comanda_id INT,IN o_cliente_id INT,IN o_usuario_id INT,
IN o_observacion TEXT,IN o_tipo_entrega varchar(6),IN o_direccion_entrega TEXT,
IN o_nombre_receptor VARCHAR(100),IN o_telefono_receptor VARCHAR(20))
BEGIN

   UPDATE comandas SET 
   cliente_id = o_cliente_id,
   usuario_id = o_usuario_id,
   observacion = o_observacion,
   tipo_entrega = o_tipo_entrega,
   direccion_entrega = o_direccion_entrega,
   nombre_receptor = o_nombre_receptor,
   telefono_receptor = o_telefono_receptor
   WHERE comanda_id = o_comanda_id;
    
    -- Opcional: devolver el ID insertado
    SELECT "ready" AS msg;
END$$
DELIMITER ;

# ----- Punto de venta ---------


DROP PROCEDURE IF EXISTS `pos_factura_venta`;
DELIMITER $$
CREATE PROCEDURE `pos_factura_venta` (IN _usuario_id INT, IN _cliente_id INT, 
IN _metodo_id INT, IN _comanda_id INT, IN _total DECIMAL(10,2))
BEGIN

	DECLARE factura_id INT;
    
    -- Manejo de errores generales y excepciones
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
        BEGIN
            -- Aquí puedes manejar el error, como un mensaje de error
            SELECT 'SQLException encountered' AS error_message;
        END;

    DECLARE EXIT HANDLER FOR SQLSTATE '23000' 
        BEGIN
            -- Este es para manejar errores específicos, por ejemplo, violación de clave primaria
            SELECT 'SQLSTATE 23000: Integrity constraint violation' AS error_message;
        END;
    
    -- Crear factura
    INSERT INTO facturas_ventas (usuario_id, cliente_id, estado_id, metodo_pago_id, total, recibido, pendiente, fecha)
    VALUES (_usuario_id, _cliente_id, 3, _metodo_id, _total, _total, 0, CURDATE());

    -- Asignar el ID de la nueva factura a la variable factura_id
    SET factura_id = LAST_INSERT_ID();
    
    IF (_comanda_id > 0) THEN
        -- Si hay una comanda_id, actualizar los detalles de la factura
        UPDATE detalle_facturas_ventas 
        SET factura_venta_id = factura_id 
        WHERE comanda_id = _comanda_id;
    ELSE
        -- Si no hay comanda_id, asignar el ID de la factura a los detalles sin comanda ni factura
        UPDATE detalle_facturas_ventas 
        SET factura_venta_id = factura_id 
        WHERE usuario_id = _usuario_id 
          AND comanda_id IS NULL
          AND factura_venta_id IS NULL;
    END IF;

    -- Si todo se ejecuta sin errores, puedes devolver un mensaje de éxito
    SELECT last_insert_id() AS msg;

END $$
DELIMITER ;


DROP PROCEDURE IF EXISTS `pos_factura_credito`;
DELIMITER $$
CREATE PROCEDURE `pos_factura_credito` (IN _usuario_id INT, IN _cliente_id INT, 
IN _metodo_id INT, IN _comanda_id INT, IN _total DECIMAL(10,2), 
IN _pago DECIMAL(10,2), IN _fecha DATE)
BEGIN

	DECLARE factura_id INT;
	DECLARE v_pendiente DECIMAL(10,2);
     
    -- Manejo de errores generales y excepciones
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
        BEGIN
            -- Aquí puedes manejar el error, como un mensaje de error
            SELECT 'SQLException encountered' AS error_message;
        END;

    DECLARE EXIT HANDLER FOR SQLSTATE '23000' 
        BEGIN
            -- Este es para manejar errores específicos, por ejemplo, violación de clave primaria
            SELECT 'SQLSTATE 23000: Integrity constraint violation' AS error_message;
        END;
        
	-- Calcular pendiente
    SET v_pendiente = ROUND(_total - _pago, 2);
    
    -- Crear factura
    INSERT INTO facturas_ventas (usuario_id, cliente_id, estado_id, metodo_pago_id, total, recibido, pendiente, fecha)
    VALUES (_usuario_id, _cliente_id, 4, _metodo_id, _total, _pago, v_pendiente, CURDATE());

    -- Asignar el ID de la nueva factura a la variable factura_id
    SET factura_id = LAST_INSERT_ID();
    
    IF (_comanda_id > 0) THEN
        -- Si hay una comanda_id, actualizar los detalles de la factura
        UPDATE detalle_facturas_ventas 
        SET factura_venta_id = factura_id 
        WHERE comanda_id = _comanda_id;
    ELSE
        -- Si no hay comanda_id, asignar el ID de la factura a los detalles sin comanda ni factura
        UPDATE detalle_facturas_ventas 
        SET factura_venta_id = factura_id 
        WHERE usuario_id = _usuario_id 
          AND comanda_id IS NULL
          AND factura_venta_id IS NULL;
    END IF;

    -- Si todo se ejecuta sin errores, puedes devolver un mensaje de éxito
    SELECT last_insert_id() AS msg;

END $$
DELIMITER ;



DROP PROCEDURE IF EXISTS `pos_agregar_servicio`;
DELIMITER $$

CREATE PROCEDURE `pos_agregar_servicio`(
    IN p_comanda_id INT,
    IN p_usuario_id INT,
    IN p_cantidad DECIMAL(10,2),
    IN p_costo DECIMAL(10,2),
    IN p_precio DECIMAL(10,2),
    IN p_servicio_id INT
)
BEGIN
    DECLARE v_detalle_id INT;
    DECLARE v_cantidad_existente DECIMAL(10,2);
    DECLARE v_comanda_exists INT DEFAULT 1;

    -- Validar comanda SOLO si viene
    IF p_comanda_id IS NOT NULL THEN
        SELECT COUNT(*) INTO v_comanda_exists
        FROM comandas
        WHERE comanda_id = p_comanda_id;

        IF v_comanda_exists = 0 THEN
            SELECT 'Error: No existe orden seleccionada' AS msg;
        END IF;
    END IF;

    -- Buscar detalle existente
    SELECT d.cantidad
    INTO v_cantidad_existente
    FROM detalle_facturas_ventas d
    INNER JOIN detalle_ventas_con_servicios ds
        ON ds.detalle_venta_id = d.detalle_venta_id
    WHERE ds.servicio_id = p_servicio_id
      AND (
            (p_comanda_id IS NULL AND d.comanda_id IS NULL AND d.factura_venta_id IS NULL)
         OR (p_comanda_id IS NOT NULL AND d.comanda_id = p_comanda_id)
      )
    LIMIT 1;

    -- ¿Existe ya?
    IF EXISTS (
        SELECT 1
        FROM detalle_facturas_ventas d
        INNER JOIN detalle_ventas_con_servicios ds
            ON ds.detalle_venta_id = d.detalle_venta_id
        WHERE ds.servicio_id = p_servicio_id
          AND (
                (p_comanda_id IS NULL AND d.comanda_id IS NULL AND d.factura_venta_id IS NULL)
             OR (p_comanda_id IS NOT NULL AND d.comanda_id = p_comanda_id)
          )
    ) THEN

        -- Obtener detalle
        SELECT d.detalle_venta_id
        INTO v_detalle_id
        FROM detalle_facturas_ventas d
        INNER JOIN detalle_ventas_con_servicios ds
            ON ds.detalle_venta_id = d.detalle_venta_id
        WHERE ds.servicio_id = p_servicio_id
          AND (
                (p_comanda_id IS NULL AND d.comanda_id IS NULL AND d.factura_venta_id IS NULL)
             OR (p_comanda_id IS NOT NULL AND d.comanda_id = p_comanda_id)
          )
        LIMIT 1;

        -- Actualizar cantidad
        UPDATE detalle_facturas_ventas
        SET cantidad = v_cantidad_existente + p_cantidad
        WHERE detalle_venta_id = v_detalle_id;

        SELECT 'Detalle de servicio incrementado' AS msg;

    ELSE

        -- Insertar nuevo detalle
        INSERT INTO detalle_facturas_ventas (
            comanda_id,
            usuario_id,
            cantidad,
            costo,
            precio,
            fecha
        )
        VALUES (
            p_comanda_id,
            p_usuario_id,
            p_cantidad,
            p_costo,
            p_precio,
            CURDATE()
        );

        SET v_detalle_id = LAST_INSERT_ID();

        INSERT INTO detalle_ventas_con_servicios (
            detalle_venta_id,
            servicio_id,
            comanda_id
        )
        VALUES (
            v_detalle_id,
            p_servicio_id,
            p_comanda_id
        );

        SELECT 'Nuevo detalle de servicio creado' AS msg;

    END IF;
END$$
DELIMITER ;


DROP PROCEDURE IF EXISTS pos_agregar_producto;
DELIMITER $$

CREATE PROCEDURE pos_agregar_producto(
    IN p_comanda_id INT,          -- NULL = venta rápida
    IN p_usuario_id INT,
    IN p_cantidad DECIMAL(10,2),
    IN p_costo DECIMAL(10,2),
    IN p_precio DECIMAL(10,2),
    IN p_producto_id INT
)
BEGIN
    DECLARE v_detalle_id INT;
    DECLARE v_cantidad_existente DECIMAL(10,2) DEFAULT 0;
    DECLARE v_stock_disponible DECIMAL(10,2);
    DECLARE v_comanda_exists INT DEFAULT 1;

    /* =========================
       Validar comanda (si aplica)
    ========================== */
    IF p_comanda_id IS NOT NULL THEN
        SELECT COUNT(*) INTO v_comanda_exists
        FROM comandas
        WHERE comanda_id = p_comanda_id;

        IF v_comanda_exists = 0 THEN
            SELECT 'Error: No existe la comanda seleccionada' AS msg;
        END IF;
    END IF;

    /* =========================
       Obtener stock disponible
    ========================== */
    SELECT cantidad INTO v_stock_disponible
    FROM productos
    WHERE producto_id = p_producto_id
    LIMIT 1;

    IF p_cantidad > v_stock_disponible THEN
        SELECT 'Error: No hay suficiente stock disponible' AS msg;
    END IF;

    /* =========================
       Obtener detalle existente
    ========================== */
    SELECT d.detalle_venta_id, d.cantidad
    INTO v_detalle_id, v_cantidad_existente
    FROM detalle_facturas_ventas d
    INNER JOIN detalle_ventas_con_productos dp
        ON dp.detalle_venta_id = d.detalle_venta_id
    WHERE dp.producto_id = p_producto_id
      AND (
            (p_comanda_id IS NULL AND d.comanda_id IS NULL AND d.factura_venta_id IS NULL AND d.usuario_id = p_usuario_id)
         OR (p_comanda_id IS NOT NULL AND d.comanda_id = p_comanda_id)
      )
    LIMIT 1;

    /* =========================
       Si existe → actualizar
    ========================== */
    IF v_detalle_id IS NOT NULL THEN

        UPDATE detalle_facturas_ventas
        SET cantidad = v_cantidad_existente + p_cantidad
        WHERE detalle_venta_id = v_detalle_id;

        SELECT 'Detalle incrementado' AS msg;

    /* =========================
       Si no existe → insertar
    ========================== */
    ELSE

        INSERT INTO detalle_facturas_ventas (
            comanda_id,
            usuario_id,
            cantidad,
            costo,
            precio,
            fecha
        ) VALUES (
            p_comanda_id,
            p_usuario_id,
            p_cantidad,
            p_costo,
            p_precio,
            CURDATE()
        );

        SET v_detalle_id = LAST_INSERT_ID();

        INSERT INTO detalle_ventas_con_productos (
            detalle_venta_id,
            producto_id,
            comanda_id
        ) VALUES (
            v_detalle_id,
            p_producto_id,
            p_comanda_id
        );

        SELECT 'Nuevo detalle creado' AS msg;

    END IF;

END$$
DELIMITER ;


DROP PROCEDURE IF EXISTS `pos_update_detalle`;
DELIMITER $$
CREATE PROCEDURE pos_update_detalle (
    IN _producto_id INT,
    IN _pieza_id INT,
    IN _servicio_id INT,
    IN _detalle_id INT,
    IN _usuario_id INT,
    IN _descuento DECIMAL(10,2),
    IN _impuesto DECIMAL(10,2),
    IN _precio DECIMAL(10,2),
    IN _cantidad DECIMAL(10,2)
)
BEGIN
    DECLARE stock_disponible DECIMAL(10,2) DEFAULT 0;  -- Establecer valor predeterminado de stock_disponible
    DECLARE stock_detalle DECIMAL(10,2) DEFAULT 0;     -- Establecer valor predeterminado de stock_detalle
    DECLARE total_stock DECIMAL(10,2) DEFAULT 0;       -- Establecer valor predeterminado de total_stock
    DECLARE exit_message VARCHAR(255);

    -- Manejadores de excepciones
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SET exit_message = 'Error: SQLException encountered';
            SELECT exit_message AS msg;
        END;

    DECLARE EXIT HANDLER FOR SQLSTATE '23000'
        BEGIN
            SET exit_message = 'Error: SQLSTATE 23000: Integrity constraint violation';
            SELECT exit_message AS msg;
        END;

    -- Si es un servicio, no verificar stock
    IF _servicio_id > 0 THEN
        -- Si es un servicio, simplemente continuar sin verificar stock
        SET stock_disponible = NULL;
    ELSE
        -- Verificar el stock disponible para el producto o pieza
        IF _producto_id > 0 THEN
            -- Si es un producto, verificar stock en la tabla de productos
            SELECT cantidad INTO stock_disponible
            FROM productos
            WHERE producto_id = _producto_id;
        ELSEIF _pieza_id > 0 THEN
            -- Si es una pieza, verificar stock en la tabla de piezas
            SELECT cantidad INTO stock_disponible
            FROM piezas
            WHERE pieza_id = _pieza_id;
        END IF;
    END IF;

    -- Verificar si se encontró stock disponible para productos o piezas
    IF stock_disponible IS NULL AND _servicio_id = 0 THEN
        SET exit_message = 'Error: Producto o pieza no encontrado';
        SELECT exit_message AS msg;
    END IF;

    -- Obtener la cantidad actual en el detalle de la factura
    SELECT cantidad INTO stock_detalle
    FROM detalle_facturas_ventas
    WHERE detalle_venta_id = _detalle_id;

    -- Verificar si se ha encontrado un detalle en la factura
    IF stock_detalle IS NULL THEN
        SET stock_detalle = 0;  -- Si no se encuentra el detalle, asignamos 0
    END IF;

    -- Calcular el stock total disponible (stock actual + cantidad en detalle)
    -- Si stock_disponible es NULL, lo asignamos a 0, ya que el producto podría no estar en stock
    IF stock_disponible IS NULL THEN
        SET stock_disponible = 0;
    END IF;

    SET total_stock = stock_disponible + stock_detalle;

    -- Depuración: Verificar el valor de total_stock antes de la comparación
    SELECT total_stock AS total_stock_available;

    -- Verificar si el stock total disponible es suficiente (solo si no es un servicio)
    IF (_servicio_id = 0 AND total_stock >= _cantidad) OR _servicio_id > 0 THEN
        -- Actualizar los detalles de la factura si hay suficiente stock o si es un servicio
        UPDATE detalle_facturas_ventas
        SET descuento = _descuento, impuesto = _impuesto, precio = _precio, cantidad = _cantidad
        WHERE detalle_venta_id = _detalle_id;

        SELECT 'Datos actualizados correctamente' AS msg;
    ELSE
        -- Si no hay suficiente stock o si no es un servicio
        SELECT 'Error: No hay suficiente stock disponible' AS msg;
    END IF;

END $$
DELIMITER ;


DROP PROCEDURE IF EXISTS `pos_eliminar_todo`;
DELIMITER $$
CREATE PROCEDURE `pos_eliminar_todo` (IN _comanda_id INT, IN _usuario_id INT)
BEGIN

     DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
     DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

    -- Si _comanda_id es mayor a 0, eliminar registros específicos
    IF _comanda_id > 0 THEN
        DELETE FROM detalle_facturas_ventas WHERE comanda_id = _comanda_id;
        SELECT 'Registros eliminados por comanda_id' as msg;
    ELSE 
        -- Si _comanda_id es 0 o menor, eliminar registros donde usuario_id coincida y otros campos sean nulos
        DELETE FROM detalle_facturas_ventas 
        WHERE usuario_id = _usuario_id 
        AND comanda_id IS NULL 
        AND factura_venta_id IS NULL;
        
        SELECT 'Registros eliminados por usuario_id' as msg;
    END IF;

END $$
DELIMITER ;


DROP PROCEDURE IF EXISTS `pos_eliminar_todo`;
DELIMITER $$
CREATE PROCEDURE `pos_eliminar_todo` (IN _comanda_id INT, IN _usuario_id INT)
BEGIN

     DECLARE EXIT HANDLER FOR SQLEXCEPTION SELECT 'SQLException encountered' AS msg;
     DECLARE EXIT HANDLER FOR SQLSTATE '23000' SELECT 'SQLSTATE 23000' AS msg;

    -- Si _comanda_id es mayor a 0, eliminar registros específicos
    IF _comanda_id > 0 THEN
        DELETE FROM detalle_facturas_ventas WHERE comanda_id = _comanda_id;
        SELECT 'Registros eliminados por comanda_id' as msg;
    ELSE 
        -- Si _comanda_id es 0 o menor, eliminar registros donde usuario_id coincida y otros campos sean nulos
        DELETE FROM detalle_facturas_ventas 
        WHERE usuario_id = _usuario_id 
        AND comanda_id IS NULL 
        AND factura_venta_id IS NULL;
        
        SELECT 'Registros eliminados por usuario_id' as msg;
    END IF;

END $$
DELIMITER ;


