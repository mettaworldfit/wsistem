#______________________________________________________________________
# TRIGGERS
# Tablas temporales
#______________________________________________________________________

DELIMITER //
DROP TRIGGER IF EXISTS restar_stock_temporal_update //

CREATE TRIGGER restar_stock_temporal_update
AFTER UPDATE ON detalle_temporal
FOR EACH ROW
BEGIN
    -- Si el producto tiene una cantidad positiva
    IF (NEW.producto_id > 0) THEN
        -- Restar la diferencia entre la nueva cantidad y la antigua en la tabla productos
        UPDATE productos
        SET cantidad = cantidad - (NEW.cantidad - OLD.cantidad)
        WHERE producto_id = NEW.producto_id;
    
    -- Si el producto es una pieza
    ELSEIF (NEW.pieza_id > 0) THEN
        -- Restar la diferencia entre la nueva cantidad y la antigua en la tabla piezas
        UPDATE piezas
        SET cantidad = cantidad - (NEW.cantidad - OLD.cantidad)
        WHERE pieza_id = NEW.pieza_id;
    END IF;
END //
DELIMITER ;


DELIMITER //
DROP TRIGGER IF EXISTS  restar_stocks_temporales //

CREATE TRIGGER restar_stocks_temporales
AFTER INSERT ON detalle_temporal FOR EACH ROW
BEGIN

IF (NEW.producto_id > 0) THEN

  Update productos
  set cantidad = cantidad - NEW.cantidad
  where producto_id = NEW.producto_id;
  
ELSEIF (NEW.pieza_id > 0) THEN

  Update piezas
  set cantidad = cantidad - NEW.cantidad
  where pieza_id = NEW.pieza_id;

END IF ;

END //
DELIMITER ;


DELIMITER //
DROP TRIGGER IF EXISTS  devolver_stocks_temporales //

CREATE TRIGGER devolver_stocks_temporales
AFTER DELETE ON detalle_temporal FOR EACH ROW
BEGIN

IF (OLD.producto_id > 0) THEN

  Update productos
  set cantidad = cantidad + OLD.cantidad
  where producto_id = OLD.producto_id;
  
  delete from detalle_variantes_temporal where detalle_temporal_id = OLD.detalle_temporal_id;

ELSEIF (OLD.pieza_id > 0) THEN

  Update piezas
  set cantidad = cantidad + OLD.cantidad
  where pieza_id = OLD.pieza_id;

END IF ;

END //
DELIMITER ;


DELIMITER //
DROP TRIGGER IF EXISTS  devolver_variantes_temporales //

CREATE TRIGGER devolver_variantes_temporales
AFTER DELETE ON detalle_variantes_temporal FOR EACH ROW
BEGIN

  Update variantes set estado_id = 13 where variante_id = OLD.variante_id;

END //
DELIMITER ;


DELIMITER //
DROP TRIGGER IF EXISTS  restar_variantes_temporales //

CREATE TRIGGER restar_variantes_temporales
AFTER INSERT ON detalle_variantes_temporal FOR EACH ROW
BEGIN

  Update variantes
  set estado_id = 14
  where variante_id = NEW.variante_id;

END //
DELIMITER ;


#______________________________________________________________________
# TRIGGERS
# Detalle de factura de venta
#______________________________________________________________________


DELIMITER //
DROP TRIGGER IF EXISTS restar_stock_productos_update //

CREATE TRIGGER restar_stock_productos_update
AFTER UPDATE ON detalle_facturas_ventas FOR EACH ROW
BEGIN
    DECLARE ID INT;

    -- Obtener el ID del producto
    SET @ID = (SELECT producto_id FROM detalle_ventas_con_productos WHERE detalle_venta_id = NEW.detalle_venta_id);

    -- Si existe el ID del producto
    IF (@ID IS NOT NULL) THEN
        -- Restamos la diferencia
        UPDATE productos
        SET cantidad = cantidad - (NEW.cantidad - OLD.cantidad)
        WHERE producto_id = @ID;
        
	END IF;
END //
DELIMITER ;


DELIMITER //
DROP TRIGGER IF EXISTS restar_stock_piezas_update //

