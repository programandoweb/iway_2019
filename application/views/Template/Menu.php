<?php
	$menu					=	array();
	$menu["Maestros"]		=	array("Empresas"=>"Empresas","Departamentos"=>"Sucursales","Usuarios"=>"Usuarios");
	$me_logo				=	img_logo(@get_empresa($this->user->empresa_id)->id);
  @$nombre = $this->user->primer_nombre.' '.$this->user->segundo_nombre.' '.$this->user->primer_apellido.' '.$this->user->segundo_apellido;
	$sucursal=(@get_empresa($this->user->empresa_id))?get_empresa($this->user->empresa_id)->nombre_legal.@get_empresa($this->user->empresa_id)->abreviacion.'</B>':'';
	$img_perfil	= me_img_profile();
?>
<div href="#Empresas" data-id="12" data-title="Empresa - JobERP" id="historyback" data-url=""></div>
<header>
  <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-primary yamm ">
		<div class="container-fluid pr-3 pl-3">
      <a class="navbar-brand" href="<?php echo base_url("Apanel")?>">
				<div class="d-sm-none">
        	<img src="<?php echo IMG?>logo-xsmall.png" class="d-block" alt="<?php echo SEO_NAME?>" title="<?php echo SEO_NAME?>" />
				</div>
				<div class="d-none d-lg-block">
					<img src="<?php echo IMG?>logo-xs.png" class="d-block" alt="<?php echo SEO_NAME?>" title="<?php echo SEO_NAME?>" />
				</div>
      </a>
			<button class="navbar-toggler navbar-toggler-left mb-1 collapsed" type="button" data-toggle="collapse" data-target="#navbarTopMenu" aria-controls="navbarTopMenu" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
      <div class="collapse navbar-collapse"  id="navbarTopMenu">
        <div style="margin:0 auto;">
          <ul class="navbar-nav mr-auto mt-2 mt-md-0 balancear bg-primary">
            <li class="nav-item active pl-3">
              <a class="nav-link " href="<?php echo base_url()?>">
                Home
                <span class="sr-only">(current)</span>
              </a>
            </li>
            <?php
              if(!isset($this->user->menu) && $this->user->type_id ==1){
                $this->user->menu	=	menu();
                $this->session->set_userdata(array('User'=>$this->user));
              }else if(!isset($this->user->menu) && $this->user->type_id != 1){
                $this->user->menu	=	menu_usuarios($this->user->rol_id);
                $this->session->set_userdata(array('User'=>$this->user));
              }
              if(isset($this->user->menu)){
                foreach($this->user->menu['roles_modulos_padre'] as $k => $v){
            ?>
                  <li class="dropdown yamm-fw nav-item active pl-3">
                    <a href="#" class="nav-link" data-toggle="dropdown">
                    <?php
                      print_r($v->modulo);
                    ?>
                    </a>
                    <?php foreach($this->user->menu['roles_modulos_hijos'][$v->id] as $k2 => $v2){?>
                    <ul class="dropdown-menu ">
                      <li class="grid-demo">
                        <div class="row">
                          <?php  foreach($v2 as $k3=>$v3){?>
                            <div class="col-sm-3 padding">
                              <div class="block">
                                <h5 class="block-title">
                                  <div class="ico">
																		<?php
																			if(!empty($v3->ico)){?>
																					<i class="<?php echo $v3->ico;?>"></i>
																		<?php
																			}else{
																		?>
																					<i class="fas fa-sort"></i>
																		<?php
																			}
																		?>
																	</div>
																	<div class="ts-13 text"><?php echo $v3->modulo;?></div>
                                </h5>
                                <?php foreach($this->user->menu['roles_modulos_nietos'][$v3->id] as $k4 => $v4){
                                  if($this->user->type_id==1){
                                ?>
                                <div class="pl-3">
																	<?php echo links($v4->url,$v4->id,$v4->modulo,$v4->modulo);?>
                                </div>
                                <?php
                                  }else if(array_search($v4->id,$this->user->menu['roles_modulos_permitidos'])===false){

                                  }else{
                                ?>
                                <div class="pl-3">
																	<?php echo links($v4->url,$v4->id,$v4->modulo,$v4->modulo);?>

                                </div>
                              <?php }}?>
                            </div>
                          </div>
                        <?php }?>
                      </div>
                    </li>
                  </ul>
                <?php }?>
              </li>
          <?php
            }
          }
          ?>
          </ul>
        </div>
        <ul class="navbar-nav ml-auto">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <img id="profile" class="rounded-circle" style="width:30px;" src="<?php echo $img_perfil;?>">
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown01">
              <!--a class="dropdown-item" href="#">Cambiar Password</a-->
							<a	href="#Api/post?modulo=Usuarios&m=logout&formato=json"
									data-id="12"
									data-formato="json"
									data-title="LogOut - <?php echo SEO_TITLE;?>"
									class="dropdown-item"
									data-url="<?php echo base_url("Api/post?modulo=Usuarios&m=logout&formato=json")?>">Cerrar Sesión</a>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</header>
