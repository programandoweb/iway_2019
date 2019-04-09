<?php
  /**/
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
?>
