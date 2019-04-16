<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Usuarios_model extends CI_Model {

	var $fields,$result,$where,$total_rows,$pagination,$search;

	public function SearchUser(){
		$list		=		get("list",false);
		$query	=		get("query");
		$tabla 	= 	"usuarios t1";
		$this->db->select("login");
		$this->db->from($tabla);
		$this->db->where('login',$query);
		if($list){
			$rows=$this->db->get()->result();
			return (!empty($rows)?array("response"=>$rows):array("response"=>"NULL"));
		}else{
			$row=$this->db->get()->row();
			return (!empty($row)?array("response"=>$row):array("response"=>"NULL"));
			//return (!empty($row)?$row:array());
		}
	}

	public function logout(){
		$this->user=$this->session->userdata('User');
		destruye_session($this->user);
		$this->session->unset_userdata('User');
		$this->session->sess_destroy();
		if ($this->input->is_ajax_request()) {
			return array(	"message"=>"Hasta pronto, ".SEO_TITLE,
										"redirect"=>base_url("Autenticacion/login"));
		}else{
			redirect(base_url());
		}
	}

	public function login(){
		$data 	= 	$this->db->select('	user_id,
																		empresa_id,
																		type_id,
																		password,
																		estado')
													->from("usuarios t1")
													->where('t1.login',post('login'))
													->get()
													->row();
		if(!empty($data)){
			if($data->type_id == 1){
				if(md5(post('password')) == $data->password){
					$this->db->where("user_id",$data->user_id);
					$data->password = encriptar(post('password'));
					$update['password'] = $data->password;
					$this->db->update("usuarios",$update);
				}
			}
			if(desencriptar($data->password)==post('password')){
				if($data->estado==0){
						return array("message"=>"Error: Esta cuenta se encuentra inactiva, consulte con el administrador");
				}
				$session  = $this->db->select('*')
															->from("sys_session")
															->where('user_id',$data->user_id)
															->get()
															->row();
				if(empty($session)){
					unset($data->password);
					if($data->type_id == 1){
						$data->menu   = menu();
					}else{
						//$data->menu   = menu_usuarios($data->rol_id,$data->empresa_id);
					}
					if($session   = ini_session($data)){
						$this->set_session_login($session);
						return array(	"message"=>"Bienvenido a ".SEO_TITLE,
													"redirect"=>base_url("Apanel"));
					}else{
						return false;
					}
				}else{
					$data->session_id   = md5(date("Y-m-d H:i:s"));
					if($this->db->where("user_id",$data->user_id)->update("sys_session",array("fecha"=>date("Y-m-d H:i:s"),"session_id"=>$data->session_id))){
						$this->set_session_login($data);
						return array(	"message"=>"Ya existe otra sesión abierta con este usuario y será eliminada",
													"redirect"=>base_url("Apanel"));
					}else{
						return array("message"=>"Ha ocurrido un error por favor contacte al administrador de sistemas");
					}
				}
			}else{
	        return array("message"=>"La contraseña es incorrecta");
	    }
		}else{
			return array("message"=>"Error usuario no existe o clave es incorrecta");
		}
	}

	private function set_session_login($data){
		$this->session->set_userdata(array('User'=>$data));
	}
	public function get_all2(){
		$tabla=  "mae_cliente_joberp t1";
		$tabla2	="usuarios t2";
		$tabla3=  "sys_roles t3";
		$this->db->select('t1.*,t2.*, t3.*')->from($tabla)
							->join($tabla2,"t1.empresa_id = t2.empresa_id","left")
							->join($tabla3,"t2.type_id = t3.type_id","left")
							->where("t2.estado",1);
		if($this->user->type_id <> 1){
			$this->db->where("t1.empresa_id",$this->user->empresa_id);
		}
		$this->result["Activos"]=$this->db->get()->result();

		$tabla=  "mae_cliente_joberp t1";
		$tabla2	="usuarios t2";
		$tabla3=  "sys_roles t3";
		$this->db->select('t1.*,t2.*, t3.*')->from($tabla)
							->join($tabla2,"t1.empresa_id = t2.empresa_id","left")
							->join($tabla3,"t2.type_id = t3.type_id","left")
							->where("t2.estado",0);
		if($this->user->type_id <> 1){
			$this->db->where("t1.empresa_id",$this->user->empresa_id);
		}
		$this->result["Inactivos"]=$this->db->get()->result();
	}
}
?>
