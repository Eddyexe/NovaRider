<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Motocicleta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function data(Request $request)
    {
        $tipo = $request->query('tipo', 'clientes');

        if ($tipo === 'clientes') {
            $data = Cliente::withCount('motocicletas')
                ->where('estadoA', true)
                ->orderBy('primer_nombre')
                ->get();
        } else {
            $data = Motocicleta::with('cliente')
                ->where('estadoA', true)
                ->orderBy('placa')
                ->get();
        }

        return response()->json([
            'tipo' => $tipo,
            'data' => $data
        ]);
    }

    public function exportPdf(Request $request)
    {
        $tipo = $request->query('tipo', 'clientes');
        $titulo = $tipo === 'clientes' ? 'Reporte de Clientes y Motocicletas' : 'Reporte de Motocicletas Registradas';

        if ($tipo === 'clientes') {
            $items = Cliente::withCount('motocicletas')
                ->where('estadoA', true)
                ->orderBy('primer_nombre')
                ->get();
        } else {
            $items = Motocicleta::with('cliente')
                ->where('estadoA', true)
                ->orderBy('placa')
                ->get();
        }

        $pdf = Pdf::loadView('reportes.pdf', [
            'titulo' => $titulo,
            'tipo' => $tipo,
            'items' => $items,
            'fecha' => now()->format('d/m/Y H:i')
        ]);

        return $pdf->download("reporte_{$tipo}_" . now()->format('YmdHis') . ".pdf");
    }
}
