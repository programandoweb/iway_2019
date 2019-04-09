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
		$this->util->set_js(["jquery.pgrw.min.js","bootstrap.min.js"]);
		$this->util->view("Autenticacion/login");
	}

	public function salir(){

	}

}
