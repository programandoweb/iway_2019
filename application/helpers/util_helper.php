<?php
  /**/

  function copiar_roles($user_id,$empresa_id,$roles=array()){
    $ci=&get_instance();
    $tabla = "sys_tipo_usuario";
    $ci->db->select("*")
            ->from($tabla)
            ->where("empresa_id",1)
            ->where("type_id>",2);
    if(!empty($roles)){
      $ci->db->where_in("type_id",$roles);
    }
    $rows = $ci->db->get()->result();
    foreach ($rows as $key => $value) {
      $ci->db->insert($tabla,array("tipo"=>$value->tipo,"empresa_id"=>$empresa_id));
      $row_rol_a_copiar = $ci->db->select("*")->from("sys_roles")->where("type_id",$value->type_id)->get()->row();
      $ci->db->insert("sys_roles",array(  "json"=>$row_rol_a_copiar->json,
                                          "json_edit"=>$row_rol_a_copiar->json_edit,
                                          "json_add"=>$row_rol_a_copiar->json_add,
                                          "estado"=>$row_rol_a_copiar->estado,
                                        ));
    }
  }

  function token(){
    return md5(rand(100,9000).date("Y-m-d h:i:s"));
  }

  function campos($tabla){
    $ci=&get_instance();
    return $ci->db->list_fields($tabla);
  }

  function links($url,$id,$title,$modulo,$class="btn btn-link"){
    return '<a href="#'.$url.'"
                data-id="'.$id.'"
                data-title="'.$title . ' - ' . SEO_TITLE.'"
                class="'.$class.'"
                data-url="'.base_url($url).'">
                '.$modulo.'</a>';
  }

  function chequea_session($user){
  	$ci=&get_instance();
    if(!isset($user)){
      return false;
    }
  	$session=$ci->db->select('*')->from(DB_PREFIJO."sys_session")->where('session_id',$user->session_id)->get()->row();
  	$fechaGuardada=@$session->fecha;
  	$ahora =	date("Y-m-d H:i:s");
  	$tiempo_transcurrido = (strtotime($ahora)-strtotime($fechaGuardada));
  	if($tiempo_transcurrido>=SESSION_TIME){
  		return false;
  	}else{
  		$ci->db->where('session_id', $user->session_id);
  		$ci->db->update(DB_PREFIJO."sys_session",array("fecha"=>$ahora));
      return true;
  	}
  }

  function get_image($image,$html=true,$alt="",$class="img-fluid"){
    /*BUSCO SI EXISTE LA IMAGEN*/
    $path_img = PATH_IMG.$image;
    if(file_exists($path_img)){
      if($html){
        echo '<img src="'.IMG.$image.'" class="'.$class.'"  alt="'.$alt.'"/>';
      }else{
        return IMG.$image;
      }
    }else{
      if($html){
        echo '<img src="'.IMG.'default.png" class="'.$class.'"  alt="'.$alt.'"/>';
      }else{
        return IMG.'default.png';
      }
    }
  }

  function post($var=""){
  	$ci 	=& 	get_instance();
  	if($var==''){
  		return $ci->input->post();
  	}else{
  		return $ci->input->post($var, TRUE);
  	}
  }

  function get($var=""){
  	$ci 	=& 	get_instance();
  	if($var==''){
  		return $ci->input->get();
  	}else{
  		return $ci->input->get($var, TRUE);
  	}
  }

  function pre($var){
  	echo '<pre>';
  		print_r($var);
  	echo '</pre>';
  }

  function json_response($response=null, $code = 200,$callback='',$redirect=false,$message_return=false){
    header_remove();
    http_response_code($code);
    header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
    header('Content-Type: application/json');
    $status = array(
      200 => '200 OK',
      203 => '203 Error',
      400 => '400 Bad Request',
      422 => 'Unprocessable Entity',
      500 => '500 Internal Server Error'
    );
    header('Status: '.$status[$code]);
    $message      = (isset($response->message))?$response->message:"Bienvenido, Api Versión 1.0";
    if(!$message_return){
      $message    = "";
    }
    $json=array(
      'status'    => $code < 300,
      'code'      => $code,
    );
    if(isset($response)){
      foreach ($response as $key => $value) {
        $json[$key] = $value;
      }
    }
    if($redirect){
      $json["redirect"]	=	$redirect;
    }
    if($callback){
      $json["callback"]	=	$callback;
    }
    $json["message"]	=	(isset($json["message"]))?$json["message"]:$message;
    return json_encode($json);
  }

  function encriptar($var){
    $ci = get_instance();
    $ci->load->library("encryption");
    return $ci->encryption->encrypt($var);
  }

  function desencriptar($var){
    $ci = get_instance();
    $ci->load->library("encryption");
    return $ci->encryption->decrypt($var);
  }
  function img_logo($empresa_id){
    return image("uploads/perfiles/".$empresa_id.'/logo.jpg');
  }
  function get_empresa($id){
    $ci   =&  get_instance();
    $tabla            = "mae_cliente_joberp";
    return $ci->db->select("*")->from($tabla)->where("empresa_id",$id)->get()->row();
  }
  function image($image,$html=false,$imageTag=false,$attr=array()){
    $return_image=null;
    if(file_exists(PATH_IMG.$image)){
      $return_image = IMG.$image;
    }else{
      $return_image = IMG."No_image.png";
    }
    if(!$html){
      return $return_image;
    }else{
      $atributos  = '';
      foreach($attr as $k => $v){
        $atributos  .=   $k.'="'.$v.'"';
      }
      if(!$imageTag){
        return '<img src="'.$return_image.'" '.$atributos.' />';
      }else{
        return '<div class="image_rect image_default" style="background-image:url('.$return_image.');-webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;"></div>';
      }
    }
  }
  function me_img_profile(){
    $ci   =&  get_instance();
    return image("uploads/perfiles/".$ci->user->user_id.'/profile.jpg');
  }
  function get_links(){
    $ci   =&  get_instance();
    $tabla  = "op_links t1";
    $rows  = $ci->db->select("t1.id_link,t1.contador,t2.modulo,t2.url")->from($tabla)
                          ->join("sys_roles_modulos t2","t1.id_link = t2.id","left")
                          ->where('user_id',$ci->user->user_id)
                          ->order_by("t1.contador","DESC")
                          ->get()
                          ->result();
    return $rows;
  }

  function menu($rol_id=NULL){
      $ci   =&  get_instance();
      $tabla            = "sys_roles";
      $ci->db->select("*")->from($tabla);
      if(!empty($rol_id)){
        $ci->db->where("rol_id",$rol_id);
      }
      $roles            = $ci->roles          = $ci->db->get()->row();
      $menu_search        = json_decode(@$roles->json);
      $menu_edit          = json_decode(@$roles->json_edit);
      if(is_array($menu_search)&& is_array($menu_edit)){
        $in             = array_merge($menu_search,$menu_edit);
      }else if(is_array($menu_search)&& !is_array($menu_edit)){
        $in             = $menu_search;
      }else if(!is_array($menu_search)&& is_array($menu_edit)){
        $in             = $menu_edit;
      }else{
        $in             = array();
      }
      $tabla            = "sys_roles_modulos t1";
      $roles_modulos_padre  = $ci->db->select("*")->from($tabla)->where('modulo_padre',0)->order_by('order_','ASC')->get()->result();
      $roles_modulos_hijos  = array();
      $roles_modulos_nietos = array();
      foreach($roles_modulos_padre as $k =>$v){
        // $hijos                  = $ci->db->select("*")->from($tabla)
        //                                   ->where('modulo_padre',$v->id)
        $hijos                  = $ci->db->query("SELECT *, t1.id, COUNT(t2.modulo_padre) as cantidad
                                                    from sys_roles_modulos t1
                                                      LEFT JOIN sys_roles_modulos t2 ON t1.id=t2.modulo_padre
                                                        WHERE t1.modulo_padre = ".$v->id."
                                                          GROUP by t1.modulo
                                                            ORDER BY cantidad DESC")->result();

        $roles_modulos_hijos[$v->id][]  = $hijos;
        foreach($hijos as $k2 => $v2){
          $roles_modulos_nietos[$v2->id]  = $ci->db->select("*")->from($tabla)->where('modulo_padre',$v2->id)->order_by('order_','ASC')->get()->result();
        }
      }
      return array( "roles_modulos_padre" =>  $roles_modulos_padre,
                    "roles_modulos_hijos" =>  $roles_modulos_hijos,
                    "roles_modulos_nietos" => $roles_modulos_nietos,
                    "roles_modulos_permitidos"  =>  $in,
                    "modulo_search"       =>  $menu_search,
                    "modulo_edit"       =>  $menu_edit);
    }
    function get_configuracion_roles(){
      $ci = get_instance();
      return $ci->db->select("*")->from("sys_roles")->where("type_id",$ci->user->type_id)->get()->result();
    }
    function destruye_session($user){
      $ci           =&  get_instance();
      if(is_object($user)){
        $ci->db->where('session_id', $user->session_id);
        $ci->db->delete("sys_session");
        return true;
      }else{
        $ci->db->where('session_id', $user["session_id"]);
        $ci->db->delete("sys_session");
        return true;
      }
    }
    function avatar($id){
      $url  = IMG.'uploads/';
      $path = PATH_IMG.'uploads/';
      $ruta = $id.'/avatar';
      $extension_permitida=array(".jpg",".jpeg",".png",".gif");
      $return='<img src="'.IMG.'avatar.png" class="rounded-circle avatar-md" />';
      foreach ($extension_permitida as $key => $value) {
        if(file_exists($path.$ruta.$value)){
          $return='<img src="'.IMG.$ruta.$value.'" class="rounded-circle avatar-md" />';
        }
      }
      return $return;
    }

    function foreach_edit($data,$count=0){
      $ci           =&  get_instance();
    	$return	=	array();
    	foreach($data as $k => $v){
    		$id	=	'';
    		foreach($v as $k2 => $v2){
    			if($k2=='id'){
    				$id	=	$v2;
    			}
    			if($k2=='nombre'){
    				$nombre	=	$v2;
    			}
    			$explode	=	explode("::",$k2);
    			if($k2=="edit"){
    			  $return[$k][$k2]	   =		links($ci->uri->segment(1).'/Add/'.$id."?view=iframe","Editar_LB".$id,"Editar","<i class='fas fa-edit'></i>","pgrw_iframe");
    			}else if($k2=="estatus"){
    				$return[$k][$k2]	=		($v2==1)?'Activo':'Inactivo';
    			}else if($explode[0]=="json" && isset($explode[1])){
    				$json_decode				=	json_decode($v->json);
    				$label							=	$explode[1];
    				$return[$k][$label]	=	@$json_decode->$label;
    			}else if($k2=="nombre_frontOffice"){
    				$return[$k][$k2]	=		'<a target="_blank" title="Ver" href="'.base_url($v2.$id).'-BackOffice">'.$nombre.' <i class="fas fa-search"></i></a>';
    			}else if($k2=="json"){
    				$return[$k][$k2]			=		$v2;
    				$return[$k]["nombres"]		=		@json_decode($v2)->nombres .' '.@json_decode($v2)->apellidos;
    				$return[$k]["ciudad"]		=		@json_decode($v2)->ciudad;
    				$return[$k]["departamento"]	=		@json_decode($v2)->departamento;
    				$return[$k]["title"]					=	@json_decode($v2)->name;
    			}else{
    				$return[$k][$k2]	=		$v2;
    			}
    			if(@$return[$k]["title"]==''){
    				@$return[$k]["title"]=$v->title;
    			}
    		}
    	}
    	return array(	"draw"=>1,
                    "data"=>$return,
    								"recordsTotal"=>$count,
    								"recordsFiltered"=>$count);
    }
    function set_input_hidden($name,$id='',$row,$format = false){
      if($id==''){
        $id = $name;
      }
      $data = array(
          'type'  =>  'hidden',
          'name'  =>  $name,
          'id'    =>  $id
      );
      if(is_object($row)){
        if(isset($row->$name)){
          if($format){
            $data['value']  = format($row->$name,true);
          }else{
            $data['value']  = $row->$name;
          }
        }
      }else{
        if($format){
          $data['value']  = format($row,true);
        }else{
          $data['value']  = $row;
        }
      }
      echo form_input($data);
    }

    function set_input($name,$row,$placeholder='',$require=false,$class='',$extra=NULL,$format=false,$id_por_defecto=true){
      $data = array(
        'type'      =>  'text',
        'name'      =>  $name,
        'placeholder'   =>  $placeholder,
        'class'     =>  'form-control '.$class
      );

      if($id_por_defecto){
        $data['id'] = $name;
      }
      if(is_array($extra)){
        foreach($extra as $k => $v){
          $data[$k] = @$v;
        }
      }
      //pre($data);
      if($require){
        $data['require']= $require;
      }
      if(is_array($row)){
        if($format){
          $data['value']  = format(@$row[$name],true);
        }else{
          $data['value']  = @$row[$name];
        }
      }else if(is_object($row)){
        if($format){
          $data['value']  = format(@$row->$name,true);
        }else{
          $data['value']  = @$row->$name;
        }
      }else{
        if($format){
          $data['value']  = format(@$row,true);
        }else{
          $data['value']  = @$row;
        }
      }
      echo form_input($data);
    }
    function import($type,$file){
      switch($type){
        case "js":
          echo '<script async src="'.JS.$file.'.js"></script>';
        break;
        case "css":
          echo '<link rel="stylesheet" href="'.CSS.$file.'.css">';
        break;
        case "js3":
          echo '<script src="'.THIRDPARTY.$file.'.js"></script>';
        break;
        case "css3":
          echo '<link rel="stylesheet" href="'.THIRDPARTY.$file.'.css">';
        break;
      }
    }

    function MakeSelect($name,$estado,$extra = array("class"=>"form-control"),$data,$key = false){
      $options = array();
      if(!empty($data)){
        if(is_array($data)){
          foreach ($data as $k => $v){
            if($key){
              $options[$v] = $v;
            }else{
              $options[$k] = $v;
            }
          }
        }
      }
      return form_dropdown($name, $options, $estado,$extra);
    }

    function MakeSiNo($name,$estado=null,$extra=array()){
      $options = array(
        ""     => "Seleccione",
         1       => 'Si',
         0       => 'No',

      );
      return form_dropdown($name, $options, $estado,$extra);
    }

    function MakeDivisa($name,$estado=null,$extra=array()){
      $options = array(
        ""     => "Seleccione",
        'COP'       => 'COP',
        'USD'       => 'USD',
        'EUR'       =>'EUR'

      );
      return form_dropdown($name, $options, $estado,$extra);
    }

    function MakeTipoIdentidad($name,$estado=null,$extra=array(),$ids= null){
     $ci   =&  get_instance();
     $tabla            = "sys_tipo_identidad";
     $ci->db->select("*")->from($tabla);
     $options    = $ci->db->get()->result();
     $option     =   array(""=>"Seleccione");
     foreach($options as $v){
       $option[$v->tipo_identidad_id]   =   $v->tipo_identidad;
     }
     //pre($option); return;
     return form_dropdown($name, $option, $estado,$extra);
   }

   function ciudades($name,$row,$placeholder='',$require=false){
     if(empty($row)){
       $row=new stdClass();
       $row->$name='';
     }
     if(!empty($row)){
       $ci          =&  get_instance();
       $tabla       = "sys_municipios";
       if(is_numeric($row)){
         $row_municipio  = $ci->db->select("*")
                                    ->from($tabla)
                                    ->where('id',$row)->get()->row();
       }else{
         $row_municipio=new stdClass();
         $row_municipio->union_ = "";
         $row='';
       }
     }
     $html = '';
     $html .=  '<input value="'.$row_municipio->union_.'" type="text" class="form-control" id="'.$name.'" placeholder="'.$placeholder.'" maxlength="150" ';
     $html .=  ($require)? 'require="require"':'';
     $html .=  '/>';
     $html	.=	'<input type="hidden" name="'.$name.'" id="content'.$name.'" require="require"  value="'.@$row.'" />';
     $html .=  '<script>
                  $(function(){
                    $( "#'.$name.'" ).autocomplete({
                      source: "'.base_url("Api/get?modulo=maestros&m=municipios&formato=json").'",
                      minLength: 2,
                      change: function (event, ui){
                        if (ui.item===null) {
                          this.value = "";
                          $("#text-alert").text("Por favor seleccione una ciudad válida del listado")
                          $("#myModal").modal("show");
                        }
                      },
                      focus: function( event, ui ) {
                        console.log(ui)
                        $("#content'.$name.'" ).val( ui.item.value );
                        $( "#'.$name.'" ).val( ui.item.label );
                        return false;
                      },
    									select: function( event, ui ) {
    										$("#content'.$name.'" ).val( ui.item.value );
    										$( "#'.$name.'" ).val( ui.item.label );
    										return false;
    									}
                    });
                  })';
    $html .=   '</script>';
     return $html;
   }

   function expedicion2($row,$name,$placeholder='',$require=false){
     if(empty($row)){
       $row=new stdClass();
       $row->$name='';
     }
     $ci   =&  get_instance();
     $tabla  = "sys_municipios";
     $rows = $ci->db->select("*")->from($tabla)->get()->result();
     if(is_object($row)){
       $row = $row->$name;
     }
     if(!empty($row)){
       $ci   =&  get_instance();
       $tabla  = "sys_municipios";
       $rowid = $ci->db->select("*")->from($tabla)->where('id',$row)->get()->row();
     }

     $html = '';
     //  $rowid = $ci->db->select("*")->from($tabla)->where('id',$row)->get()->row();
     $html .=  '<input type="text" class="form-control" id="'.$name.'" placeholder="'.$placeholder.'" maxlength="150"  value="'.@$rowid->union_.'"';
     $html .=  ($require)? 'require="require"':'""';
     $html .=  '/>';
     $html	.=	'<input type="hidden" name="'.$name.'" id="content'.$name.'" require="require"  value="'.@$row.'" />';
     $html .=  ' <script>
               $(function(){
                 var projects = [';
                   foreach($rows as $k => $v){
                     $html .=  '{
                             value: "'.$v->id.'",
                             label: "'.$v->union_.'"

                           },';
                   }

     $html .=  '     ];


                $( "#'.$name.'" ).autocomplete({
                   minLength: 0,
                   source: projects,
                   change: function (event, ui){
                     if (ui.item===null) {
                       this.value = "";
                       $("#text-alert").text("Por favor seleccione una ciudad válida del listado")
                       $("#myModal").modal("show");

                     }
                   },
                   focus: function( event, ui ) {
                     $("#content'.$name.'" ).val( ui.item.value );
                     $( "#'.$name.'" ).val( ui.item.label );
                     return false;
                   },
 									select: function( event, ui ) {

 										$("#content'.$name.'" ).val( ui.item.value );
 										$( "#'.$name.'" ).val( ui.item.label );
 										return false;
 									}
                 });
               });

               $( "#'.$name.'" ).autocomplete();
             </script>
           ';
     return $html;
   }

   function get_municipios(){
     $ci   =&  get_instance();
     $tabla  = "sys_municipios";
     return $ci->db->select("*")->from($tabla)->get()->result();
   }

   function cargo($row,$name,$placeholder='',$require=false){
    if(empty($row)){
      $row=new stdClass();
      $row->$name='';
    }
    //pre($row); return;

    if(is_object($row)){
      $row = $row->cargo;
    }
    if(!empty($row)){
      $ci   =&  get_instance();
      $tabla  = "sys_profesiones";
      $rowid = $ci->db->select("*")->from($tabla)->where('profesion_id',$row)->get()->row();
    }

    $html = '';
    $html .=  '<input type="text" class="form-control" id="'.$name.'" placeholder="'.$placeholder.'" maxlength="150"  value="'.@$rowid->profecion.'"';
      $html .=  ($require)? 'require="require"':'""';
      $html .=  '/>';
    $html	.=	'<input type="hidden" name="'.$name.'" id="content'.$name.'" require="require"  value="'.@$row.'" />';

    $html .=  ' <script>
              $(function(){
                var projects = [';
                  foreach(get_cargo() as $k => $v){
                    $html .=  '{
                      value: "'.$v->id.'",
                      label: "'.$v->profecion	.'",

                          },';
                  }

    $html .=  '     ];
    $( "#'.$name.'" ).autocomplete({
      minLength: 0,
      source: projects,
      change: function (event, ui){
        if (ui.item===null) {
          this.value = "";
          $("#text-alert").text("Por favor seleccione un cargo válido")
          $("#myModal").modal("show");
        }
      },
      focus: function( event, ui ) {
        $("#content'.$name.'" ).val( ui.item.value );
        $( "#'.$name.'" ).val( ui.item.label );
        return false;
      },
      select: function( event, ui ) {

        $("#content'.$name.'" ).val( ui.item.value );
        $( "#'.$name.'" ).val( ui.item.label );
        return false;
      }
    });
  });
   </script>
          ';
    return $html;
   }


   function get_cargo(){
      $ci   =&  get_instance();
      $tabla =  "sys_profesiones";
      $resultado= $ci->db->select("*")->from($tabla)->get()->result();
      return $resultado;
   }

   function MakeEstado($name,$estado=null,$extra=array()){
    $options = array(
      '1'         => 'Activo',
      '0'       => 'Inactivo'
    );
    return form_dropdown($name, $options, $estado,$extra);
  }


  function GetColor($id){
    $ci 	=& 	get_instance();
    $tabla						=	"mae_cliente_joberp";
    return $ci->db->select("*")->from($tabla)->where("id",$id)->get()->row();
  }

  function get_estadoCivil(){
    $ci   =&  get_instance();
    $tabla            = "sys_estado_civil";
   $ci->db->select("*")->from($tabla);
   $options    = $ci->db->get()->result();
   $option     =   array(""=>"Seleccione");
   foreach($options as $v){
     $option[$v->id]   =   $v->Estado;
   }
   return $option;
  }

  function ini_session($user){
    $ci   =&  get_instance();
    $session_id   = md5(date("Y-m-d H:i:s"));
    if(is_object($user)){
      $user->session_id   = $session_id;
      $insert         = $ci->db->insert("sys_session",array( "fecha"=>date("Y-m-d H:i:s"),
                                            "user_id"=>$user->user_id,
                                            "session_id"=>$user->session_id));
    }else if(is_array($user)){
      $user['session_id']   = $session_id;
      $insert         = $ci->db->insert("sys_session",array( "fecha"=>date("Y-m-d H:i:s"),
                                            "user_id"=>$user["user_id"],
                                            "session_id"=>$user["session_id"]));
    }
    if($insert){
      return $user;
    }else{
      return false;
    }
  }

  function set_template_mail($var=array()){
    $ci   =&  get_instance();
    $view = PATH_VIEW.'/Template/Emails/'.$var['view'].'.php';
    if(file_exists($view)){
      return $ci->load->view('Template/Emails/'.$var['view'],$var['data'],TRUE);
    }else{
      return false;
    }
  }

  function columnas($campo){
  	$return		=	'';
  	$lastkey 	= 	count($campo) - 1;
  	$count		=	0;
  	foreach($campo as $k => $v){
  		if($count==$lastkey || $k=='estatus' || $k=='id'){
  			$return		.=	'<th data-columna="'.$k.'" width="30" class="text-center">';
  		}else{
  			$return		.=	'<th data-columna="'.$k.'">';
  		}
  			$return		.=	$v;
  		$return		.=	'</th>';
  		$count++;
  	}
  	return $return;
  }

  function listados($view,$data){
    $ci   =&  get_instance();
    $ci->load->view('Template/Header');
    $ci->load->view('Template/Flash');
    if($ci->uri->segment(1) == "Apanel"){
      $ci->load->view('Template/Apanel/Menu');
    }
    $ci->load->view('Template/Breadcrumb');
    $ci->load->view('Template/'.$view,$data);
    $ci->load->view('Template/Footer');
  }

  function TaskBar($items=array()){
    $ci=&get_instance();
    $iconos = array();
    $iconos['title']      = new stdClass();
    $iconos['title']->url   = current_url();
    $iconos['title']->icono   = (isset($items['name']['icono']))?$items['name']['icono']:'<i class="fas fa-angle-right"></i>';
    $iconos['title']->title   = '';

    $title  = '';
    if(isset($items['name'])){
      if(is_array($items['name'])){
        if(isset($items['name']['title']) && isset($items['name']['url'])){
          $title  .=  $iconos['title']->icono.' '.$items['name']['title'];
        }else{
          $title  .=  $iconos['title']->icono.' '.$items['name']['title'];
        }
        unset($items['name']);
      }
    }

    $iconos['back']       = new stdClass();
    if($ci->agent->referrer() && @$items['back']==TRUE){
      $iconos['back']->url  = $ci->agent->referrer();
    }else{
      $iconos['back']->url  = "ocultar";
    }
    $iconos['back']->icono    = '<i class="fas fa-chevron-circle-left"></i>';
    $iconos['back']->title    = 'Volver Atrás';

    $iconos['impresion']      = new stdClass();
    if(is_numeric($ci->uri->segment(3))){
      $iconos['impresion']->url = $ci->uri->segment(1).'/'.$ci->uri->segment(2).'/'.$ci->uri->segment(3).'/print';
    }else{
      $iconos['impresion']->url = $ci->uri->segment(1).'/'.$ci->uri->segment(2).'/print';
    }

    $iconos['impresion']->icono   = '<i class="fas fa-print"></i>';
    $iconos['impresion']->title   = 'Imprimir Documento';

    $iconos['import']       = new stdClass();
    if(is_numeric($ci->uri->segment(3))){
      $iconos['import']->url  = $ci->uri->segment(1).'/'.$ci->uri->segment(2).'/'.$ci->uri->segment(3).'/import';
    }else{
      $iconos['import']->url  = $ci->uri->segment(1).'/'.$ci->uri->segment(2).'/import';
    }
    $iconos['import']->icono    = '<i class="fas fa-upload"></i>';
    $iconos['import']->title    = 'Importar Documento';

    $iconos['check']        = new stdClass();
    if(is_numeric($ci->uri->segment(3))){
      $iconos['check']->url = $ci->uri->segment(1).'/'.$ci->uri->segment(2).'/'.$ci->uri->segment(3).'/check';
    }else{
      $iconos['check']->url = $ci->uri->segment(1).'/'.$ci->uri->segment(2).'/check';
    }
    $iconos['check']->icono   = '<i class="fas fa-check"></i>';
    $iconos['check']->title   = 'Verificar Documento';

    $iconos['config']       = new stdClass();
    if(is_numeric($ci->uri->segment(3))){
      $iconos['config']->url  = $ci->uri->segment(1).'/'.$ci->uri->segment(2).'/'.$ci->uri->segment(3).'/config';
    }else{
      $iconos['config']->url  = $ci->uri->segment(1).'/'.$ci->uri->segment(2).'/config';
    }
    if(empty($items['config']['icono'])){
      $iconos['config']->icono    = '<i class="fas fa-check"></i>';
    }else{
      $iconos['config']->icono    = $items['config']['icono'];
    }
    $iconos['config']->title    = 'Configuración';
    $iconos['config']->size     = 'modal-lg';
    $iconos['config']->height   = 450;

    $iconos['inbox']        = new stdClass();
    if(is_numeric($ci->uri->segment(3))){
      $iconos['inbox']->url   = $ci->uri->segment(1).'/'.$ci->uri->segment(2).'/'.$ci->uri->segment(3).'/recibir';
    }else{
      $iconos['inbox']->url   = $ci->uri->segment(1).'/'.$ci->uri->segment(2).'/recibir';
    }
    $iconos['inbox']->icono   = '<i class="fas fa-inbox"></i>';
    $iconos['inbox']->title   = 'Recibir Pagos';

    $iconos['anular']     = new stdClass();
    if(is_numeric($ci->uri->segment(3))){
      $iconos['anular']->url  = $ci->uri->segment(1).'/'.$ci->uri->segment(2).'/'.$ci->uri->segment(3).'/anular';
    }else{
      $iconos['anular']->url  = $ci->uri->segment(1).'/'.$ci->uri->segment(2).'/anular';
    }
    $iconos['anular']->icono  = '<i class="fas fa-ban"></i>';
    $iconos['anular']->title  = 'Anular Pagos';

    $iconos['pago']     = new stdClass();
    if(is_numeric($ci->uri->segment(3))){
      $iconos['pago']->url  = $ci->uri->segment(1).'/'.$ci->uri->segment(2).'/'.$ci->uri->segment(3).'/pago';
    }else{
      $iconos['pago']->url  = $ci->uri->segment(1).'/'.$ci->uri->segment(2).'/pago';
    }
    $iconos['pago']->icono  = '<i class="fas fa-dollar"></i>';
    $iconos['pago']->title  = 'Pagar';

    $iconos['pdf']        = new stdClass();
    if(is_numeric($ci->uri->segment(3))){
      $iconos['pdf']->url   = $ci->uri->segment(1).'/'.$ci->uri->segment(2).'/'.$ci->uri->segment(3).'/PDF';
    }else{
      $iconos['pdf']->url   = $ci->uri->segment(1).'/'.$ci->uri->segment(2).'/PDF';
    }
    $iconos['pdf']->icono   = '<i class="fas fa-file-pdf"></i>';
    $iconos['pdf']->title   = 'Documento en PDF';

    $iconos['excel']        = new stdClass();
    if(is_numeric($ci->uri->segment(3))){
      $iconos['excel']->url = $ci->uri->segment(1).'/'.$ci->uri->segment(2).'/'.$ci->uri->segment(3).'/excel';
    }else{
      $iconos['excel']->url = $ci->uri->segment(1).'/'.$ci->uri->segment(2).'/excel';
    }
    $iconos['excel']->icono   = '<i class="fa fa-file-excel" aria-hidden="true"></i>';
    $iconos['excel']->title   = 'Descargar Excel';

    $iconos['mail']       = new stdClass();
    if(is_numeric($ci->uri->segment(3))){
      $iconos['mail']->url  = $ci->uri->segment(1).'/'.$ci->uri->segment(2).'/'.$ci->uri->segment(3).'/mail';
    }else{
      $iconos['mail']->url  = $ci->uri->segment(1).'/'.$ci->uri->segment(2).'/mail';
    }
    $iconos['mail']->icono    = '<i class="far fa-envelope"></i>';
    $iconos['mail']->title    = 'Enviar por Email';

    $iconos['pageleft']     = new stdClass();
    if(is_numeric($ci->uri->segment(3))){
      if($ci->uri->segment(3)>1){
        $left           = $ci->uri->segment(3)- 1;
        $iconos['pageleft']->url  = $ci->uri->segment(1).'/'.$ci->uri->segment(2).'/'.$left.'/'.$ci->uri->segment(4);
        $iconos['pageleft']->icono  = '<i class="fas fa-caret-square-left"></i>';
        $iconos['pageleft']->title  = 'Documento anterior';

      }else{
        $iconos['pageleft']->url  = "ocultar";
        $iconos['pageleft']->icono  = '<i class="fas fa-caret-square-left"></i>';
        $iconos['pageleft']->title  = 'Documento anterior';
      }
    }else{
      $iconos['pageleft']->url  = $ci->uri->segment(1).'/'.$ci->uri->segment(2);
    }


    $iconos['pageright']      = new stdClass();
    if(is_numeric($ci->uri->segment(3))){
      $right  = $ci->uri->segment(3)+1;
      $iconos['pageright']->url = $ci->uri->segment(1).'/'.$ci->uri->segment(2).'/'.$right.'/'.$ci->uri->segment(4);
    }else{
      $iconos['pageright']->url = $ci->uri->segment(1).'/'.$ci->uri->segment(2);
    }
    $iconos['pageright']->icono = '<i class="fas fa-caret-square-right"></i>';
    $iconos['pageright']->title = 'Documento siguiente';

    $iconos['add']        = new stdClass();
    if(is_numeric($ci->uri->segment(3))){
      $iconos['add']->url = $ci->uri->segment(1).'/'.$ci->uri->segment(2).'/'.$ci->uri->segment(3).'/add';
    }else{
      $iconos['add']->url = $ci->uri->segment(1).'/'.$ci->uri->segment(2).'/add';
    }
    $iconos['add']->icono   = '<i class="fa fa-plus"></i>';
    $iconos['add']->title   = 'Agregar Documento';
        $return           = '<div class="row filters">';
        $return           .=  '<div class="col-md-12">';
          $return           .=  '<nav id="submenu" class="navbar navbar-toggleable-md navbar-light bg-faded nav-short p-2">';
            $return           .=  '<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">';
              $return           .=  '<span class="navbar-toggler-icon"></span>';
            $return           .=  '</button>';
            $return           .=  '<a class="navbar-brand">';
              $return           .=  '<h4 class="font-weight-700 text-uppercase orange ">';
                $return           .=  $title;
              $return           .=  '</h4>';
            $return           .=  '</a>';
            $return           .=  '<div class="collapse navbar-collapse" id="navbarNavDropdown">';
              $return           .=  '<div class="btn-group  ml-auto" role="group" aria-label="Small button group">';
                foreach($items as $k => $v){
                  if(is_array($v) && $v){
                    if(isset($v['title'])){
                      $title_link = $v['title'];
                    }else{
                      $title_link = $iconos[$k]->title;
                    }
                    if(isset($v['url'])){
                      $url_link = $v['url'];
                    }else{
                      $url_link = $iconos[$k]->url;
                    }
                    if(isset($v['lightbox'])){
                      $atributos  = 'class="nav-link lightbox '.$k.' " data-type="iframe"';
                    }else if(isset($v['confirm'])){
                      $atributos  = 'class="nav-link confirm" confirm="true" data-title="Deseas anular esta factura?"  data-message="Para continuar pulsa aceptar."';
                    }else if(isset($v['popup'])){
                      $atributos  = 'class="nav-link popup" popup="true" data-title="Popup"';
                    }else{
                      $atributos  = 'class="nav-link '.$k.'"';
                    }
                    if(isset($v['atributo'])){
                      $atributos  = 'class="nav-link" '.$v['atributo'];
                    }
                    if(isset($v['target'])){
                      $atributos  .=  'class="nav-link " target="_blank"';
                    }
                    if(isset($v['id'])){
                      $atributos  .=  'id="'.$v['id'].'"';
                      $contenedor  =  '<div id="Opciones_excel" style="display:none;">
                                <form action="'.current_url().'/mail" method="post">
                                  <table width="100%">
                                    <tr>
                                      <td>
                                        <input id="email" type="email" name="email" placeholder="correo electronico" class="form-control" required="1" />
                                      </td>
                                      <td style="text-align: right;">
                                      <button id="enviar" class="btn btn-primary" type="button" disabled>Enviar</button>
                                      </td>
                                    </tr>
                                  </table>
                                </form>
                              </div>';
                    }
                    if(isset($v['size'])){
                      $atributos  .=  ' data-size="'.$v['size'].'" ';
                    }
                    if(isset($v['size'])){
                      $atributos  .=  ' data-height="'.$v['height'].'" ';
                    }
                    $return           .=  '<a '.$atributos.' title="'.$title_link.'" href="'.$url_link.'" >';
                      if(isset($v['icono'])){
                        $return           .=  $v['icono'];
                      }else{
                        $return           .=  $iconos[$k]->icono;
                      }
                    $return           .=  '</a>';
                  }else{
                    if($iconos[$k]->title=='Imprimir Documento'){
                      $atributos  = 'class="nav-link "';
                    }else if($iconos[$k]->title=='Documento en PDF'){
                      $atributos  = 'class="nav-link " target="_blank" ';
                    }else{
                      if($v==='lightbox'){
                        $atributos  = 'class="nav-link lightbox '.$k.'" data-type="iframe"';
                      }else{
                        $atributos  = 'class="nav-link '.$k.'"';
                      }
                    }
                    if($iconos[$k]->url!='ocultar'){
                      $return           .=  '<a '.$atributos.' class="'.$k.'" title="'.$iconos[$k]->title.'" href="'.$iconos[$k]->url.'" >';
                        $return           .=  $iconos[$k]->icono;
                      $return           .=  '</a>';
                    }
                  }
                }
              $return           .=  '</div>';
            $return           .=  '</div>';
          $return           .=  '</nav>';
        $return           .=  '</div>';
      $return           .=  '</div>';
    $return           .=  @$contenedor;
    if(isset($items['config']['atributo'])){
      $row        =   get_NotificacionEmail(base_url("Utilidades/CorreoNotificacion/SolicitudPlataformas"));
      $hidden     =   array("Modulo"=>$ci->uri->segment(1));
    $return .= '<div class="modal fade" id="OpcionesEmail">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                      <h4 class="modal-title">Configuración envio email</h4>
                      <button type="button" class="close cerrar" data-dismiss="modal">&times;</button>
                  </div>
                  <div class="modal-body" id="form">
                      <form action="'.base_url('Utilidades/ConfigEmail').'" method="post" accept-charset="utf-8">
                    <div class="row">
                      <div class="col-md-7">
                        <div class="container">
                          <div class="form-group">
                            <div class="input-group mt-3">
                              <div class="row col-md-12">
                                  <input type="email" id="correos_notificacion" class="form-control" placeholder="Email" >
                                  <div id="submit" class="btn btn-primary ml-4" style="cursor:pointer">Agregar</div>
                                  <div class="alert alert-danger col-md-12 mt-2" id="message" role="alert" style="display:none;">
                                </div>
                                <div class="col-md-12 mt-1">
                                      <table class="display table table-hover" ordercol=1 order="asc">
                                        <tr>
                                      <th>Correo</th>
                                      <th>Accion</th>
                                        </tr>
                                          <tbody id="correo">';
                                                  if(!empty($row)){
                                                      foreach ($row as $k => $v) {
                                  $return .=' <tr>
                                                  <td>'.$v->correo.'</td>
                                                  <td class="text-center">
                                                      <a href="'.base_url("Utilidades/deleteItem/".$v->id_email).'">
                                                          <i class="fas fa-trash"></i>
                                                      </a>
                                                  </td>
                                              </tr>';
                                                      }
                                                  }
                              $return .=  '</tbody>
                                      </table>
                                  </div>
                              </div>
                              <script type="text/javascript" charset="utf-8" async defer>
                                $(document).ready(function(){
                                  function isValidEmailAddress(emailAddress) {
                                    var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
                                    return pattern.test(emailAddress);
                                    }

                                  $("#id_modelo").attr("name","nombre_modelo");
                                      $("#submit").click(function() {
                                    var valido= true;
                                    var correo = $("#correos_notificacion").val();
                                      console.log(correo);
                                    if ((valido)&&(correo == "")){
                                      valido = false;
                                      $("#message").fadeIn();
                                      $("#message").html("El campo no puede estar vacío");

                                    }

                                        if ((valido)&&(!isValidEmailAddress(correo) )){
                                      valido = false;
                                      $("#message").fadeIn();
                                      $("#message").html("correo no valido");

                                    }
                                    if (valido){
                                                                        $.post("'.base_url("Utilidades/CorreoNotificacion/SolicitudPlataformas").'",{correo:correo}, function($data){
                                        console.log($data);
                                        var $json = JSON.parse($data);
                                        if($json.message){
                                          $("#message").fadeIn();
                                          $("#message").html($json.message);
                                        }
                                        if($json.correo){
                                          eval(agregar_correo_tabla($json));
                                        }
                                        console.log($json);
                                        $("#Usuario").val($json.nickname);
                                      }); //fin post
                                    }

                                      });
                                });
                              </script>

                            </div>';
                $return .='     </div>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                  </div>
              </div>
          </div>';
    }
    return $return;
  }
?>
