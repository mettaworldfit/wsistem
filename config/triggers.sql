DELIMITER //
DROP TRIGGER IF EXISTS  agregar_pagos_proveedores //

CREATE TRIGGER agregar_pagos_proveedores
AFTER INSERT ON pagos_proveedores FOR EACH ROW
BEGIN

begin
declare por_pagarX int;
end ;

Update facturas_proveedores
set pagado = pagado + NEW.recibido,
por_pagar = por_pagar - NEW.recibido
where factura_proveedor_id = NEW.factura_proveedor_id;

SET @por_pagarX = (select por_pagar from facturas_proveedores where factura_proveedor_id = NEW.factura_proveedor_id);

IF (@por_pagarX  = 0) THEN
  UPDATE facturas_proveedores SET estado_id = 3 WHERE factura_proveedor_id = NEW.factura_proveedor_id;
ELSEIF (@por_pagarX > 0) THEN
  UPDATE facturas_proveedores SET estado_id = 4 WHERE factura_proveedor_id = NEW.factura_proveedor_id;
END IF;

END //
DELIMITER ;


DELIMITER //
DROP TRIGGER IF EXISTS eliminar_pagos_proveedores //

CREATE TRIGGER eliminar_pagos_proveedores
BEFORE DELETE ON pagos_proveedores FOR EACH ROW
BEGIN

begin
declare por_pagarX int;
end ;

Update facturas_proveedores
set pagado = pagado - OLD.recibido,
por_pagar = por_pagar + OLD.recibido
where factura_proveedor_id = OLD.factura_proveedor_id;

SET @por_pagarX = (select por_pagar from facturas_proveedores where factura_proveedor_id = OLD.factura_proveedor_id);

IF (@por_pagarX = 0) THEN
  UPDATE facturas_proveedores SET estado_id = 3 WHERE factura_proveedor_id = OLD.factura_proveedor_id;
ELSEIF (@por_pagarX > 0) THEN
  UPDATE facturas_proveedores SET estado_id = 4 WHERE factura_proveedor_id = OLD.factura_proveedor_id;
END IF;

END //
DELIMITER ;



CREATE TABLE pagos_proveedores (

pago_factura_id int auto_increment NOT NULL,
factura_proveedor_id int NOT NULL,
usuario_id int NOT NULL,
proveedor_id int NOT NULL,
metodo_pago_id int NOT NULL,
recibido int NOT NULL,
observacion varchar(150) NULL,
fecha date NULL,

PRIMARY KEY (pago_factura_id),
CONSTRAINT pagos_proveedores_metodos_de_pagos FOREIGN KEY (metodo_pago_id) REFERENCES metodos_de_pagos(metodo_pago_id),
CONSTRAINT pagos_proveedores_proveedores FOREIGN KEY (proveedor_id) REFERENCES proveedores(proveedor_id),
CONSTRAINT pagos_proveedores_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id),
CONSTRAINT pagos_proveedores_facturas_proveedores FOREIGN KEY (factura_proveedor_id) REFERENCES facturas_proveedores(factura_proveedor_id) ON DELETE CASCADE ON UPDATE CASCADE 

)ENGINE = InnoDb; 

