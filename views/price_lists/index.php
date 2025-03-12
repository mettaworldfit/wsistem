<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
        <h1>Lista de precios</h1>
        </div>
       

        <div class="float-right">
        <a href="<?= base_url ?>price_lists/add" class="btn-custom btn-default">
                <i class="fas fa-plus"></i>
                <p>Agregar lista</p>
            </a>
        </div>
    </div>
    <p class="title-info">Define precios especiales para tus productos.</p>
</div>



<div class="generalContainer">
    <table id="example" class="table-custom table ">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nombre</th>
                <th>Observación</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php while($element = $price_lists->fetch_object()): ?>        
                <?php $rows = Help::verify_parent_list($element->lista_id); while ($parent = $rows->fetch_object()) { ?>   
          
           <tr>
               <td><?= $element->lista_id?></td>
               <td><?= $element->nombre_lista?></td>
               <td class="note-width"><?= $element->descripcion?></td>
                    <td>

                        <a href="<?=base_url?>price_lists/edit&id=<?=$element->lista_id?>">
                        <span class="action-edit"><i class="fas fa-pencil-alt"></i></span>
                        </a>
                        
                        <span <?php if ($parent->parent_row == 0) { ?> class="action-delete" onclick="deletePriceList('<?= $element->lista_id ?>')" <?php } else { ?> class="action-delete action-disable" <?php } ?> title="Eliminar">
                        <i class="fas fa-times"></i>
                        </span>
                      
                    </td>
                </tr>
     <?php } endwhile; ?>
        </tbody>
    </table>

</div>