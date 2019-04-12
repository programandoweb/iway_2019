<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Autenticacion extends CI_Controller {

	/*DEBEMOS CREAR LLAMADOS A JS Y CSS CON LA FUNCION SET CSS O JS SE LA PASA
	UN ARRAY CON LOS ARCHIVOS QUE NECESITA SIN LA EXTENSIÓN*/

	public function __construct(){
		parent::__construct();
		$this->load->library('CI_Minifier');
		$this->util 		= 	new Util_model();
  }

	public function index(){
		$this->load->view('welcome_message');
	}

	public function login(){
		if(ENVIRONMENT=='development'){
			$this->util->set_js(["jquery.pgrw.js","bootstrap.min.js"]);
		}else{
			$this->util->set_js(["jquery.pgrw.min.js","bootstrap.min.js"]);
		}
		$this->util->view("Autenticacion/login");
	}

	public function salir(){
		destruye_session($this->user);
		$this->session->unset_userdata('User');
		$this->session->sess_destroy();
		if ($this->input->is_ajax_request()) {
			$this->Response 		=			array(	"message"	=>	"Cierre de sesión satisfactorio, será redirigido",
												"code"		=>	"200");
			echo answers_json($this->Response);
		}else{
			redirect(base_url());
		}
	}

	public function error(){
		$this->util->view("Error_NoView");
	}

}
