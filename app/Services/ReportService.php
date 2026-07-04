<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\Lente;
use App\Models\User;
use App\Models\Recomendacion;
use App\Models\AnalisisFacial;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function dashboardData(): array
    {
        return [
            'usuarios_registrados' => User::count(),
            'lentes_disponibles' => Lente::where('estado', 'disponible')->count(),
            'lentes_vendidos' => Lente::where('estado', 'vendido')->count(),
            'ventas_totales' => Pedido::whereIn('estado', ['confirmado', 'en_preparacion', 'entregado'])->count(),
            'pedidos_realizados' => Pedido::count(),
            'ingresos_totales' => Pedido::whereIn('estado', ['confirmado', 'en_preparacion', 'entregado'])->sum('total'),
            'recomendaciones_realizadas' => Recomendacion::count(),
            'analisis_faciales' => AnalisisFacial::count(),
        ];
    }

    public function ventasPorFecha(string $desde, string $hasta)
    {
        return Pedido::select(
            DB::raw('DATE(fecha_pedido) as fecha'),
            DB::raw('COUNT(*) as total_pedidos'),
            DB::raw('SUM(total) as ingresos')
        )
            ->whereBetween('fecha_pedido', [$desde, $hasta])
            ->whereIn('estado', ['confirmado', 'en_preparacion', 'entregado'])
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();
    }

    public function ventasPorCategoria()
    {
        return DB::table('detalle_pedidos')
            ->join('lentes', 'detalle_pedidos.lente_id', '=', 'lentes.id')
            ->join('categorias', 'lentes.categoria_id', '=', 'categorias.id')
            ->select(
                'categorias.nombre',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(detalle_pedidos.precio_unitario) as ingresos')
            )
            ->groupBy('categorias.id', 'categorias.nombre')
            ->orderByDesc('total')
            ->get();
    }

    public function lentesMasVendidos(int $limite = 10)
    {
        return DB::table('detalle_pedidos')
            ->join('lentes', 'detalle_pedidos.lente_id', '=', 'lentes.id')
            ->select(
                'lentes.id',
                'lentes.nombre',
                'lentes.codigo',
                'lentes.imagen_principal',
                DB::raw('COUNT(*) as veces_vendido'),
                DB::raw('SUM(detalle_pedidos.precio_unitario) as total_generado')
            )
            ->groupBy('lentes.id', 'lentes.nombre', 'lentes.codigo', 'lentes.imagen_principal')
            ->orderByDesc('veces_vendido')
            ->limit($limite)
            ->get();
    }

    public function usuariosTopCompras(int $limite = 10)
    {
        return User::select(
            'usuarios.id',
            'usuarios.nombre',
            'usuarios.apellido',
            'usuarios.email',
            DB::raw('COUNT(pedidos.id) as total_compras'),
            DB::raw('COALESCE(SUM(pedidos.total), 0) as total_gastado')
        )
            ->leftJoin('pedidos', 'usuarios.id', '=', 'pedidos.usuario_id')
            ->whereIn('pedidos.estado', ['confirmado', 'en_preparacion', 'entregado'])
            ->groupBy('usuarios.id', 'usuarios.nombre', 'usuarios.apellido', 'usuarios.email')
            ->orderByDesc('total_gastado')
            ->limit($limite)
            ->get();
    }
}
