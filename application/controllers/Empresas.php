<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Empresas extends CI_Controller {

	/*DEBEMOS CREAR LLAMADOS A JS Y CSS CON LA FUNCION SET CSS O JS SE LA PASA
	UN ARRAY CON LOS ARCHIVOS QUE NECESITA SIN LA EXTENSIÓN*/

	var $util,$Apanel,$Rows,$Row;

	public function __construct(){
    parent::__construct();
		$this->util 		= 	new Util_model();
		$this->Breadcrumb=$this->uri->segment_array();
		$this->user=$this->session->userdata('User');
		$this->Apanel=true;
		if(ENVIRONMENT=='development'){
			$this->util->set_js([	"bootstrap.min.js",
														"jquery.smartWizard.min.js",
														"jquery-ui.js",
														"forms.js",
														"date-picker-es.js",
														"jquery.datetimepicker.full.min.js"]);
			$this->util->set_css(["yamm.css",
														"smart_wizard_theme_circles.css",
														"jquery-ui.css",
														"jquery.datetimepicker.min.css"]);
		}else{
			$this->util->set_js([	"bootstrap.min.js",
														"jquery.smartWizard.min.js",
														"jquery-ui.js",
														"forms.js",
														"date-picker-es.js",
														"jquery.datetimepicker.full.min.js"]);
			$this->util->set_css(["yamm.css",
														"smart_wizard_theme_circles.css",
														"jquery-ui.css",
														"jquery.datetimepicker.min.css"]);
		}
		if(empty($this->user) && $this->uri->segment(2)!='Login'){
			redirect(base_url("Autenticacion/login"));	return;
		}
		$this->load->model("Empresas_model");
		$this->Empresas	= 	new Empresas_model();
  }

	public function index(){
		$this->util->set_title("Empresas");
		$this->Empresas->getEmpresa();
		$this->Rows	=	$this->Empresas->result;
		$this->campos=array("avatar"=>"Avatar",
												"concat_nombres"=>"Nombre Legal / Comercial ",
												"login"=>"Usuario",
												"concat_contacto"=>"Contacto",
												"edit"=>"Acción");
		$this->util->view("Empresas/List");
	}

	public function Add(){
		if(post()){
			$this->Empresas->set(post());
				$this->util->skipHeader();
				$this->util->view("Confirmacion");
			return;
		}
		$this->util->set_title("Empresas");
		$this->Row	=	$this->Empresas->getEmpresa_X_Id($this->uri->segment(3));
		$this->util->view("Empresas/Add");
	}

}
