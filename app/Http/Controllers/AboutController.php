<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class AboutController extends Controller {

    public function index() {

        // Get list of loaded extensions.
        $modulesList = '<table border=2 width="200" align="center"><tr><td align="center"> Loaded Modules </td></tr><tr><td>';
        $extArray = get_loaded_extensions();
        foreach($extArray as $module) {
            $modulesList .= $module . ',  ';
        }
        $modulesList .= '</td></tr></table>';

        // Get phpinfo.
        ob_start();
        phpinfo();
        $phpinfoData = ob_get_contents();
        ob_clean();

        // Return everything.
        return view('about') . '<br>' . $modulesList . '<br>' . $phpinfoData;
    }
}