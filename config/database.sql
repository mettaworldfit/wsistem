CREATE DATABASE 

IF NOT EXISTS proyecto CHARACTER SET utf8 COLLATE utf8_general_ci;

USE proyecto;

CREATE TABLE configuraciones (
    config_id INT AUTO_INCREMENT NOT NULL,
    config_key VARCHAR(100) NOT NULL,   -- Clave de configuración
    config_value TEXT NOT NULL,         -- Valor asociado a la clave
    descripcion VARCHAR(255) NULL,      -- Descripción opcional para cada configuración
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- Fecha de creación
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,  -- Fecha de actualización
    PRIMARY KEY (config_id)
    
) ENGINE = InnoDB;


CREATE TABLE roles (

rol_id int auto_increment NOT NULL,
nombre_rol varchar(20) NOT NULL,

PRIMARY KEY (rol_id)

)ENGINE = InnoDb; 


CREATE TABLE estados_generales (

estado_id int auto_increment NOT NULL,
nombre_estado varchar(20) NOT NULL,

PRIMARY KEY (estado_id)

)ENGINE = InnoDb; 


CREATE TABLE usuarios (

usuario_id int auto_increment NOT NULL,
rol_id int NOT NULL,
estado_id int NOT NULL,
nombre varchar(50) NOT NULL,
apellidos varchar(50) NOT NULL,
username varchar(20) unique NOT NULL,
password varchar(20) NOT NULL,
fecha date NULL,

PRIMARY KEY (usuario_id),
CONSTRAINT usuarios_estados_generales FOREIGN KEY (estado_id) REFERENCES estados_generales(estado_id) ON UPDATE CASCADE,
CONSTRAINT usuarios_roles FOREIGN KEY (rol_id) REFERENCES roles(rol_id)

)ENGINE = InnoDb; 


CREATE TABLE clientes (

cliente_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
cedula varchar(11) unique NULL, 
nombre varchar(50) NOT NULL,
apellidos varchar(50) NULL,
telefono1 varchar(15) NULL,
telefono2 varchar(15) NULL,
email varchar(35) NULL,
direccion varchar(60) NULL,
fecha date NULL,

PRIMARY KEY (cliente_id),
CONSTRAINT clientes_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id) ON UPDATE CASCADE

)ENGINE = InnoDb; 


CREATE TABLE proveedores (

proveedor_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
nombre_proveedor varchar(50) NOT NULL,
apellidos varchar(50) NULL,
telefono1 varchar(15) NULL,
telefono2 varchar(15) NULL,
email varchar(35) NULL,
direccion varchar(60) NULL,
fecha date NULL,

PRIMARY KEY (proveedor_id),
CONSTRAINT proveedores_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id) ON UPDATE CASCADE

)ENGINE = InnoDb; 


CREATE TABLE marcas (

marca_id int auto_increment NOT NULL,
nombre_marca varchar(20) unique NOT NULL,
fecha date NULL,

PRIMARY KEY (marca_id)

)ENGINE = InnoDb; 


CREATE TABLE ofertas (

oferta_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
nombre_oferta varchar(25) NOT NULL,
valor int NOT NULL,
descripcion varchar(100) NULL,
fecha date NULL,

PRIMARY KEY (oferta_id),
CONSTRAINT ofertas_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id)

)ENGINE = InnoDb; 


CREATE TABLE impuestos (

impuesto_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
nombre_impuesto varchar(20) NOT NULL,
valor int NOT NULL,
descripcion varchar(100) NULL,
fecha date NULL,

PRIMARY KEY (impuesto_id),
CONSTRAINT impuestos_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id)

)ENGINE = InnoDb; 


CREATE TABLE categorias (

categoria_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
nombre_categoria varchar(50) unique NOT NULL,
descripcion varchar(150) NULL,
fecha date NULL,

PRIMARY KEY (categoria_id),
CONSTRAINT categoriass_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id)

)ENGINE = InnoDb; 


CREATE TABLE posiciones (

posicion_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
referencia varchar(50) unique NOT NULL,
descripcion varchar(60) NULL,
fecha date NULL,

PRIMARY KEY (posicion_id),
CONSTRAINT posiciones_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id)

)ENGINE = InnoDb; 


CREATE TABLE almacenes (

almacen_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
nombre_almacen varchar(30) unique NOT NULL,
descripcion varchar(80) NULL,
fecha date NULL,

PRIMARY KEY (almacen_id),
CONSTRAINT almacenes_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id)

)ENGINE = InnoDb; 


CREATE TABLE lista_de_precios (

lista_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
nombre_lista varchar(30) unique NOT NULL,
descripcion varchar(60) NULL,
fecha date NULL,

PRIMARY KEY (lista_id),
CONSTRAINT lista_de_precios_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id)

)ENGINE = InnoDb; 


