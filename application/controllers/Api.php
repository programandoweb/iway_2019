<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

  var $util;

	public function __construct(){
    parent::__construct();
    $this->load->library('CI_Minifier');
    $this->user=$this->session->userdata('User');
  }

	public function index(){
		// /echo json_response(null,200,'',false,true);
	}

	public function apirequest(){
    $this->exec();
	}

	private function  exec(){
    /*PARÁMETROS A PASARLE A REST*/
		$clase	=	get("modulo")."_model";
		$metodo	=	get("m");
    $view 	= PATH_MODEL.'/'.$clase.'.php';
    /*VERIFICO SI EXISTE EL MODELO*/
    if(file_exists($view)){
			/*IMPORTO EL MODELO*/
			$this->load->model($clase);
			/*LLENO UNA VARIABLE CON LA CLASE RESPECTIVA*/
			$clase=new $clase;
			/*CHEQUEO SI EXISTE EL MODELO*/
			if(method_exists($clase,$metodo)){

      	$rows	=	$clase->$metodo();
				switch(get("formato")){
					/*
						POR AHORA ESTE ES EL FORMATO DE RESPUESTA,
						LO DEJO EN UN SWITCH POR SI LUEGO NECESITAMOS OTRO
						FORMATO DE RESPUESTA
					*/
					case 'json':
					default:
						echo json_response($rows,200,get("callback"),get("redirect"));
					break;
				}
			}
		}
	}

}
?>
