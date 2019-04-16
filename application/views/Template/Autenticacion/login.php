<section id="login" class="height-100">
  <div class="container-fluid height-100">
    <div class="row height-100">
      <div class="col-12 col-sm-5 left height-100 relative">
        <div class="square">
          <img src="<?php echo IMG?>logo-sm.png" class="rounded mx-auto d-block mb-5" alt="<?php echo SEO_NAME?>" title="<?php echo SEO_NAME?>" />
          <?php echo form_open( base_url("Api/post?modulo=Usuarios&m=login&formato=json"),
                                array(  'ajax' => 'true',
                                        "class"=>"form-signin",
                                        "autocomplete"=>"off"),
                                array(  "id"=>$this->uri->segment(4),));	?>
            <div class="row">
              <div class="col">
                <div class="input-group input-group-sm mb-3">
                  <input autocomplete="off" require placeholder="Nombre de Usuario" type="text" name="login" class="form-control form-control-underline" aria-label="Small" aria-describedby="inputGroup-sizing-sm">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <div class="input-group input-group-sm ">
                  <input autocomplete="off" require id="password" type="password" placeholder="Clave Personal" name="password" class="form-control form-control-underline" aria-label="Small" aria-describedby="inputGroup-sizing-sm2">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col text-right">
                <div class="toggle_pass" data-rel="#password" data-active="Mostrar" data-inactive="Ocultar">Mostrar</div>
              </div>
            </div>
            <div class="row mt-3">
              <div class="col text-center">
                <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
              </div>
            </div>
            <div class="form-group">
              <div class="text-left">
                <a  href="#Autenticacion/Recover"
                    data-id="recover01"
                    data-type="modal"
                    data-content="iframe"
                    data-title="Recover - <?php echo SEO_TITLE;?>"
                    class=""
                    data-url="<?PHP echo base_url("Autenticacion/Recover")?>">
                    Olvidé mi Contraseña
                </a>
              </div>
            </div>
            <div class="form-group">
              <div class="text-left">
                <span>¿No tienes cuenta aún?</span>
                <a class="prueba lightbox" title="Olvidé mi contraseña" data-height="170" data-size="modal-sm" data-type="modal" href="#autenticacion/recover">
                    Prueba Webcamplus por siete (7) días.
                </a>
              </div>
            </div>
            <div class="form-group">
              <div class="text-center">
                  <small>Todos los derechos reservados - BEL ServiceTM 2017</small>
              </div>
            </div>
          <?php echo form_close();?>
        </div>
      </div>
      <div class="col-12 col-sm-7 right height-100 cover d-none d-md-none d-lg-block" style="background-image:url(<?php echo get_image('empresario.jpg',false); ?>)">
      </div>
    </div>
  </div>
</section>
