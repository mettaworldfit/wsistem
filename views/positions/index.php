<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Posiciones</h1>
        </div>


        <div class="float-right">
        <a class="btn-custom btn-default" href="<?= base_url ?>positions/add">
                <i class="fas fa-plus"></i>
                <p>Agregar posición</p>
            </a>
        </div>
    </div>
</div>



<div class="generalContainer">
    <table id="example" class="table-custom table ">
        <thead>
            <tr>
                <th>N°</th>
                <th>Referencia</th>
                <th>Fecha</th>
                <th></th>
            </tr>
        </thead>


      
        <tbody>
        <?php while($element = $positions->fetch_object()): ?>
            <?php $parents = Help::verify_parent_position($element->posicion_id); while ($parent = $parents->fetch_object()) { ?>

           <tr>
               <td><?= $element->posicion_id?></td>
               <td><?= $element->referencia ?></td>
               <td><?= $element->fecha ?></td>               
               <td>

                <a class="action-edit" href="<?= base_url.'positions/edit&id='.$element->posicion_id ?>" title="Editar"> 
                <i class="fas fa-pencil-alt"></i>
                </a>

                <span <?php if ($parent->parent_row == 0) { ?> class="action-delete" onclick="deletePosition('<?= $element->posicion_id ?>')" <?php } else { ?> class="action-delete action-disable" <?php } ?> title="Eliminar">
                <i class="fas fa-times"></i>
                </span>
               </td>
           </tr>

           <?php } endwhile; ?>
        </tbody>
     
    </table>

</div>


