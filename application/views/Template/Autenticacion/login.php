<section id="login" class="height-100">
  <div class="container-fluid height-100">
    <div class="row height-100">
      <div class="col-12 col-sm-4 left height-100 relative">
        <div class="square">
          <img src="<?php echo IMG?>logo-sm.png" class="rounded mx-auto d-block mb-5" alt="<?php echo SEO_NAME?>" title="<?php echo SEO_NAME?>" />

          <?php echo form_open(current_url(),array('ajax' => 'true',"class"=>"form-signin"),array("id"=>$this->uri->segment(4)));	?>
            <div class="row">
              <div class="col">
                <div class="input-group input-group-sm mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fas fa-user"></i></span>
                  </div>
                  <input require placeholder="Nombre de Usuario" type="text" name="login" class="form-control form-control-underline" aria-label="Small" aria-describedby="inputGroup-sizing-sm">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <div class="input-group input-group-sm mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="inputGroup-sizing-sm2"><i class="fas fa-unlock-alt"></i></span>
                  </div>
                  <input require type="password" placeholder="Clave Personal" name="login" class="form-control form-control-underline" aria-label="Small" aria-describedby="inputGroup-sizing-sm2">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col text-center">
                <button type="submit" class="btn btn-primary btn-sm">Ingresar</button>
              </div>
            </div>
          <?php echo form_close();?>

        </div>
      </div>
      <div class="col-12 col-sm-8 right height-100 cover d-none d-md-none d-lg-block" style="background-image:url(<?php echo get_image('empresario.jpg',false); ?>)">

      </div>
    </div>
  </div>
</section>
