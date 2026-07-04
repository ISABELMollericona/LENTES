<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index()
    {
        return view('admin.reportes.index');
    }

    public function ventasPorFecha(Request $request)
    {
        $desde = $request->input('desde', now()->startOfMonth()->format('Y-m-d'));
        $hasta = $request->input('hasta', now()->format('Y-m-d'));

        $data = $this->reportService->ventasPorFecha($desde, $hasta);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function ventasPorCategoria()
    {
        $data = $this->reportService->ventasPorCategoria();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function lentesMasVendidos()
    {
        $data = $this->reportService->lentesMasVendidos();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function usuariosTop()
    {
        $data = $this->reportService->usuariosTopCompras();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function exportar($tipo)
    {
        $data = match ($tipo) {
            'ventas-fecha' => $this->reportService->ventasPorFecha(
                request('desde', now()->startOfMonth()->format('Y-m-d')),
                request('hasta', now()->format('Y-m-d'))
            ),
            'ventas-categoria' => $this->reportService->ventasPorCategoria(),
            'top-lentes' => $this->reportService->lentesMasVendidos(),
            'top-usuarios' => $this->reportService->usuariosTopCompras(),
            default => abort(404),
        };

        $filename = "reporte-{$tipo}-" . now()->format('YmdHis') . '.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename={$filename}"];

        $callback = function () use ($data) {
            $output = fopen('php://output', 'w');
            if ($data->isNotEmpty()) {
                fputcsv($output, array_keys((array) $data->first()));
                foreach ($data as $row) {
                    fputcsv($output, (array) $row);
                }
            }
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }
}
