<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/* */

class Util_model extends CI_Model {

	var $dominio,$current_url,$title,$description,$keywords,$author,$extra,$app_id,$site_name,$url,$image,$js,$css,$skipHeader,$thirdParty;

	public function __construct(){
		$this->dominio=DOMINIO;
		$this->title=SEO_TITLE;
		$this->description=SEO_DESCRIPTION;
		$this->keywords=SEO_KEYWORDS;
		$this->author=SEO_GENERATOR;
		$this->current_url=current_url();
		$this->extra='';
    $this->app_id='';
    $this->css=$this->js=$this->site_name='';
		$this->thirdParty	=	"";
		$this->skipHeader =	false;
	}

	public function get_header(){
    $return = '<base href="'.$this->dominio.'">';
    $return .= '<link rel="canonical" href="'.$this->dominio.'">';
    $return .= '<link rel="shortcut icon" href="'.$this->dominio.'/images/favicon.png" type="image/x-icon">';
    $return .= '<link rel="icon" href="'.$this->dominio.'/images/favicon.png" type="image/x-icon">';
    $return .= '<link rel="alternate" hreflang="es" href="'.$this->dominio.'">';
    $return .= '<link rel="author" href="https://plus.google.com/u/0/+LcdoJorgeM%C3%A9ndez/about">';
    $return .= '<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">';
    $return .= '<title>'.$this->title.'</title>';
    $return .= '<meta name="description" content="'.$this->description.'">';
    $return .= '<meta name="keywords" content="'.$this->keywords.'">';
    $return .= '<meta name="author" content="'.$this->author.'">';
    $return .= '<meta name="googlebot" content="index, follow">';
    $return .= '<meta name="robots" content="index, follow">';
    $return .= '<meta name="distribution" content="global">';
    $return .= '<meta name="audience" content="all">';
    $return .= '<meta property="og:type" content="website">';
    $return .= '<meta property="fb:app_id" content="'.$this->app_id.'">';
    $return .= '<meta property="og:url" content="'.$this->url.'">';
    $return .= '<meta property="og:image" content="'.$this->image.'">';
    $return .= '<meta property="og:site_name" content="'.$this->site_name.'">';
    $return .= '<meta property="og:title" content="'.$this->title.'">';
    $return .= '<meta property="og:description" content="'.$this->description.'">';
    // $return .= '<link rel="stylesheet" href="'.CSS.'bootstrap.min.css">';
		// //$return .= '<link rel="stylesheet" href="'.CSS.'bootstrap-material.css">';
		// $return .= '<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">';
		// //$return .= '<link rel="stylesheet" href="'.CSS.'fontawesome-free-5.7.2-web/css/all.min.css">';
    // $return .= '<link rel="stylesheet" href="'.CSS.'pgrw.css">';
    // $return .= '<script src="'.JS.'jquery-3.3.1.min.js"></script>';
    // //$return .= '<script src="'.JS.'popper.min.js" async></script>';
    // //$return .= '<script src="'.JS.'bootstrap.min.js" async></script>';
    // $return .= '<script src="'.JS.'pgrw.js"></script>';
		// $return .= $this->css;
		// $return .= $this->js;
		$return .= '<link rel="stylesheet" href="'.CSS.'bootstrap.min.css">';
		$return .= '<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">';
		$return .= '<link rel="stylesheet" href="'.CSS.'pgrw.css">';
		$return .= '<script src="'.JS.'jquery-3.3.1.min.js"></script>';
		$return .= '<script src="'.JS.'pgrw.js"></script>';
		$return .= $this->css;
		$return .= $this->js;
		$return .= $this->thirdParty;
		$return .= '<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">';
		$return .= '<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>';
    return $return;
	}


	public function get_footer(){
		$return="";
		return $return;
	}

  public function view($view,$breadcrumb=false){
		//pre($this->skipHeader);
		if(!$this->input->is_ajax_request()){
      $this->load->view('Template/Header',array("header"=>$this->template_header()));
      $this->load->view('Template/Flash');
    	if($breadcrumb){
        $this->load->view('Template/Breadcrumb');
    	}
    }
    if(file_exists(PATH_VIEW.'/Template/'.$view.'.php')){
  		$this->load->view('Template/'.$view);
  	}else{
  		$this->load->view('Template/Error_NoView',array("View"=>$view));
  	}
    if(!$this->input->is_ajax_request()){
      $this->load->view('Template/Footer',array("footer"=>$this->template_footer()));
    }
  }

  public function template_header($return=false){
    $header = $this->get_header();
		$loading=	IMG."logo-gif.gif";
    $html 	=	file_get_contents(PATH_BASE.TEMPLATE.'/header.php');
		if($return){
			echo 	str_replace(array("{header}","{loading}"), array($header,$loading),$html);
    }else {
			if(@$this->Apanel && get("view")!="iframe" && !$this->skipHeader){
				$html	=	$html.$this->load->view("Template/Menu",array(),true);
			}
      return 	str_replace(array("{header}","{loading}"), array($header,$loading),$html);
    }
  }

  private function template_footer($return=false){
    $footer = $this->get_footer();
    $html 	=	file_get_contents(PATH_BASE.TEMPLATE.'/footer.php');
    if($return){
      echo 	str_replace(array("{footer}"), array($footer),$html);
    }else {
      return 	str_replace(array("{footer}"), array($footer),$html);
    }
  }

	public function set_thirdParty($array){
		$thirdParty	=	"";
		foreach ($array as $key => $value) {
			switch($key){
        case "js":
					if(is_array($value)){
						foreach ($value as $v) {
							$thirdParty	.= '<script async src="'.THIRDPARTY.$v.'.js"></script>';
						}
					}else{
						$thirdParty	.= '<script async src="'.THIRDPARTY.$value.'.js"></script>';
					}
        break;
        case "css":
					if(is_array($value)){
						foreach ($value as $v) {
							$thirdParty	.= '<link rel="stylesheet" href="'.THIRDPARTY.$v.'.css">';
						}
					}else{
						$thirdParty	.= '<link rel="stylesheet" href="'.THIRDPARTY.$value.'.css">';
					}

        break;
      }
		}
		return $this->thirdParty 		=		$thirdParty;
	}

	public function set_js($array){
		$js	=	'';
		foreach ($array as $key => $value) {
			$js	.=	'<script src="'.JS.$value.'"></script>';
		}
		return $this->js 		=	$js;
	}

	public function set_css($array){
		$css	=	'';
		foreach ($array as $key => $value) {
			$css	.=	'<link rel="stylesheet" href="'.CSS.$value.'">';
		}
		return $this->css 	=	$css;
	}

	public function get_title(){
		return $this->title;
	}

	public function set_title($title){
		return $this->title 	=	$title;
	}

	public function get_description(){
		return $this->description;
	}

	public function set_description($description){
		return $this->description 	=	$description;
	}

	public function get_keywords(){
		return $this->keywords;
	}

	public function set_keywords($keywords){
		return $this->keywords 	=	$keywords;
	}

	public function get_author(){
		return $this->author;
	}

	public function set_author($author){
		return $this->author 	=	$author;
	}

	public function get_extra(){
		return $this->extra;
	}

	public function set_extra($extra){
		return $this->extra 	=	$extra;
	}

	public function skipHeader(){
		return $this->skipHeader 	=	true;
	}

}
?>
