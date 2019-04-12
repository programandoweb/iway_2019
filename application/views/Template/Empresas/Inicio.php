<div class="container h-100 d-inline-block">
  <div class="row">
    <?php #pre( $this->Rows );?>
    <div class="col">
      <?php
        $this->load->view("Template/SubMenu");
      ?>
      <div class = "table-responsive">
        <div class="p-5">
          <table class="table">
            <thead>
              <tr>
                <th scope="col">Avatar</th>
                <th scope="col">Nombre Legal / Comercial  </th>
                <th scope="col">Usuario</th>
                <th scope="col">Contacto</th>
                <th scope="col">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if(!empty($this->Rows["data"])){
                      foreach ($this->Rows["data"] as $key => $value) {
              ?>
              <tr>
                        <th scope="row"><?php echo $value["avatar"]?></th>
                        <td><?php echo $value["concat_nombres"]?></td>
                        <td><?php echo $value["login"]?></td>
                        <td>@<?php echo $value["login"]?></td>
                        <td><?php echo $value["edit"]?></td>
              </tr>
              <?php
                      }
                    }else{
              ?>
                          <td colspan="5" class="text-center"> No exiten registros</td>
              <?php
                    }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
