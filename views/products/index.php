<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
        <h1>Productos</h1>
        </div>


        <div class="float-right">
        <a href="<?= base_url?>src/excel/reporte-productos.php" class="btn-custom btn-green">
            <i class="fas fa-file-excel"></i> <p>Excel</p> </a>
        <a href="<?=base_url?>products/add" class="btn-custom btn-default">
        <i class="fas fa-plus"></i>
           <p>Agrega producto</p> </a>
        </div>
    </div>
</div>



<div class="generalContainer">
<div id="loader"></div>
    <table id="example" class="table-custom table" style="display: none;">
        <thead>
            <tr>
                <th class="hide-cell">Código</th>
                <th>Nombre</th>
                <th class="hide-cell">Categorías</th>
                <th class="hide-cell">Almacen</th>
                <th>Cantidad</th>
                <th class="hide-cell">P/Compra</th>
                <th>P/Unitario</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php  while ($element = $products->fetch_object()) : ?>

                <tr>
                    <td class="hide-cell"><?= $element->cod_producto ?></td>
                    <td><?= ucwords($element->nombre_producto) ?> </td>
                    <td class="hide-cell"><?= ucwords($element->nombre_categoria) ?> </td>
                    <td class="hide-cell"><?= ucwords($element->nombre_almacen) ?> </td>

                    <?php if($element->cantidad > $element->cantidad_min){?>
                        <td class="text-success"><?= $element->cantidad ?></td>
            
                    <?php } else if($element->cantidad < 1) { ?>
                    <td class="text-danger"><?= $element->cantidad ?> </td>
                    <?php } else if($element->cantidad <= $element->cantidad_min) { ?>
                        <td class="text-warning"><?= $element->cantidad ?> </td>
                    <?php }; ?>

                    <td class="hide-cell"><?= number_format($element->precio_costo,2) ?></td>
                    <td><?= number_format($element->precio_unitario,2) ?></td>
                    <td>


                        <a  class="action-edit <?php if ($element->nombre_estado != 'Activo') { ?> action-disable <?php } ?>" title="Editar"
                                 href="<?php if ($element->nombre_estado == 'Activo') { 
                                     echo base_url.'products/edit&id='.$element->idproducto; 
                                     } else { echo '#'; } ?> "> 
                              <i class="fas fa-pencil-alt"></i>
                        </a>

                        <span class="<?php if ($element->nombre_estado == 'Activo'){ ?> action-active  <?php } else { ?> action-delete <?php } ?>" 
                        <?php if ($element->nombre_estado == 'Activo' && $_SESSION['identity']->nombre_rol == 'administrador') { ?>  onclick="disableProduct('<?= $element->idproducto ?>')" <?php } else if ($_SESSION['identity']->nombre_rol == 'administrador') { ?> onclick="enableProduct('<?= $element->idproducto ?>')" <?php } ?>
                        <?php if ($element->nombre_estado == 'Activo'){ ?> title="Desactivar ítem"  <?php } else { ?> title="Activar" <?php } ?> id="">
                              <i class="fas fa-lightbulb"></i>
                        </span>
                        
                        <span <?php if ($_SESSION['identity']->nombre_rol == 'administrador') { ?> class="action-delete" onclick="deleteProduct('<?= $element->idproducto ?>')" <?php } else { ?> class="action-delete action-disable" <?php } ?> title="Eliminar" >
                        <i class="fas fa-times"></i>
                        </span>
                      
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</div>