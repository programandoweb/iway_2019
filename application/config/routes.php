<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$route['Api/(get|post|push|delete)']  = 'Api/apirequest';
$route['install'] = 'Main/Install';
$route['default_controller'] = 'Apanel';
$route['404_override'] = 'Autenticacion/error';
$route['translate_uri_dashes'] = FALSE;