CREATE TRIGGER restar_stock_piezas_update
AFTER UPDATE ON detalle_facturas_ventas FOR EACH ROW
BEGIN
    DECLARE ID INT;

    -- Obtener el ID de la pieza desde la tabla detalle_ventas_con_piezas_
    SET @ID = (SELECT pieza_id FROM detalle_ventas_con_piezas_ WHERE detalle_venta_id = NEW.detalle_venta_id);

    -- Si existe el ID de la pieza
    IF (@ID IS NOT NULL) THEN
        -- Restamos la diferencia entre la cantidad nueva y la antigua en la tabla piezas
        UPDATE piezas
        SET cantidad = cantidad - (NEW.cantidad - OLD.cantidad)
        WHERE pieza_id = @ID;
    END IF;
END //
DELIMITER ;


DELIMITER //
DROP TRIGGER IF EXISTS restar_stock_productos //

CREATE TRIGGER restar_stock_productos
AFTER INSERT ON detalle_ventas_con_productos FOR EACH ROW
BEGIN

declare ID int;
declare Cant decimal(10,2);

SET @ID = (SELECT producto_id FROM detalle_ventas_con_productos where detalle_venta_id = NEW.detalle_venta_id);
SET @Cant = (SELECT cantidad FROM detalle_facturas_ventas where detalle_venta_id = NEW.detalle_venta_id);

IF (@ID != '') THEN 

  Update productos
  set cantidad = cantidad - @Cant
   where producto_id = @ID;
   
END IF;

END //
DELIMITER ;


DELIMITER //
DROP TRIGGER IF EXISTS restar_stock_piezas //

CREATE TRIGGER restar_stock_piezas
AFTER INSERT ON detalle_ventas_con_piezas_ FOR EACH ROW
BEGIN

declare ID int;
declare Cant decimal(10,2);

SET @ID = (SELECT pieza_id FROM detalle_ventas_con_piezas_ where detalle_venta_id = NEW.detalle_venta_id);
SET @Cant = (SELECT cantidad FROM detalle_facturas_ventas where detalle_venta_id = NEW.detalle_venta_id);

IF (@ID != '') THEN 

  Update piezas
  set cantidad = cantidad - @Cant
   where pieza_id = @ID;
   
END IF;

END //
DELIMITER ;


DELIMITER //
DROP TRIGGER IF EXISTS facturar_variantes //

CREATE TRIGGER facturar_variantes
AFTER INSERT ON variantes_facturadas FOR EACH ROW
BEGIN

  Update variantes
  set estado_id = 14
  where variante_id = NEW.variante_id;

END //
DELIMITER ;


DELIMITER //
DROP TRIGGER IF EXISTS devolver_variantes //

CREATE TRIGGER devolver_variantes
AFTER DELETE ON variantes_facturadas FOR EACH ROW
BEGIN

  Update variantes
  set estado_id = 13
  where variante_id = OLD.variante_id;

END //
DELIMITER ;


DELIMITER //
DROP TRIGGER IF EXISTS  devolver_stocks //

CREATE TRIGGER devolver_stocks
BEFORE DELETE ON detalle_facturas_ventas FOR EACH ROW
BEGIN

declare IDproducto int;
declare IDpieza int;

SET @IDproducto = (SELECT producto_id FROM detalle_ventas_con_productos where detalle_venta_id = OLD.detalle_venta_id);
SET @IDpieza = (SELECT pieza_id FROM detalle_ventas_con_piezas_ where detalle_venta_id = OLD.detalle_venta_id);

IF (@IDproducto != '') THEN 

  Update productos
  set cantidad = cantidad + OLD.cantidad
  where producto_id = @IDproducto;
   
delete from variantes_facturadas where detalle_venta_id = OLD.detalle_venta_id;
   
ELSEIF (@IDpieza != '') THEN
  Update piezas
  set cantidad = cantidad + OLD.cantidad
  where pieza_id = @IDpieza;
END IF;

END //
DELIMITER ;


#______________________________________________________________________
# TRIGGERS
# Facturas de ventas
#______________________________________________________________________

