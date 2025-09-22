<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| URI ROUTING
|--------------------------------------------------------------------------
*/

$route['default_controller'] = 'SuperAdmin/index';            // Halaman pertama langsung ke login
$route['dashboard']          = 'SuperAdmin/index'; // Dashboard superadmin
$route['login']              = 'Auth/index';       // Halaman login
$route['logout']             = 'Auth/logout';      // Logout
// Route tambahan untuk role lain (pegawai misalnya)
$route['pegawai']            = 'Pegawai/index';    

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

