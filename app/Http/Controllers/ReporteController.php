<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function index()
    {
        // Placeholder hasta que se implementen vistas de reportes
        return response('Reportes - índice', 200);
    }

    public function avanzados()
    {
        return response('Reportes avanzados', 200);
    }

    public function exportar()
    {
        // Aquí podría generarse un archivo; por ahora solo texto
        return response('Exportación de reportes (placeholder)', 200);
    }

    public function admin()
    {
        return response('Reportes admin', 200);
    }
}
