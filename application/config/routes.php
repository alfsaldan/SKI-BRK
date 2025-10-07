<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| URI ROUTING
|--------------------------------------------------------------------------
*/

$route['default_controller'] = 'Administrator/index';            // Halaman pertama langsung ke login
$route['dashboard']          = 'Administrator/index'; // Dashboard administrator
$route['login']              = 'Auth/index';       // Halaman login
$route['logout']             = 'Auth/logout';      // Logout
// Route tambahan untuk role lain (pegawai misalnya)
$route['pegawai']            = 'Pegawai/index';    
$route['administrator_renstra/kpi_indikatorkinerja'] = 'Administrator_Renstra/kpi_indikatorKinerja';
$route['administrator_renstra/kpi_penilaiankinerja'] = 'Administrator_Renstra/kpi_penilaianKinerja';


$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

