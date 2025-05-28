<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1>Catagorías</h1>
        </div>


        <div class="float-right">
            <a href="<?= base_url ?>categories/add" class="btn-custom btn-default">
                <i class="fas fa-plus"></i>
                <p>Agregar categoría</p>
            </a>
        </div>
    </div>
    <p class="title-info">Utiliza las categorías para clasificar tus productos.</p>
</div>



<div class="generalContainer">
<table id="categories" class="table-custom table">
        <thead>
            <tr>
                <th>N°</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Fecha</th>
                <th></th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="categoriesModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nueva categoría</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="#" method="post" id="formAddCategories">
                    <div class="form-group">
                        <label for="">Nombre<span class="text-danger">*</span></label>
                        <input class="form-control form-control-sm" type="text" name="name" id="" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="createNewCategorie">Guardar</button>
            </div>
        </div>
    </div>
</div>