DELIMITER //
DROP TRIGGER IF EXISTS  agregar_item_venta //

CREATE TRIGGER agregar_item_venta
AFTER INSERT ON detalle_facturas_ventas FOR EACH ROW
BEGIN

DECLARE pendienteX decimal(10,2);

Update facturas_ventas
set total = total + (NEW.cantidad * (NEW.impuesto + NEW.precio )- NEW.descuento), 
pendiente = total - recibido
where factura_venta_id = NEW.factura_venta_id;

SET @pendienteX = (select pendiente from facturas_ventas where factura_venta_id = NEW.factura_venta_id);

IF (@pendienteX = 0) THEN
  UPDATE facturas_ventas SET estado_id = 3 WHERE factura_venta_id = NEW.factura_venta_id;
ELSEIF (@pendienteX > 0) THEN
  UPDATE facturas_ventas SET estado_id = 4 WHERE factura_venta_id = NEW.factura_venta_id;
END IF;

END //
DELIMITER ;


DELIMITER //
DROP TRIGGER IF EXISTS eliminar_item_venta //

CREATE TRIGGER eliminar_item_venta 
BEFORE DELETE ON detalle_facturas_ventas FOR EACH ROW
BEGIN

DECLARE pendienteX decimal(10,2);

Update facturas_ventas
set total = total - (OLD.cantidad * (OLD.impuesto + OLD.precio - OLD.descuento)), 
pendiente = pendiente - (OLD.cantidad * (OLD.impuesto + OLD.precio - OLD.descuento))
where factura_venta_id = OLD.factura_venta_id;

SET @pendienteX = (select pendiente from facturas_ventas where factura_venta_id = OLD.factura_venta_id);

IF (@pendienteX <= 0) THEN
  UPDATE facturas_ventas SET estado_id = 3, pendiente = 0 WHERE factura_venta_id = OLD.factura_venta_id;
ELSEIF (@pendienteX > 0) THEN
  UPDATE facturas_ventas SET estado_id = 4 WHERE factura_venta_id = OLD.factura_venta_id;
END IF;

END //
DELIMITER ;


#______________________________________________________________________
# TRIGGERS
# Pagos a facturas de ventas
#______________________________________________________________________


DELIMITER //
DROP TRIGGER IF EXISTS  agregar_pago_venta //

CREATE TRIGGER agregar_pago_venta 
AFTER INSERT ON pagos_a_facturas_ventas FOR EACH ROW
BEGIN

begin
declare pendienteX decimal(10,2);
declare recibido decimal(10,2);
end ;

SET @recibido = (select recibido from pagos where pago_id = NEW.pago_id);

Update facturas_ventas
set recibido = recibido + @recibido,
pendiente = pendiente - @recibido
where factura_venta_id = NEW.factura_venta_id;

SET @pendienteX = (select pendiente from facturas_ventas where factura_venta_id = NEW.factura_venta_id);

IF (@pendienteX = 0) THEN
  UPDATE facturas_ventas SET estado_id = 3 WHERE factura_venta_id = NEW.factura_venta_id;
ELSEIF (@pendienteX > 0) THEN
  UPDATE facturas_ventas SET estado_id = 4 WHERE factura_venta_id = NEW.factura_venta_id;
END IF;

END //
DELIMITER ;


DELIMITER //
DROP TRIGGER IF EXISTS eliminar_pago_venta //

CREATE TRIGGER eliminar_pago_venta
AFTER DELETE ON pagos_a_facturas_ventas FOR EACH ROW
BEGIN

begin
declare pendienteX decimal(10,2);
declare recibido decimal(10,2);
end ;

SET @recibido = (select recibido from pagos where pago_id = OLD.pago_id);

Update facturas_ventas
set recibido = recibido - @recibido,
pendiente = pendiente + @recibido
where factura_venta_id = OLD.factura_venta_id;

SET @pendienteX = (select pendiente from facturas_ventas where factura_venta_id = OLD.factura_venta_id);

IF (@pendienteX = 0) THEN
  UPDATE facturas_ventas SET estado_id = 3 WHERE factura_venta_id = OLD.factura_venta_id;