CREATE TABLE piezas (

pieza_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
estado_id int NOT NULL,
almacen_id int NOT NULL,
cod_pieza varchar(20) unique NULL,
nombre_pieza varchar(50) unique NOT NULL,
precio_costo DECIMAL(10,2) NULL,
precio_unitario DECIMAL(10,2) NOT NULL,
cantidad int NOT NULL,
cantidad_min int NOT NULL,
imagen varchar(50) NULL,
fecha date NULL,

PRIMARY KEY (pieza_id),
CONSTRAINT piezas_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id),
CONSTRAINT piezas_estados_generales FOREIGN KEY (estado_id) REFERENCES estados_generales(estado_id),
CONSTRAINT piezas_almacenes FOREIGN KEY (almacen_id) REFERENCES almacenes(almacen_id)

)ENGINE = InnoDb; 


CREATE TABLE piezas_con_posiciones (

pieza_id int NOT NULL,
posicion_id int NOT NULL,

PRIMARY KEY (pieza_id,posicion_id),
CONSTRAINT piezas_y_piezas_con_posiciones FOREIGN KEY (pieza_id) REFERENCES piezas(pieza_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT posiciones_y_piezas_con_posiciones FOREIGN KEY (posicion_id) REFERENCES posiciones(posicion_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb;


CREATE TABLE piezas_con_categorias (

pieza_id int NOT NULL,
categoria_id int NOT NULL,

PRIMARY KEY (pieza_id,categoria_id),
CONSTRAINT piezas_y_piezas_con_categorias FOREIGN KEY (pieza_id) REFERENCES piezas(pieza_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT categorias_y_piezas_con_categorias FOREIGN KEY (categoria_id) REFERENCES categorias(categoria_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb;


CREATE TABLE piezas_con_ofertas (

pieza_id int NOT NULL,
oferta_id int NOT NULL,

PRIMARY KEY (pieza_id,oferta_id),
CONSTRAINT piezas_y_piezas_con_ofertas FOREIGN KEY (pieza_id) REFERENCES piezas(pieza_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT ofertas_y_piezas_con_ofertas FOREIGN KEY (oferta_id) REFERENCES ofertas(oferta_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb;


CREATE TABLE piezas_con_marcas (

pieza_id int NOT NULL,
marca_id int NOT NULL,

PRIMARY KEY (pieza_id,marca_id),
CONSTRAINT piezas_y_piezas_con_marcas FOREIGN KEY (pieza_id) REFERENCES piezas(pieza_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT marcas_y_piezas_con_marcas FOREIGN KEY (marca_id) REFERENCES marcas(marca_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb;


CREATE TABLE piezas_con_lista_de_precios (

pieza_lista_id int auto_increment NOT NULL,
pieza_id int NOT NULL,
lista_id int NOT NULL,
valor int NOT NULL,
fecha date NOT NULL,

PRIMARY KEY (pieza_lista_id),
CONSTRAINT piezas_y_piezas_con_lista_de_precios FOREIGN KEY (pieza_id) REFERENCES piezas(pieza_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT lista_de_precios_y_piezas_con_lista_de_precios FOREIGN KEY (lista_id) REFERENCES lista_de_precios(lista_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb;


CREATE TABLE piezas_con_proveedores (

pieza_id int NOT NULL,
proveedor_id int NOT NULL,

PRIMARY KEY (pieza_id,proveedor_id),
CONSTRAINT piezas_y_piezas_con_proveedores FOREIGN KEY (pieza_id) REFERENCES piezas(pieza_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT proveedores_y_piezas_con_proveedores FOREIGN KEY (proveedor_id) REFERENCES proveedores(proveedor_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb;


CREATE TABLE servicios(
servicio_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
nombre_servicio varchar(70) NOT NULL unique,
costo DECIMAL(10,2) NULL,
precio int NULL,
fecha date NULL,

PRIMARY KEY (servicio_id),
CONSTRAINT servicios_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id)
)ENGINE = InnoDb;


CREATE TABLE productos (

producto_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
estado_id int NOT NULL,
almacen_id int NOT NULL,
cod_producto varchar(20) unique NULL,
nombre_producto varchar(100) NOT NULL,
precio_costo DECIMAL(10,2) NULL,
precio_unitario DECIMAL(10,2) NOT NULL,
cantidad DECIMAL(10,2) NOT NULL,
cantidad_min int NULL DEFAULT 1,
imagen varchar(50) NULL,
fecha date NULL,

PRIMARY KEY (producto_id),
CONSTRAINT productos_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id),
CONSTRAINT productos_estados_generales FOREIGN KEY (estado_id) REFERENCES estados_generales(estado_id),
CONSTRAINT productos_almacenes FOREIGN KEY (almacen_id) REFERENCES almacenes(almacen_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb; 


CREATE TABLE variantes (

variante_id int auto_increment NOT NULL,
producto_id int NOT NULL,
estado_id int NOT NULL,
tipo ENUM('dispositivo','producto') NOT NULL,
sabor varchar(45) NULL,
serial varchar(20) NULL unique,
costo_unitario decimal(19,2) null,
caja varchar(2) null,
fecha date NULL,

PRIMARY KEY (variante_id),
CONSTRAINT variantes_estados FOREIGN KEY (estado_id) REFERENCES estados_generales(estado_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT variantes_productos FOREIGN KEY (producto_id) REFERENCES productos(producto_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb; 


CREATE TABLE colores (

color_id int auto_increment NOT NULL,
color varchar(20) NOT NULL,

PRIMARY KEY (color_id)

)ENGINE = InnoDb; 


CREATE TABLE variantes_con_colores (

variante_id int NOT NULL,
color_id int NOT NULL,

PRIMARY KEY (variante_id,color_id),
CONSTRAINT  variantes_con_colores_variantes FOREIGN KEY (variante_id) REFERENCES variantes(variante_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT  variantes_con_colores_colores FOREIGN KEY (color_id) REFERENCES colores(color_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb; 


CREATE TABLE variantes_con_proveedores (

variante_id int NOT NULL,
proveedor_id int NOT NULL,

PRIMARY KEY (variante_id,proveedor_id),
CONSTRAINT variantes_con_proveedores_variantes FOREIGN KEY (variante_id) REFERENCES variantes(variante_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT variantes_con_proveedores_proveedores FOREIGN KEY (proveedor_id) REFERENCES proveedores(proveedor_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb; 


CREATE TABLE productos_con_categorias (

producto_id int NOT NULL,
categoria_id int NOT NULL,

PRIMARY KEY (producto_id,categoria_id),
CONSTRAINT productos_y_productos_con_categorias FOREIGN KEY (producto_id) REFERENCES productos(producto_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT categorias_y_productos_con_categorias FOREIGN KEY (categoria_id) REFERENCES categorias(categoria_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb;


CREATE TABLE productos_con_impuestos (

producto_id int NOT NULL,
impuesto_id int NOT NULL,

PRIMARY KEY (producto_id,impuesto_id),
CONSTRAINT productos_y_productos_con_impuestos FOREIGN KEY (producto_id) REFERENCES productos(producto_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT impuestos_y_productos_con_impuestos FOREIGN KEY (impuesto_id) REFERENCES impuestos(impuesto_id) 

)ENGINE = InnoDb;


CREATE TABLE productos_con_posiciones (

producto_id int NOT NULL,
posicion_id int NOT NULL,

PRIMARY KEY (producto_id,posicion_id),
CONSTRAINT productos_y_productos_con_posiciones FOREIGN KEY (producto_id) REFERENCES productos(producto_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT posiciones_y_productos_con_posiciones FOREIGN KEY (posicion_id) REFERENCES posiciones(posicion_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb;


CREATE TABLE productos_con_ofertas (

producto_id int NOT NULL,
oferta_id int NOT NULL,

PRIMARY KEY (producto_id,oferta_id),
CONSTRAINT productos_y_productos_con_ofertas FOREIGN KEY (producto_id) REFERENCES productos(producto_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT ofertas_y_productos_con_ofertas FOREIGN KEY (oferta_id) REFERENCES ofertas(oferta_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb;


CREATE TABLE productos_con_marcas (

producto_id int NOT NULL,
marca_id int NOT NULL,

PRIMARY KEY (producto_id,marca_id),
CONSTRAINT productos_y_productos_con_marcas FOREIGN KEY (producto_id) REFERENCES productos(producto_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT marcas_y_productos_con_marcas FOREIGN KEY (marca_id) REFERENCES marcas(marca_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb;


CREATE TABLE productos_con_lista_de_precios (

producto_lista_id int auto_increment NOT NULL,
producto_id int NOT NULL,
lista_id int NOT NULL,
valor int NOT NULL,
fecha date NOT NULL,

PRIMARY KEY (producto_lista_id),
CONSTRAINT productos_y_productos_con_lista_de_precios FOREIGN KEY (producto_id) REFERENCES productos(producto_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT lista_de_precios_y_productos_con__lista_de_precios FOREIGN KEY (lista_id) REFERENCES lista_de_precios(lista_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb;


CREATE TABLE productos_con_proveedores (

producto_id int NOT NULL,
proveedor_id int NOT NULL,

PRIMARY KEY (producto_id,proveedor_id),
CONSTRAINT productos_y_productos_con_proveedores FOREIGN KEY (producto_id) REFERENCES productos(producto_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT proveedores_y_productos_con_proveedores FOREIGN KEY (proveedor_id) REFERENCES proveedores(proveedor_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb;


CREATE TABLE metodos_de_pagos (

metodo_pago_id int auto_increment NOT NULL,
nombre_metodo varchar(30) NOT NULL,

PRIMARY KEY (metodo_pago_id)

)ENGINE = InnoDb;


CREATE TABLE facturas_ventas (

factura_venta_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
cliente_id int NOT NULL,
estado_id int NOT NULL,
metodo_pago_id int NOT NULL,
total DECIMAL(10,2) NULL,
recibido DECIMAL(10,2) NULL,
pendiente DECIMAL(10,2) NULL,
bono DECIMAL(10,2) NULL,
descripcion varchar(150) NULL,
fecha date NOT NULL,
hora TIME DEFAULT (CURRENT_TIMESTAMP),

PRIMARY KEY (factura_venta_id),
CONSTRAINT facturas_ventas_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id),
CONSTRAINT facturas_ventas_clientes FOREIGN KEY (cliente_id) REFERENCES clientes(cliente_id),
CONSTRAINT facturas_ventas_metodos_de_pagos FOREIGN KEY (metodo_pago_id) REFERENCES metodos_de_pagos(metodo_pago_id),
CONSTRAINT facturas_ventas_estados_generales FOREIGN KEY (estado_id) REFERENCES estados_generales(estado_id)

)ENGINE = InnoDb; 


CREATE TABLE comandas (
comanda_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
cliente_id INT NOT NULL,
usuario_id INT NOT NULL,
estado_id INT NOT NULL,
observacion TEXT,
tipo_entrega ENUM('envio','retiro','-') DEFAULT '-' NULL,
direccion_entrega TEXT NULL,
nombre_receptor VARCHAR(100),
telefono_receptor VARCHAR(20) NULL,
fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  
CONSTRAINT comandas_clientes FOREIGN KEY (cliente_id) REFERENCES clientes(cliente_id) ON UPDATE CASCADE,
CONSTRAINT comandas_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id) ON UPDATE CASCADE,
CONSTRAINT comandas_estados_generales FOREIGN KEY (estado_id) REFERENCES estados_generales(estado_id) ON UPDATE CASCADE

)ENGINE = InnoDb;


 CREATE TABLE detalle_facturas_ventas (

detalle_venta_id int auto_increment NOT NULL,
factura_venta_id int NULL,
comanda_id int NULL,
usuario_id int NOT NULL,
cantidad DECIMAL(10,2) NOT NULL,
costo DECIMAL(10,2) NOT NULL DEFAULT 0.00,
precio DECIMAL(10,2) NOT NULL,
impuesto DECIMAL(10,2) NULL,
descuento DECIMAL(10,2) NULL,
fecha date NOT NULL,
hora TIME DEFAULT (CURRENT_TIMESTAMP),

PRIMARY KEY (detalle_venta_id),
CONSTRAINT detalle_facturas_ventas_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id),
CONSTRAINT detalle_facturas_ventas_facturas_ventas FOREIGN KEY (factura_venta_id) REFERENCES facturas_ventas(factura_venta_id),
CONSTRAINT detalle_facturas_ventas_comandas FOREIGN KEY (comanda_id) REFERENCES comandas(comanda_id) ON UPDATE CASCADE ON DELETE CASCADE

)ENGINE = InnoDb;


CREATE TABLE variantes_facturadas (

variante_id int NOT NULL,
detalle_venta_id int NOT NULL,

PRIMARY KEY (variante_id,detalle_venta_id),
CONSTRAINT variantes_variantes_facturadas FOREIGN KEY (variante_id) REFERENCES variantes(variante_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT detalle_ventas_variantes_facturadas FOREIGN KEY (detalle_venta_id) REFERENCES detalle_facturas_ventas(detalle_venta_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb;


CREATE TABLE detalle_ventas_con_productos (

detalle_venta_id int NOT NULL,
producto_id int NOT NULL,
factura_venta_id int NULL, 
comanda_id int NULL, 

PRIMARY KEY (detalle_venta_id,producto_id),
CONSTRAINT detalle_ventas_con_productos FOREIGN KEY (producto_id) REFERENCES productos(producto_id),
CONSTRAINT detalle_ventas_con_productos_factura FOREIGN KEY (factura_venta_id) REFERENCES facturas_ventas(factura_venta_id),
CONSTRAINT detalle_ventas_con_productos_comandas FOREIGN KEY (comanda_id) REFERENCES comandas(comanda_id), 
CONSTRAINT detalle_ventas_con_productos_detalle FOREIGN KEY (detalle_venta_id) REFERENCES detalle_facturas_ventas(detalle_venta_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb;


CREATE TABLE detalle_ventas_con_piezas_ (

detalle_venta_id int NOT NULL,
pieza_id int NOT NULL,
factura_venta_id int NULL,
comanda_id int NULL,

PRIMARY KEY (detalle_venta_id,pieza_id),
CONSTRAINT detalle_ventas_con_piezas_factura FOREIGN KEY (factura_venta_id) REFERENCES facturas_ventas(factura_venta_id),
CONSTRAINT dvcp_piezas FOREIGN KEY (pieza_id) REFERENCES piezas(pieza_id),
CONSTRAINT detalle_ventas_con_piezas_comandas FOREIGN KEY (comanda_id) REFERENCES comandas(comanda_id),
CONSTRAINT detalle_ventas_con_piezas_detalle FOREIGN KEY (detalle_venta_id) REFERENCES detalle_facturas_ventas(detalle_venta_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb;


CREATE TABLE detalle_ventas_con_servicios (

detalle_venta_id int NOT NULL,
servicio_id int NOT NULL,
factura_venta_id int NULL,
comanda_id int NULL,

PRIMARY KEY (detalle_venta_id,servicio_id),
CONSTRAINT detalle_ventas_con_servicios FOREIGN KEY (servicio_id) REFERENCES servicios(servicio_id),
CONSTRAINT detalle_ventas_con_servicios_factura FOREIGN KEY (factura_venta_id) REFERENCES facturas_ventas(factura_venta_id),
CONSTRAINT detalle_ventas_con_servicios_servicios FOREIGN KEY (comanda_id) REFERENCES comandas(comanda_id),
CONSTRAINT detalle_ventas_con_servicios_detalle FOREIGN KEY (detalle_venta_id) REFERENCES detalle_facturas_ventas(detalle_venta_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb;


CREATE TABLE detalle_temporal (

detalle_temporal_id int auto_increment UNIQUE NOT NULL,
usuario_id int NOT NULL,
producto_id int NULL,
pieza_id int NULL,
servicio_id int NULL,
descripcion varchar(100) NOT NULL,
cantidad DECIMAL(10,2) NOT NULL,
costo DECIMAL(10,2) NOT NULL DEFAULT 0.00,
precio DECIMAL(10,2) NOT NULL,
impuesto DECIMAL(10,2) NULL,
descuento DECIMAL(10,2) NULL,
hora time NULL,
fecha date NULL,

PRIMARY KEY (detalle_temporal_id),
CONSTRAINT detalle_temporal_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id)

)ENGINE = InnoDb; 


CREATE TABLE detalle_variantes_temporal (


detalle_temporal_id int NOT NULL,
variante_id int NOT NULL,

PRIMARY KEY (variante_id),
CONSTRAINT variantes_detalle_variantes_temporal FOREIGN KEY (variante_id) REFERENCES variantes(variante_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb;


CREATE TABLE bono_config (

bono_config_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
estado_id int NOT NULL,
min_factura int NOT NULL,
valor int NOT NULL,
fecha date NULL,

PRIMARY KEY (bono_config_id),
CONSTRAINT bono_config_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id),
CONSTRAINT bono_config_estados_generales FOREIGN KEY (estado_id) REFERENCES estados_generales(estado_id) 

)ENGINE = InnoDb; 


CREATE TABLE bonos (

bono_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
cliente_id int NOT NULL,
valor int NOT NULL,
fecha date NULL,

PRIMARY KEY (bono_id),
CONSTRAINT bonos_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id),
CONSTRAINT bonos_clientes FOREIGN KEY (cliente_id) REFERENCES clientes(cliente_id) 

)ENGINE = InnoDb; 


CREATE TABLE condiciones(

condicion_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
sintoma varchar(50) NOT NULL,
fecha date NULL,

PRIMARY KEY (condicion_id),
CONSTRAINT condiciones_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id)

)ENGINE = InnoDb;


CREATE TABLE equipos (

equipo_id int auto_increment NOT NULL,
marca_id int NOT NULL,
nombre_modelo varchar(50) NOT NULL,
modelo varchar(20) NULL,

PRIMARY KEY (equipo_id),
CONSTRAINT equipos_marcas FOREIGN KEY (marca_id) REFERENCES marcas(marca_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb; 


CREATE TABLE ordenes_rp(

orden_rp_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
cliente_id int NOT NULL,
estado_id int NOT NULL,
equipo_id int NOT NULL,
serie varchar(50) NULL,
imei int NULL,
observacion varchar(150) NULL,
fecha_entrada date NOT NULL,
fecha_salida date NULL,

PRIMARY KEY (orden_rp_id),
CONSTRAINT ordenes_rp_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id),
CONSTRAINT ordenes_rp_equipos FOREIGN KEY (equipo_id) REFERENCES equipos(equipo_id),
CONSTRAINT ordenes_rp_clientes FOREIGN KEY (cliente_id) REFERENCES clientes(cliente_id),
CONSTRAINT ordenes_rp_estados_generales FOREIGN KEY (estado_id) REFERENCES estados_generales(estado_id)

)ENGINE = InnoDb; 


CREATE TABLE ordenes_rp_con_condiciones(

condicion_id int NOT NULL,
orden_rp_id int NOT NULL,

PRIMARY KEY (condicion_id,orden_rp_id),
CONSTRAINT condiciones_condiciones FOREIGN KEY (condicion_id) REFERENCES condiciones(condicion_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb;


CREATE TABLE detalle_ordenRP(
detalle_ordenRP_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
orden_rp_id int NOT NULL,
descripcion varchar(50) NOT NULL,
cantidad DECIMAL(10,2) NULL,
costo DECIMAL(10,2) NOT NULL DEFAULT '0.00',
precio DECIMAL(10,2) NULL,
descuento int NULL,
fecha date NOT NULL,
hora TIME DEFAULT (CURRENT_TIMESTAMP),

PRIMARY KEY (detalle_ordenRP_id),
CONSTRAINT detalle_ordenRP_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id),
CONSTRAINT detalle_ordenRP_ordenes_rp FOREIGN KEY (orden_rp_id) REFERENCES ordenes_rp(orden_rp_id) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE = InnoDb; 



CREATE TABLE detalle_ordenRP_con_piezas(

detalle_ordenRP_id int NOT NULL,
pieza_id int NOT NULL,

PRIMARY KEY (detalle_ordenRP_id,pieza_id),
CONSTRAINT detalle_ordenRP_con_piezas_piezas FOREIGN KEY (pieza_id) REFERENCES piezas(pieza_id) ON UPDATE CASCADE,
CONSTRAINT detalle_ordenRP_con_piezas_detalle_factura_rp FOREIGN KEY (detalle_ordenRP_id) REFERENCES detalle_ordenRP(detalle_ordenRP_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb; 


CREATE TABLE detalle_ordenRP_con_servicios(

detalle_ordenRP_id int NOT NULL,
servicio_id int NOT NULL,

PRIMARY KEY (detalle_ordenRP_id,servicio_id),
CONSTRAINT detalle_ordenRP_con_servicios_servicios FOREIGN KEY (servicio_id) REFERENCES servicios(servicio_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT detalle_ordenRP_con_servicios_detalle_factura_rp FOREIGN KEY (detalle_ordenRP_id) REFERENCES detalle_ordenRP(detalle_ordenRP_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb; 


CREATE TABLE facturasRP (

facturaRP_id int auto_increment NOT NULL,
orden_rp_id int NOT NULL,
usuario_id int NOT NULL,
cliente_id int NOT NULL,
metodo_pago_id int NOT NULL,
estado_id int NOT NULL,
total DECIMAL(10,2) NULL,
recibido DECIMAL(10,2) NULL,
pendiente DECIMAL(10,2) NULL,
descripcion varchar(150) NULL,
fecha date NOT NULL,
hora TIME DEFAULT (CURRENT_TIMESTAMP),

PRIMARY KEY (facturaRP_id),
CONSTRAINT facturasRP_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id),
CONSTRAINT facturasRP_clientes FOREIGN KEY (cliente_id) REFERENCES clientes(cliente_id),
CONSTRAINT facturasRP_metodos_de_pagos FOREIGN KEY (metodo_pago_id) REFERENCES metodos_de_pagos(metodo_pago_id),
CONSTRAINT facturasRP_estados_generales FOREIGN KEY (estado_id) REFERENCES estados_generales(estado_id),
CONSTRAINT facturasRP_ordenes_rp FOREIGN KEY (orden_rp_id) REFERENCES ordenes_rp(orden_rp_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb; 


CREATE TABLE ordenes_compras (

orden_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
proveedor_id int NOT NULL,
estado_id int NOT NULL,
observacion varchar(150) NULL,
origen ENUM('caja','fuera_caja') NOT NULL DEFAULT 'caja',
fecha date NOT NULL,
expiracion date NULL,

PRIMARY KEY (orden_id),
CONSTRAINT ordenes_compras_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id),
CONSTRAINT ordenes_compras_proveedores FOREIGN KEY (proveedor_id) REFERENCES proveedores(proveedor_id),
CONSTRAINT ordenes_compras_estados_generales FOREIGN KEY (estado_id) REFERENCES estados_generales(estado_id)

)ENGINE = InnoDb; 


CREATE TABLE detalle_compra (

detalle_compra_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
orden_id int NOT NULL,
cantidad DECIMAL(10,2) NOT NULL,
precio DECIMAL(10,2) NOT NULL,
impuestos DECIMAL(10,2) NULL,
descuentos DECIMAL(10,2) NULL,
observacion varchar(50) NULL,
fecha date NOT NULL,

PRIMARY KEY (detalle_compra_id),
CONSTRAINT detalle_compra_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id),
CONSTRAINT detalle_compra_ordenes_compras FOREIGN KEY (orden_id) REFERENCES ordenes_compras(orden_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb; 


CREATE TABLE detalle_compra_con_piezas (

detalle_compra_id int NOT NULL,
pieza_id int NOT NULL,
orden_id int NOT NULL,

PRIMARY KEY (pieza_id,orden_id),
CONSTRAINT detalle_compra_con_piezas_piezas FOREIGN KEY (pieza_id) REFERENCES piezas(pieza_id),
CONSTRAINT detalle_compra_con_piezas_ordenes_compras FOREIGN KEY (orden_id) REFERENCES ordenes_compras(orden_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT detalle_compra_con_piezas_detalle_compra FOREIGN KEY (detalle_compra_id) REFERENCES detalle_compra(detalle_compra_id) ON DELETE CASCADE ON UPDATE CASCADE 

)ENGINE = InnoDb;


CREATE TABLE detalle_compra_con_productos (

detalle_compra_id int NOT NULL,
producto_id int NOT NULL,
orden_id int NOT NULL,

PRIMARY KEY (producto_id,orden_id),
CONSTRAINT detalle_compra_con_productos_productos FOREIGN KEY (producto_id) REFERENCES productos(producto_id),
CONSTRAINT detalle_compra_con_productos_ordenes_compras FOREIGN KEY (orden_id) REFERENCES ordenes_compras(orden_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT detalle_compra_con_productos_detalle_compra FOREIGN KEY (detalle_compra_id) REFERENCES detalle_compra(detalle_compra_id) ON DELETE CASCADE ON UPDATE CASCADE 

)ENGINE = InnoDb;


CREATE TABLE facturas_proveedores (
factura_proveedor_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
proveedor_id int NOT NULL,
orden_id int NOT NULL,
metodo_pago_id int NOT NULL,
estado_id int NOT NULL,
total int NULL,
pagado int NULL,
por_pagar int NULL,
observacion varchar(100),
fecha date NOT NULL,
hora TIME DEFAULT (CURRENT_TIMESTAMP),

PRIMARY KEY (factura_proveedor_id),
CONSTRAINT facturas_proveedores_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id),
CONSTRAINT facturas_proveedores_ordenes_compras FOREIGN KEY (orden_id) REFERENCES ordenes_compras(orden_id) ON UPDATE CASCADE ON DELETE CASCADE,
CONSTRAINT facturas_proveedores_metodos_de_pagos FOREIGN KEY (metodo_pago_id) REFERENCES metodos_de_pagos(metodo_pago_id),
CONSTRAINT facturas_proveedores_proveedores FOREIGN KEY (proveedor_id) REFERENCES proveedores(proveedor_id),
CONSTRAINT facturas_proveedores_estados_generales FOREIGN KEY (estado_id) REFERENCES estados_generales(estado_id)

)ENGINE = InnoDb; 


CREATE TABLE ordenes_gastos (

orden_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
proveedor_id int NOT NULL,
origen ENUM('caja', 'fuera_caja') NOT NULL DEFAULT 'caja',
fecha date NOT NULL,

PRIMARY KEY (orden_id),
CONSTRAINT ordenes_gastos_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id),
CONSTRAINT ordenes_gastos_proveedores FOREIGN KEY (proveedor_id) REFERENCES proveedores(proveedor_id)

)ENGINE = InnoDb; 

 
CREATE TABLE motivos (

motivo_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
descripcion varchar(60),
fecha date NOT NULL,

PRIMARY KEY (motivo_id),
CONSTRAINT motivos_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id)

)ENGINE = InnoDb;  


CREATE TABLE detalle_gasto (

detalle_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
motivo_id int NOT NULL,
orden_id int NOT NULL,
cantidad int NOT NULL,
precio int NOT NULL,
impuestos int NOT NULL,
observacion varchar(100) NULL,
fecha date NOT NULL,

PRIMARY KEY (detalle_id),
CONSTRAINT detalle_gasto_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id),
CONSTRAINT detalle_gasto_motivos FOREIGN KEY (motivo_id) REFERENCES motivos(motivo_id),
CONSTRAINT detalle_gasto_ordenes_gastos FOREIGN KEY (orden_id) REFERENCES ordenes_gastos(orden_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb; 


CREATE TABLE gastos (

gasto_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
proveedor_id int NOT NULL,
orden_id int NOT NULL,
total DECIMAL(10,2) NOT NULL,
pagado DECIMAL(10,2) NOT NULL,
observacion varchar(100) NULL,
fecha date NOT NULL,
hora TIME DEFAULT (CURRENT_TIMESTAMP),

PRIMARY KEY (gasto_id),
CONSTRAINT gastos_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id),
CONSTRAINT gastos_proveedores FOREIGN KEY (proveedor_id) REFERENCES proveedores(proveedor_id),
CONSTRAINT gastos_ordenes_gastos FOREIGN KEY (orden_id) REFERENCES ordenes_gastos(orden_id) ON DELETE CASCADE ON UPDATE CASCADE

)ENGINE = InnoDb; 


CREATE TABLE pagos (

pago_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
cliente_id int NOT NULL,
metodo_pago_id int NOT NULL,
recibido DECIMAL(10,2) NOT NULL,
observacion varchar(100) NULL,
fecha date NULL,
hora TIME DEFAULT (CURRENT_TIMESTAMP),

PRIMARY KEY (pago_id),
CONSTRAINT pagos_metodos_de_pagos FOREIGN KEY (metodo_pago_id) REFERENCES metodos_de_pagos(metodo_pago_id),
CONSTRAINT pagos_clientes FOREIGN KEY (cliente_id) REFERENCES clientes(cliente_id),
CONSTRAINT pagos_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id) 

)ENGINE = InnoDb; 


CREATE TABLE pagos_a_facturas_ventas (

pago_id int NOT NULL,
factura_venta_id int NOT NULL,

PRIMARY KEY (pago_id,factura_venta_id),
CONSTRAINT pagos_a_facturas_ventas_pagos FOREIGN KEY (pago_id) REFERENCES pagos(pago_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT pagos_a_facturas_ventas_facturas_ventas FOREIGN KEY (factura_venta_id) REFERENCES facturas_ventas(factura_venta_id) ON DELETE CASCADE ON UPDATE CASCADE 

)ENGINE = InnoDb; 


CREATE TABLE pagos_a_facturasRP (

pago_id int NOT NULL,
facturaRP_id int NOT NULL,

PRIMARY KEY (pago_id,facturaRP_id),
CONSTRAINT pagos_a_facturasRP_pagos FOREIGN KEY (pago_id) REFERENCES pagos(pago_id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT pagos_a_facturasRP_facturasRP FOREIGN KEY (facturaRP_id) REFERENCES facturasRP(facturaRP_id) ON DELETE CASCADE ON UPDATE CASCADE 

)ENGINE = InnoDb;


CREATE TABLE pagos_proveedores (

pago_factura_id int auto_increment NOT NULL,
factura_proveedor_id int NOT NULL,
usuario_id int NOT NULL,
proveedor_id int NOT NULL,
metodo_pago_id int NOT NULL,
recibido int NOT NULL,
observacion varchar(150) NULL,
fecha date NULL,
hora TIME DEFAULT (CURRENT_TIMESTAMP),

PRIMARY KEY (pago_factura_id),
CONSTRAINT pagos_proveedores_metodos_de_pagos FOREIGN KEY (metodo_pago_id) REFERENCES metodos_de_pagos(metodo_pago_id),
CONSTRAINT pagos_proveedores_proveedores FOREIGN KEY (proveedor_id) REFERENCES proveedores(proveedor_id),
CONSTRAINT pagos_proveedores_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id),
CONSTRAINT pagos_proveedores_facturas_proveedores FOREIGN KEY (factura_proveedor_id) REFERENCES facturas_proveedores(factura_proveedor_id) ON DELETE CASCADE ON UPDATE CASCADE 

)ENGINE = InnoDb; 


CREATE TABLE cotizaciones (

cotizacion_id int auto_increment NOT NULL,
usuario_id int NOT NULL,
cliente_id int NOT NULL,
total DECIMAL(10,2) NOT NULL,
descripcion varchar(100) NULL,
fecha date NULL,

PRIMARY KEY (cotizacion_id),
CONSTRAINT cotizaciones_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id) ON UPDATE CASCADE,
CONSTRAINT cotizaciones_clientes FOREIGN KEY (cliente_id) REFERENCES clientes(cliente_id) ON UPDATE CASCADE ON DELETE CASCADE

)ENGINE = InnoDb; 


CREATE TABLE detalle_cotizaciones (

detalle_id int auto_increment NOT NULL,
cotizacion_id int NOT NULL,
usuario_id int NOT NULL,
descripcion varchar(70) NOT NULL,
cantidad DECIMAL(10,2) NOT NULL,
precio DECIMAL(10,2) NOT NULL,
impuesto DECIMAL(10,2) NULL,
descuento DECIMAL(10,2) NULL,
fecha date NULL,

PRIMARY KEY (detalle_id),
CONSTRAINT detalle_cotizaciones_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id) ON UPDATE CASCADE,
CONSTRAINT detalle_cotizaciones_cotizaciones FOREIGN KEY (cotizacion_id) REFERENCES cotizaciones(cotizacion_id) ON UPDATE CASCADE ON DELETE CASCADE

)ENGINE = InnoDb; 


CREATE TABLE cierres_caja (
    cierre_id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    fecha_apertura DATETIME NOT NULL,
    fecha_cierre DATETIME DEFAULT NULL,
    saldo_inicial DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    ingresos_efectivo DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    ingresos_tarjeta DECIMAL(10,2) NOT NULL DEFAULT 0.00,
	ingresos_transferencia DECIMAL(10,2) NOT NULL DEFAULT 0.00,
	ingresos_cheque DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    egresos_caja DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    egresos_fuera DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    retiros DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    reembolsos DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    efectivo_caja DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_esperado DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_real DECIMAL(10,2) NOT NULL DEFAULT 0.00, 
    diferencia DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    observaciones TEXT NULL,
    estado ENUM('abierto', 'cerrado') DEFAULT 'abierto' NOT NULL,
    
    CONSTRAINT cierres_caja_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id) ON UPDATE CASCADE
)ENGINE = InnoDb;

