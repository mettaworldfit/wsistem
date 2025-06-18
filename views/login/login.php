<aside class="sidebar">

  <div class="user-access">

    <div class="logo-content">
      <img src="<?= base_url ?>public/imagen/sistem/icon.png" alt="" class="login-logo">
    </div>

    <span class="login-title">Inicial sesión</span>
    <br><br>

    <form class="login-form" id="login">

      <div class="input-div">
        <div class="i">
          <i class="fas fa-user"></i>
        </div>

        <input name="user" type="text" placeholder="" id="userName" required>
        <label for="#">Usuarios</label>
      </div>

      <div class="input-div">
        <div class="i">
          <i class="fas fa-lock"></i>
        </div>


        <input name="password" type="password" placeholder="" id="userPassword" required>
        <label for="#">Contraseña</label>
      </div>


      <button type="submit">
        <div id="btn-txt">Iniciar sesión <i class="fas fa-sign-out-alt"></i></div>
        <div class="load">
          <div class="loadingio-spinner-pulse-gf09yprf7f8">
            <div class="ldio-gbaxaaxfkjf">
              <div></div>
              <div></div>
              <div></div>
            </div>
          </div>
        </div>
      </button>
      <br>
      <span class="missing">No se ha podido iniciar sesión <i class="fas fa-key"></i></span>
    </form>

    <br>


  </div>

  </div>

</aside>

<aside class="sidebar-right">

  <div class="sidebar-right-content">


  </div>

</aside>