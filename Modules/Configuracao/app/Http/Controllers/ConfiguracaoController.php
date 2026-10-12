<?php

namespace Modules\Configuracao\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConfiguracaoController extends Controller
{
    public function index(){
        return view('configuracao::index');
    }
}