ELSEIF (@pendienteX > 0) THEN
  UPDATE facturas_ventas SET estado_id = 4 WHERE factura_venta_id = OLD.factura_venta_id;
END IF;

delete from pagos where pago_id = OLD.pago_id;

END //
DELIMITER ;


#______________________________________________________________________
# TRIGGERS
# Pagos a facturas de reparaciones
#______________________________________________________________________


DELIMITER //
DROP TRIGGER IF EXISTS  agregar_pago_reparacion //

CREATE TRIGGER agregar_pago_reparacion
AFTER INSERT ON pagos_a_facturasRP FOR EACH ROW
BEGIN

begin
declare pendienteX decimal(10,2);
declare recibido decimal(10,2);
end ;

SET @recibido = (select recibido from pagos where pago_id = NEW.pago_id);

Update facturasRP
set recibido = recibido + @recibido,
pendiente = pendiente - @recibido
where facturaRP_id = NEW.facturaRP_id;

SET @pendienteX = (select pendiente from facturasRP where facturaRP_id = NEW.facturaRP_id);

IF (@pendienteX = 0) THEN
  UPDATE facturasRP SET estado_id = 3 WHERE facturaRP_id = NEW.facturaRP_id;
ELSEIF (@pendienteX > 0) THEN
  UPDATE facturasRP SET estado_id = 4 WHERE facturaRP_id = NEW.facturaRP_id;
END IF;

END //
DELIMITER ;


DELIMITER //
DROP TRIGGER IF EXISTS eliminar_pago_reparacion //

CREATE TRIGGER eliminar_pago_reparacion
AFTER DELETE ON pagos_a_facturasRP FOR EACH ROW
BEGIN

begin
declare pendienteX decimal(10,2);
declare recibido decimal(10,2);
end ;

SET @recibido = (select recibido from pagos where pago_id = OLD.pago_id);

Update facturasRP
set recibido = recibido - @recibido,
pendiente = pendiente + @recibido
where facturaRP_id = OLD.facturaRP_id;

SET @pendienteX = (select pendiente from facturasRP where facturaRP_id = OLD.facturaRP_id);

IF (@pendienteX = 0) THEN
  UPDATE facturasRP SET estado_id = 3 WHERE facturaRP_id = OLD.facturaRP_id;
ELSEIF (@pendienteX > 0) THEN
  UPDATE facturasRP SET estado_id = 4 WHERE facturaRP_id = OLD.facturaRP_id;
END IF;

delete from pagos where pago_id = OLD.pago_id;

END //
DELIMITER ;


#______________________________________________________________________
# TRIGGERS
# Pagos a facturas de proveedores
#______________________________________________________________________


DELIMITER //
DROP TRIGGER IF EXISTS  agregar_pagos_proveedores //

CREATE TRIGGER agregar_pagos_proveedores
AFTER INSERT ON pagos_proveedores FOR EACH ROW
BEGIN

begin
declare por_pagarX decimal(10,2);
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
declare por_pagarX decimal(10,2);
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


#______________________________________________________________________
# TRIGGERS
# Reparaciones
#______________________________________________________________________


DELIMITER //
DROP TRIGGER IF EXISTS  restar_stock_piezas_rp //

CREATE TRIGGER restar_stock_piezas_rp
AFTER INSERT ON detalle_ordenRP_con_piezas FOR EACH ROW
BEGIN

begin
declare cantidad decimal(10,2);
end;

SET @cantidad = (SELECT cantidad FROM detalle_ordenRP where detalle_ordenRP_id = NEW.detalle_ordenRP_id);

Update piezas
set cantidad = cantidad - @cantidad
where pieza_id = NEW.pieza_id;

END //
DELIMITER ;


DELIMITER //
DROP TRIGGER IF EXISTS devolver_stocks_piezas //
CREATE TRIGGER devolver_stocks_piezas
BEFORE DELETE ON detalle_ordenRP
FOR EACH ROW
BEGIN
  DECLARE v_pieza_id INT;

  -- Buscar la pieza asociada al detalle eliminado
  SELECT pieza_id INTO v_pieza_id
  FROM detalle_ordenRP_con_piezas
  WHERE detalle_ordenRP_id = OLD.detalle_ordenRP_id
  LIMIT 1;

  -- Devolver la cantidad al stock de esa pieza
  IF v_pieza_id IS NOT NULL THEN
    UPDATE piezas
    SET cantidad = cantidad + OLD.cantidad
    WHERE pieza_id = v_pieza_id;
  END IF;

