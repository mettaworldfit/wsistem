<div class="section-wrapper">
    <div class="align-content clearfix">
        <div class="float-left">
            <h1><i class="fas fa-user-circle"></i> Nuevo usuario</h1>
        </div>
    </div>
</div>

<div class="generalContainer-medium">
    <form class="user-content" method="POST" id="formUser">
        <div class="container">

            <div class="row mb-3">
                <div class="col-sm-6">
                    <label for="nombre">Nombre<span class="text-danger">*</span></label>
                    <input type="text" class="form-custom" name="name" required>
                </div>

                <div class="col-sm-6 mb-3">
                    <label for="apellidos">Apellidos</label>
                    <input type="text" class="form-custom" name="lastname">
                </div>
            </div>


            <div class="row mb-3">
                <div class="col-sm-6">
                    <label for="">Username<span class="text-danger">*</span></label>
                    <input type="text" class="form-custom" name="username" required>
                </div>

                <div class="col-sm-6 mb-3">
                    <label for="">Password<span class="text-danger">*</span></label>
                    <input type="password" class="form-custom" name="password" required>
                </div>

                <div class="col-sm-6">
                    <label for="">Rol</label>
                    <select class="form-custom search" name="role" id="rol" required>
                        <?php $roles = Help::roles();
                        while ($rol = $roles->fetch_object()): ?>
                            <option value="<?= $rol->rol_id ?>"><?= ucwords($rol->nombre_rol) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="buttons clearfix">
            <div class="floatButtons">
                <button class="btn-blue btn-custom" type="submit" id="">
                    <i class="fas fa-plus"></i>
                    <p>Guardar</p>
                </button>
            </div>
        </div>
    </form>
</div>