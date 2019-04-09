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

?>