END //
DELIMITER ;


#______________________________________________________________________
# TRIGGERS
# Facturas de reparación
#______________________________________________________________________


DELIMITER //
DROP TRIGGER IF EXISTS  agregar_item_orden_reparacion //

CREATE TRIGGER agregar_item_orden_reparacion 
AFTER INSERT ON detalle_ordenRP FOR EACH ROW
BEGIN

DECLARE pendienteX decimal(10,2);

Update facturasRP
set total = total + (NEW.cantidad * NEW.precio) - NEW.descuento, 
pendiente = total - recibido
where orden_rp_id = NEW.orden_rp_id;

SET @pendienteX = (select pendiente from facturasRP where orden_rp_id = NEW.orden_rp_id);

IF (@pendienteX = 0) THEN
  UPDATE facturasRP SET estado_id = 3 WHERE orden_rp_id = NEW.orden_rp_id;
ELSEIF (@pendienteX > 0) THEN
  UPDATE facturasRP SET estado_id = 4 WHERE orden_rp_id = NEW.orden_rp_id;
END IF;

END //
DELIMITER ;


DELIMITER //
DROP TRIGGER IF EXISTS eliminar_item_orden_reparacion //

CREATE TRIGGER eliminar_item_orden_reparacion
BEFORE DELETE ON detalle_ordenRP FOR EACH ROW
BEGIN

DECLARE pendienteX decimal(10,2);

Update facturasRP
set total = total - (OLD.cantidad * OLD.precio) - OLD.descuento, 
pendiente = pendiente - (OLD.cantidad * OLD.precio) - OLD.descuento
where orden_rp_id = OLD.orden_rp_id;

SET @pendienteX = (select pendiente from facturasRP where orden_rp_id = OLD.orden_rp_id);

IF (@pendienteX <= 0) THEN
  UPDATE facturasRP SET estado_id = 3, pendiente = 0 WHERE orden_rp_id = OLD.orden_rp_id;
ELSEIF (@pendienteX > 0) THEN
  UPDATE facturasRP SET estado_id = 4 WHERE orden_rp_id = OLD.orden_rp_id;
END IF;

END //
DELIMITER ;





#______________________________________________________________________
# TRIGGERS
# Facturas de proveedores
#______________________________________________________________________


DELIMITER //
DROP TRIGGER IF EXISTS  agregar_item_orden_compra //

CREATE TRIGGER agregar_item_orden_compra
AFTER INSERT ON detalle_compra FOR EACH ROW
BEGIN

DECLARE por_pagarX decimal(10,2);

Update facturas_proveedores
set total = total + (NEW.cantidad * NEW.precio) - NEW.descuentos + NEW.impuestos, 
por_pagar = total - pagado
where orden_id = NEW.orden_id;

SET @por_pagarX = (select por_pagar from facturas_proveedores where orden_id = NEW.orden_id);

IF (@por_pagarX = 0) THEN
  UPDATE facturas_proveedores SET estado_id = 3 WHERE orden_id = NEW.orden_id;
ELSEIF (@por_pagarX > 0) THEN
  UPDATE facturas_proveedores SET estado_id = 4 WHERE orden_id = NEW.orden_id;
END IF;

END //
DELIMITER ;


DELIMITER //
DROP TRIGGER IF EXISTS eliminar_item_orden_compra //

CREATE TRIGGER eliminar_item_orden_compra 
BEFORE DELETE ON detalle_compra FOR EACH ROW
BEGIN

DECLARE por_pagarX decimal(10,2);

Update facturas_proveedores
set total = total - (OLD.cantidad * OLD.precio) - OLD.descuentos + OLD.impuestos, 
por_pagar = por_pagar - (OLD.cantidad * OLD.precio) - OLD.descuentos + OLD.impuestos
where orden_id = OLD.orden_id;

