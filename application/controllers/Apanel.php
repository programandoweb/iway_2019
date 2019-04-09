<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Apanel extends CI_Controller {

	/*DEBEMOS CREAR LLAMADOS A JS Y CSS CON LA FUNCION SET CSS O JS SE LA PASA
	UN ARRAY CON LOS ARCHIVOS QUE NECESITA SIN LA EXTENSIÓN*/

	var $util;

	public function __construct(){
    parent::__construct();
		$this->util 		= 	new Util_model();
		$this->load->library('ControllerList');
		$this->Menu=$this->controllerlist->getControllers();
		$this->Breadcrumb=$this->uri->segment_array();
		$this->user=$this->session->userdata('User');
		if(empty($this->user) && $this->uri->segment(2)!='Login'){
			redirect(base_url("Autenticacion/login"));	return;
		}
  }

	public function index(){
		$this->util->set_title("Apanel - ".SEO_TITLE);
		$this->load->view('welcome_message');
	}

	public function login(){
		$this->util->set_title("Apanel - ".SEO_TITLE);
		$this->load->view('welcome_message');
	}

}
