<?php

defined('BASEPATH') OR exit('No direct script access allowed');
class Empresas_model extends CI_Model {

	var $fields,$result,$where,$total_rows,$pagination,$search;

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
							->where("t1.estado",1)
							->group_by("t1.empresa_id");
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
				$this->result	=	foreach_edit($result,$totalquery->row()->total);
	}

	public function getEmpresa_X_Id($empresa_id){
		$this->db->select("*")->from("mae_cliente_joberp t1")->join("usuarios t2","t1.empresa_id = t2.empresa_id","left")->where("t1.empresa_id",1);
		if($this->user->type_id>1){
			$this->db->where("t2.user_id",$this->user->type_id);
		}
		$query	=	$this->db->get();
		return $query->row();
	}

  public function set($var){
        $tabla      =       "mae_cliente_joberp";
        $tabla2     =       "usuarios";
        if(isset($var["empresa_id"]) && !empty($var["empresa_id"]) && isset($var['user_id'])&& !empty($var['user_id'])){
            $user_id = $var['user_id'];
            unset($var['user_id']);
            $email                  =   $var['email'];
            /*$insert2['responsable_id']  =   $var['responsable_id'];
            unset($var['responsable_id']);*/
            $insert2['username'] = $var['username'];
            $insert2['primer_nombre'] = $var['primer_nombre'];
            $insert2['segundo_nombre'] = $var['segundo_nombre'];
            $insert2['primer_apellido'] = $var['primer_apellido'];
            $insert2['segundo_apellido'] = $var['segundo_apellido'];
            $insert2['tipo_identificacion']=$var['tipo_identificacion'];
            $insert2['nombre_legal']= $var['nombre_legal'];
            $insert2['nombre_comercial']= $var['nombre_comercial'];
            $insert2['regimen_empresa']= $var['regimen_empresa'];
            $insert2['naturaleza']= $var['naturaleza'];
            $insert2['rol_id']   = $var['rol_id'];
            $insert2['email_user']= $email;
            $insert2['ciudad_expedicion'] = $var['ciudad_expedicion'];
            $insert2['cargo'] = $var['cargo'];
            $insert2['numero_identificacion'] = $var['numero_identificacion'];
            $insert2['divisa_oficial'] = $var['divisa_oficial'];
            $insert2['documento_moneda_extranjera'] = $var['documento_moneda_extranjera'];
            $insert2['pagina_web'] = $var['pagina_web'];
            unset($var['nombre_usuario']);
         // $insert2['nombre'] = $var['representante_legal'];
            unset($var['representante_legal']);
            unset($var['redirect']);
            unset($var['username']);
            unset($var['identificacion']);
            unset($var['nombre']);
            unset($var['identificacion_ext']);
            unset($var['primer_nombre']);
            unset($var['segundo_nombre']);
            unset($var['primer_apellido']);
            unset($var['segundo_apellido']);


            $insert2['documento'] = $var['documento'];
            unset($var['documento']);
            $insert2['rol_id']   = $var['rol_id'];
            unset($var['rol_id']);
            $var['fecha_registro'] = date('Y-m-d');
            $var['responsable_creacion']=$this->user->user_id;
            $this->db->where("empresa_id", $var["empresa_id"]);
            if($this->db->update($tabla, $var)){
                //logs($insert2['responsable_id'],2,"mae_clientes_joberp,usuarios",$var['id'],"update_Empresas","1",$var);
                $this->db->where("user_id", $user_id);
                if($this->db->update($tabla2, $insert2)){
                    $response = "La empresa ha sido modificada";
                }else{
                    $response = "La empresa ha sido modificada, pero el usuario asociado a la empresa no fue modificado por favor consulte al administrador de sistema";
                }
            }
        }else{
            unset($var["id"]);
            unset($var["user_id"]);
            $email                  =   $var['email'];
            $pass                   =   explode("@",$var['email']);
            $insert2['token']           =   md5(date("H:i:s Y-M-d"));
            $password               =   $pass[0].rand(1000,50000);
           // $insert2['responsable_id']  =   $var['responsable_id']
            $insert2['login'] = $var['login'];
            $insert2['email']= $email;
            unset($var['nombre_usuario']);
            /*$insert2['nombre'] = $var['representante_legal'];
            $var['representante_legal'] = id_representante();*/
            $insert2['type_id']   = $var['rol_id'];
            $insert2['login'] = $var['login'];
            $nombre = $insert2['primer_nombre'] = $var['primer_nombre'].' '.$insert2['segundo_nombre'] = $var['segundo_nombre'].' '.$insert2['primer_apellido'] = $var['primer_apellido'].' '.$insert2['segundo_apellido'] = $var['segundo_apellido'];
            //$insert2['ciudad_expedicion'] = $var['ciudad_expedicion'];
            //$insert2['tipo_identificacion']=$var['tipo_identificacion'];
            //$insert2['nombre_legal']= $var['nombre_legal'];
            //$insert2['nombre_comercial']= $var['nombre_comercial'];
            //$insert2['regimen_empresa']= $var['regimen_empresa'];
            //$insert2['naturaleza']= $var['naturaleza'];
            //$insert2['cargo'] = $var['cargo'];
            //$insert2['numero_identificacion'] = $var['numero_identificacion'];
            //$insert2['divisa_oficial'] = $var['divisa_oficial'];
            //$insert2['documento_moneda_extranjera'] = $var['documento_moneda_extranjera'];
            //$insert2['pagina_web'] = $var['pagina_web'];
            unset($var['rol_id']);
            unset($var['redirect']);
            unset($var['type']);
            unset($var['nombre']);
            unset($var['login']);
            unset($var['identificacion']);
            unset($var['identificacion_ext']);
            unset($var['primer_nombre']);
            unset($var['segundo_nombre']);
            unset($var['primer_apellido']);
            unset($var['segundo_apellido']);

            $var["id_representante_legal"] = id_representante();
            //$insert2['documento'] = $var['documento'];
            unset($var['documento']);
            $pass					=	'contrasena';
            $var['fecha_registro'] = date('Y-m-d');
            //$var['estado'] = 1;
            $var['responsable_creacion']=$this->user->user_id;

            if($this->db->insert($tabla, $var)){
                $insert2['empresa_id'] = $this->db->insert_id();
               // logs($insert2['responsable_id'],2,"mae_clientes_joberp",$insert2['empresa_id'],"Empresas","1",$var);
                $insert2['estado'] = 1 ;
                $insert2['password']    =   encriptar($password);
                if($this->db->insert($tabla2, $insert2)){
                    send_mail(array(
                                        "recipient"=>$email,
                                        "subject"=>"Bienvenido a nuestro sistema",
                                        "body"=>$this->load->view('Template/Emails/bienvenida',array("userType"=>"Representante legal","userPassword"=>$password,"userEmail"=>$email,"userName"=>$nombre,"userUsuario"=>$insert2['login'],"href"=>DOMINIO),TRUE),
                                        ));
                    $response = "La empresa ha sido creada";
                }else{
                    $response = "La empresa ha sido creada, pero el usuario asociado a la empresa no fue generado por favor consulte al administrador de sistema";
                }
            }else{
                $response = "No se pudieron insertar los datos. Por favor contacte al administrador de sistemas";
            }
        // $this->db->insert('mytable', $data);
        }
        return $response;
    }

    public function get($var){

    }
}
?>