SET @por_pagarX = (select por_pagar from facturas_proveedores where orden_id = OLD.orden_id);

IF (@por_pagarX <= 0) THEN
  UPDATE facturas_proveedores SET estado_id = 3, por_pagar = 0 WHERE orden_id = OLD.orden_id;
ELSEIF (@pendienteX > 0) THEN
  UPDATE facturas_proveedores SET estado_id = 4 WHERE orden_id = OLD.orden_id;
END IF;

END //
DELIMITER ;


#______________________________________________________________________
# TRIGGERS
# Cotizaciones
#______________________________________________________________________


DELIMITER //
DROP TRIGGER IF EXISTS  agregar_item_cotizacion //

CREATE TRIGGER agregar_item_cotizacion
AFTER INSERT ON detalle_cotizaciones FOR EACH ROW
BEGIN

Update cotizaciones
set total = total + (NEW.cantidad * (NEW.impuesto + NEW.precio )- NEW.descuento)
where cotizacion_id = NEW.cotizacion_id;

END //
DELIMITER ;


DELIMITER //
DROP TRIGGER IF EXISTS eliminar_item_detalle_cotizaciones //

CREATE TRIGGER eliminar_item_detalle_cotizaciones
BEFORE DELETE ON detalle_cotizaciones FOR EACH ROW
BEGIN

Update cotizaciones
set total = total - (OLD.cantidad * (OLD.impuesto + OLD.precio - OLD.descuento))
where cotizacion_id = OLD.cotizacion_id;

END //
DELIMITER ;


SHOW TRIGGERS;




-- ===================== CAMBIOS POR APLICAR =========================



DROP PROCEDURE IF EXISTS `vt_crearDetalleTemporalPOS`;
DELIMITER $$
CREATE PROCEDURE `vt_crearDetalleTemporalPOS` (in _producto_id int,in descripcion varchar(100), 
in _usuario_id int, in _cantidad decimal(10,2),in costo decimal(10,2), in precio decimal(10,2))
BEGIN
 
DECLARE stockDisponible DECIMAL(10,2);
 DECLARE cantidadExistente DECIMAL(10,2);

    -- Obtener la cantidad actual de detalle_temporal
    SELECT cantidad INTO cantidadExistente
    FROM detalle_temporal
    WHERE producto_id = _producto_id AND usuario_id = _usuario_id
    LIMIT 1;
    
     SELECT cantidad INTO stockDisponible
    FROM productos WHERE producto_id = _producto_id LIMIT 1;

 -- Verificar que la cantidad no exceda el stock disponible
    IF _cantidad > stockDisponible THEN
        SELECT 'Error: No hay suficiente stock disponible.' AS msg;
    ELSE
    
IF EXISTS (SELECT 1 FROM detalle_temporal WHERE producto_id = _producto_id AND usuario_id = _usuario_id) THEN

UPDATE detalle_temporal SET 
cantidad = cantidadExistente + _cantidad 
WHERE producto_id = _producto_id AND usuario_id = _usuario_id;

SELECT 'Detalle incrementado' AS msg;

ELSE 

    -- Si el producto no existe, insertar el nuevo detalle
        INSERT INTO detalle_temporal (usuario_id, producto_id, pieza_id, servicio_id, descripcion, cantidad, costo, precio, hora, fecha)
        VALUES (_usuario_id, _producto_id, 0, 0, descripcion, _cantidad, costo, precio, CURTIME(), CURDATE());

        SELECT LAST_INSERT_ID() AS msg;

END IF;
END IF;


END $$
DELIMITER ; 



INSERT INTO detalle_temporal 
(usuario_id, producto_id, pieza_id, servicio_id, descripcion, cantidad, costo, precio, hora, fecha)
VALUES
(usuario_id,producto_id,0,0,descripcion,cantidad,costo,precio, CURTIME(), CURDATE());



CALL vt_crearDetalleTemporalPOS(1077, 'ACEITUNAS GOYA RELLENAS DE PIMIENTOS', 1, 1, 97.85, 120.00);

select * from detalle_temporal;






