<?php

defined('BASEPATH') OR exit('No direct script access allowed');
class Empresas_model extends CI_Model {

	var $fields,$result,$where,$total_rows,$pagination,$search;

	public function lista(){
		$tabla	=	"mae_cliente_joberp t1";
	}

	public function getEmpresa(){
		$tabla	=	"mae_cliente_joberp t1";
		$tabla2	=	"usuarios t2";
		$this->db->select('SQL_CALC_FOUND_ROWS t1.empresa_id', false);
		$this->db->select("	concat(nombre_legal,' ',nombre_comercial) as concat_nombres,
												login,
												concat(telefono, ' ', celular , '<br/>',direccion) as concat_contacto,
												user_id,
												user_id as id,
												'iway' as edit")
							->from($tabla)
							->join($tabla2,"t1.empresa_id = t2.empresa_id","left")
							->where("t1.estado",1);

		$search=get("search");
		if(!empty($search["value"])){
			$this->db->like("nombre_legal",$search["value"]);
		}

		$this->db->group_by("t1.empresa_id")
							->limit(get("length"),get("start"));
							//->where("t1.empresa_id>",1);
        if($this->uri->segment(3)){
            $this->db->where("t1.id",$this->uri->segment(3));
        }
				if($this->user->type_id <> 1){
            $this->db->where("t1.empresa_id",$this->user->empresa_id);
        }
        $query	=	$this->db->get();
				$rows		=	$query->result();
				$result	=	array();
				foreach ($rows as $key => $value) {
					$result[$key]	=	$value;
					$result[$key]->avatar=avatar($value->user_id);
					$result[$key]->concat_nombres=$value->concat_nombres;
				}

				$totalquery = $this->db->query('SELECT FOUND_ROWS() as total;');
				return $this->result	=	foreach_edit($result,$totalquery->row()->total);
	}

	public function getEmpresa_X_Id($empresa_id){
		$this->db->select("*")->from("mae_cliente_joberp t1")->join("usuarios t2","t1.empresa_id = t2.empresa_id","left")->where("t1.empresa_id",$empresa_id);
		if($this->user->type_id>1){
			$this->db->where("t2.user_id",$this->user->type_id);
		}
		$query	=	$this->db->get();
		return $query->row();
	}

  public function set($var){
    $tabla      =   "mae_cliente_joberp";
    $tabla2     =   "usuarios";
		$return			=		true;
		$copiar_roles	=	false;

		/*
			listo los datos de una tabla para luego limpiarlos
			con la function post() y los meto en un array
			para luego insertarlos o actualizarlos
		*/

		/*primer paso, proceso la empresa*/
		$insert=[];
		$campos			=		campos($tabla);
		foreach ($campos as $campo) {
			$insert[$campo]	=	post($campo);
		}
    if(isset($var["empresa_id"]) && !empty($var["empresa_id"])){
			$this->db->where("empresa_id",$var["empresa_id"]);
			if(!$this->db->update($tabla,$insert)){
				$return=false;
			}
			$empresa_id	=	$var["empresa_id"];
    }else{
			$insert["cargo"]	=	0;
			$insert["fecha_registro"]	=	date("Y-m-d");
			if(!$this->db->insert($tabla,$insert)){
				$return=false;
			}
			$empresa_id	=	$this->db->insert_id();
    }
		/*segundo, proceso el usuario asociado a la empresa*/
		$insert_usuario=[];
		$campos			=		campos($tabla2);
		foreach ($campos as $campo) {
			$insert_usuario[$campo]	=	post($campo);
		}
		$insert_usuario["empresa_id"]=$empresa_id;
		$insert_usuario["token"]=token();
		/*	estos datos son para el usuario que no pertenece a la empresa id 1 que
				son root		*/
		if($empresa_id>1){
			$insert_usuario["type_id"]=2;
		}else{
			$insert_usuario["type_id"]=1;
		}
		if(isset($var["user_id"]) && !empty($var["user_id"])){
			unset($insert_usuario["password"]);
			$this->db->where("user_id",$var["user_id"]);
			if(!$this->db->update($tabla2,$insert_usuario)){
				$return=false;
			}
			$user_id	=	$var["user_id"];
    }else{
			$insert_usuario["password"]	=	md5($insert_usuario["login"].'123');
			if(!$this->db->insert($tabla2,$insert_usuario)){
				$return=false;
			}
			$user_id	=	$this->db->insert_id();
			$copiar_roles=true;
    }
		/*
				Una vez ingresado el usuario, si es un insert, procedemos a crearle
				los respectivos privilegios
		*/
		if($copiar_roles){
			copiar_roles($user_id,$empresa_id);
		}
    return $return;
  }

  public function get($var){

  }
}
?>
