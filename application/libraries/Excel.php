<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . '../vendor/autoload.php';  // pastikan path vendor benar

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Excel {
    public function load($filePath) {
        return IOFactory::load($filePath);
    }

    public function writer($spreadsheet, $type = 'Xls') {
        return IOFactory::createWriter($spreadsheet, $type);
    }
}
