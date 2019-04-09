<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2019, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     testing
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 */
	define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');

switch (ENVIRONMENT){
	case 'development':
		error_reporting(-1);
		ini_set('display_errors', 1);
	break;

	case 'testing':
	case 'production':
		ini_set('display_errors', 0);
		if (version_compare(PHP_VERSION, '5.3', '>=')){
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
		}else{
			error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
		}
	break;

	default:
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'The application environment is not set correctly.';
		exit(1); // EXIT_ERROR
	}

	/*DETECTAR EL AMBIENTE DE TRABAJO*/
	if($_SERVER['HTTP_HOST'] == "localhost"){
		$dominio		=		'http://' . $_SERVER['HTTP_HOST'].'/iway_2019';
		$db_user		=		'root';
		$db_pass		=		'';
		$db_db			=		'iway_db2';
	}else{
		$dominio		=		'https://' . $_SERVER['HTTP_HOST'].'/iway';
		$db_user		=		'iway_db';
		$db_pass		=		'fdqejH8h';
		$db_db			=		'iway_db2';
	}

	/*HEADER*/
	//define('DOMINIO',$_SERVER["REQUEST_SCHEME"].'://base.programandoweb.net/');
	define('DOMINIO',$dominio);
	define('SEO_KEYWORDS',"");
	define('SEO_DESCRIPTION',"");
	define('SEO_TITLE',"Iway®");
	define('SEO_NAME',"Iway®");
	define('SEO_GENERATOR',"@iway");
	/*END HEADER*/

	/*DATABASE*/
	define('DB_PREFIJO','');
	define('DB_USER',$db_user);
	define('DB_PASS',$db_pass);
	define('DB_DATABASE',$db_db);
	/*EN DATABASE*/

	/*SMTP*/
	define('PROTOCOL'					,	"mail");
	define('SMTP_HOST'				,	"workplace.com.co");
	define('SMTP_PORT'				,	"465");

	define('SMTP_TIMEOUT'			,	"7");
	define('SMTP_USER'				,	"sofia@workplace.com.co");
	define('SMTP_PASS'				,	"8w7!G.rpPsTH");
	define('CHARSET'				,	"utf-8");
	define('NEWLINE'				,	"\r\n");
	define('MAILTYPE'				,	"html");
	define('VALIDATION'				,	TRUE);
	define('FROM_NAME'				,	"WebcamPlus®");
	define('FROM_EMAIL'				,	SMTP_USER);
	/*END SMTP*/

	/*FILES*/
	define('IMG',DOMINIO."/images/");
	define('CSS',DOMINIO."/template/css/");
	define('JS',DOMINIO."/template/js/");
	define('PATH_IMG',dirname(__FILE__)."/images/");
	/*END FILES*/

	/*OTHERS*/
	define('ELEMENTOS_X_PAGINA',150);
	define('TEMPLATE',"template");
	define('PATH_BASE',dirname(__FILE__).'/');
	define('PATH_APP',PATH_BASE.'application/');
	define('PATH_CONTROLLERS',PATH_APP.'controllers');
	define('PATH_MODEL',PATH_APP.'models');
	define('PATH_VIEW',PATH_APP.'views');
	/*END OTHERS*/

	/*MÚDLOS ACTIVO*/
	//define('SESSION_TIME',900);
	define('SESSION_TIME',3600);
	define('MODULO_X_DEFAULT',"Apanel");

	$system_path = 'system';
	$application_folder = 'application';
	$view_folder = '';
	if (defined('STDIN')){
		chdir(dirname(__FILE__));
	}
	if (($_temp = realpath($system_path)) !== FALSE){
		$system_path = $_temp.DIRECTORY_SEPARATOR;
	}else{
		$system_path = strtr(
			rtrim($system_path, '/\\'),
			'/\\',
			DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
		).DIRECTORY_SEPARATOR;
	}
	if ( ! is_dir($system_path)){
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'Your system folder path does not appear to be set correctly. Please open the following file and correct this: '.pathinfo(__FILE__, PATHINFO_BASENAME);
		exit(3); // EXIT_CONFIG
	}
	define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
	define('BASEPATH', $system_path);
	define('FCPATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
	define('SYSDIR', basename(BASEPATH));
	if (is_dir($application_folder)){
		if (($_temp = realpath($application_folder)) !== FALSE){
			$application_folder = $_temp;
		}else{
			$application_folder = strtr(
				rtrim($application_folder, '/\\'),
				'/\\',
				DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
			);
		}
	}elseif (is_dir(BASEPATH.$application_folder.DIRECTORY_SEPARATOR)){
		$application_folder = BASEPATH.strtr(
			trim($application_folder, '/\\'),
			'/\\',
			DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
		);
	}else{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'Your application folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
		exit(3); // EXIT_CONFIG
	}
	define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);
	if ( ! isset($view_folder[0]) && is_dir(APPPATH.'views'.DIRECTORY_SEPARATOR)){
		$view_folder = APPPATH.'views';
	}
	elseif (is_dir($view_folder)){
		if (($_temp = realpath($view_folder)) !== FALSE){
			$view_folder = $_temp;
		}else{
			$view_folder = strtr(
				rtrim($view_folder, '/\\'),
				'/\\',
				DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
			);
		}
	}elseif (is_dir(APPPATH.$view_folder.DIRECTORY_SEPARATOR)){
		$view_folder = APPPATH.strtr(
			trim($view_folder, '/\\'),
			'/\\',
			DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
		);
	}else{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'Your view folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
		exit(3); // EXIT_CONFIG
	}
	define('VIEWPATH', $view_folder.DIRECTORY_SEPARATOR);
require_once BASEPATH.'core/CodeIgniter.php';
