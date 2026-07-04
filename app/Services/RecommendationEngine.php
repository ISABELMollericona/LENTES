<?php

namespace App\Services;

use App\Models\Lente;
use App\Models\Recomendacion;
use App\Models\DetalleRecomendacion;
use Illuminate\Support\Collection;

class RecommendationEngine
{
    private array $pesos = [
        'forma_rostro'  => 25,
        'tipo_montura'  => 20,
        'estilo_color'  => 25,
        'color_favorito'=> 15,
        'presupuesto'   => 15,
    ];

    private array $reglasFormaMontura = [
        'redondo' => ['completa', 'semi_al_aire'],
        'cuadrado' => ['semi_al_aire', 'al_aire'],
        'ovalado' => ['completa', 'semi_al_aire', 'al_aire'],
        'rectangular' => ['completa', 'semi_al_aire'],
        'corazon' => ['al_aire', 'semi_al_aire'],
        'diamante' => ['semi_al_aire', 'al_aire'],
    ];

    private array $reglasUsoLente = [
        'computadora' => ['optical'],
        'lectura' => ['optical'],
        'estudio' => ['optical'],
        'conducir' => ['optical', 'sol'],
        'uso_diario' => ['optical', 'sol'],
        'deportes' => ['sol'],
        'moda' => ['sol'],
    ];

    private array $reglasEstiloColor = [
        'clasico'     => ['negro', 'carey', 'marrón', 'marron', 'dorado', 'plateado', 'gris', 'café'],
        'moderno'     => ['azul', 'rojo', 'blanco', 'transparente', 'negro', 'plateado', 'gris'],
        'ejecutivo'   => ['negro', 'plateado', 'gris', 'azul', 'dorado', 'carey'],
        'deportivo'   => ['rojo', 'azul', 'verde', 'neon', 'negro', 'naranja'],
        'minimalista' => ['transparente', 'blanco', 'negro', 'gris', 'plateado'],
        'casual'      => ['carey', 'marrón', 'marron', 'azul', 'verde', 'rojo', 'negro'],
    ];

    public function recomendar(array $preferencias): array
    {
        $query = Lente::disponibles();
        $this->aplicarFiltrosBase($query, $preferencias);

        $lentes = $query->get();
        $resultados = collect();

        foreach ($lentes as $lente) {
            $compatibilidad = $this->calcularCompatibilidad($lente, $preferencias);
            if ($compatibilidad > 0) {
                $justificacion = $this->generarJustificacion($lente, $preferencias, $compatibilidad);
                $resultados->push([
                    'lente'          => $lente,
                    'compatibilidad' => $compatibilidad,
                    'justificacion'  => $justificacion,
                ]);
            }
        }

        // Ordenar por compatibilidad
        $ordenados = $resultados->sortByDesc('compatibilidad');

        // Diversidad: máximo 3 lentes con la misma montura en el top 9
        $diversificados = collect();
        $conteoMontura  = [];
        $maxPorMontura  = 3;

        foreach ($ordenados as $item) {
            $montura = $item['lente']->tipo_montura;
            $conteoMontura[$montura] = ($conteoMontura[$montura] ?? 0);

            if ($conteoMontura[$montura] < $maxPorMontura) {
                $diversificados->push($item);
                $conteoMontura[$montura]++;
            }

            if ($diversificados->count() >= 9) break;
        }

        // Si no alcanzamos 9, rellenar con los mejores restantes
        if ($diversificados->count() < 9) {
            $ids = $diversificados->pluck('lente.id');
            foreach ($ordenados as $item) {
                if (!$ids->contains($item['lente']->id)) {
                    $diversificados->push($item);
                    if ($diversificados->count() >= 9) break;
                }
            }
        }

        return $diversificados->values()->toArray();
    }

    private function aplicarFiltrosBase($query, array $prefs): void
    {
        if (!empty($prefs['presupuesto_max'])) {
            $query->where('precio', '<=', $prefs['presupuesto_max']);
        }

        if (!empty($prefs['uso_lentes'])) {
            $tiposPermitidos = $this->reglasUsoLente[$prefs['uso_lentes']] ?? [];
            if (!empty($tiposPermitidos)) {
                $query->whereIn('tipo_lente', $tiposPermitidos);
            }
        }

        // Filtro de género: excluir lentes del género opuesto
        if (!empty($prefs['genero']) && $prefs['genero'] !== 'unisex') {
            $query->whereIn('genero', [$prefs['genero'], 'unisex']);
        }
    }

    private function calcularCompatibilidad(Lente $lente, array $prefs): float
    {
        $puntaje = 0;

        // 1. Forma de rostro → montura recomendada (25 pts)
        if (!empty($prefs['forma_rostro'])) {
            $puntaje += $this->evaluarFormaRostro($lente, $prefs['forma_rostro']);
        } else {
            $puntaje += $this->pesos['forma_rostro'] * 0.5; // neutro si no hay forma
        }

        // 2. Montura preferida del usuario (20 pts, soft)
        if (!empty($prefs['tipo_montura'])) {
            $puntaje += $this->evaluarMontura($lente, $prefs['tipo_montura']);
        } else {
            $puntaje += $this->pesos['tipo_montura'] * 0.5;
        }

        // 3. Estilo → colores asociados (25 pts)
        if (!empty($prefs['estilo'])) {
            $puntaje += $this->evaluarEstilo($lente, $prefs['estilo']);
        } else {
            $puntaje += $this->pesos['estilo_color'] * 0.5;
        }

        // 4. Color favorito exacto (15 pts)
        if (!empty($prefs['color_favorito'])) {
            $puntaje += $this->evaluarColor($lente, $prefs['color_favorito']);
        }

        // 5. Presupuesto (15 pts): mayor puntaje cuanto más barato es respecto al máximo
        if (!empty($prefs['presupuesto_max']) && $prefs['presupuesto_max'] > 0) {
            $puntaje += $this->evaluarPresupuesto($lente, $prefs['presupuesto_max']);
        } else {
            $puntaje += $this->pesos['presupuesto'] * 0.5;
        }

        return min(round($puntaje, 2), 100.0);
    }

    private function evaluarFormaRostro(Lente $lente, string $formaRostro): float
    {
        $monturasRecomendadas = $this->reglasFormaMontura[$formaRostro] ?? [];
        if (in_array($lente->tipo_montura, $monturasRecomendadas)) {
            // posición en la lista: primera opción = puntaje completo, segunda = 80%
            $pos = array_search($lente->tipo_montura, $monturasRecomendadas);
            return $this->pesos['forma_rostro'] * ($pos === 0 ? 1.0 : 0.8);
        }
        return 0;
    }

    private function evaluarMontura(Lente $lente, string $monturaPreferida): float
    {
        if ($lente->tipo_montura === $monturaPreferida) {
            return $this->pesos['tipo_montura']; // 20 pts: coincidencia exacta
        }
        return $this->pesos['tipo_montura'] * 0.3; // 6 pts: montura diferente (no penaliza fuerte)
    }

    private function evaluarEstilo(Lente $lente, string $estilo): float
    {
        $coloresEstilo = $this->reglasEstiloColor[$estilo] ?? [];
        $colorLente    = strtolower($lente->color ?? '');

        foreach ($coloresEstilo as $color) {
            if (str_contains($colorLente, $color)) {
                return $this->pesos['estilo_color'];
            }
        }
        return $this->pesos['estilo_color'] * 0.25; // 6 pts base aunque no coincida el color
    }

    private function evaluarColor(Lente $lente, string $colorFavorito): float
    {
        $colorLente = strtolower($lente->color ?? '');
        $colorFav   = strtolower($colorFavorito);

        if (str_contains($colorLente, $colorFav)) {
            return $this->pesos['color_favorito']; // 15 pts
        }
        return 0;
    }

    private function evaluarPresupuesto(Lente $lente, float $presupuestoMax): float
    {
        if ($lente->precio <= 0) return 0;
        $ratio = $lente->precio / $presupuestoMax; // 0.0 - 1.0
        // Más barato → más puntaje (hasta 15 pts)
        return $this->pesos['presupuesto'] * max(0, 1 - $ratio * 0.7);
    }

    private function generarJustificacion(Lente $lente, array $prefs, float $compatibilidad): string
    {
        $razones = [];

        // Forma de rostro
        if (!empty($prefs['forma_rostro'])) {
            $monturasOk = $this->reglasFormaMontura[$prefs['forma_rostro']] ?? [];
            if (in_array($lente->tipo_montura, $monturasOk)) {
                $monturaLabel = str_replace('_', ' ', $lente->tipo_montura);
                $razones[] = "La montura {$monturaLabel} es ideal para tu rostro {$prefs['forma_rostro']}";
            }
        }

        // Montura preferida
        if (!empty($prefs['tipo_montura']) && $lente->tipo_montura === $prefs['tipo_montura']) {
            $monturaLabel = str_replace('_', ' ', $lente->tipo_montura);
            if (empty($razones)) {
                $razones[] = "Montura {$monturaLabel} según tu preferencia";
            }
        }

        // Color favorito
        if (!empty($prefs['color_favorito']) && !empty($lente->color)) {
            $colorFav = strtolower($prefs['color_favorito']);
            if (str_contains(strtolower($lente->color), $colorFav)) {
                $razones[] = "Color {$lente->color} coincide con tu favorito";
            } else {
                $razones[] = "Color {$lente->color} disponible en este modelo";
            }
        }

        // Estilo
        if (!empty($prefs['estilo'])) {
            $coloresEstilo = $this->reglasEstiloColor[$prefs['estilo']] ?? [];
            $colorLente    = strtolower($lente->color ?? '');
            $coincide      = false;
            foreach ($coloresEstilo as $c) {
                if (str_contains($colorLente, $c)) { $coincide = true; break; }
            }
            if ($coincide) {
                $razones[] = "Se adapta a tu estilo {$prefs['estilo']}";
            }
        }

        // Presupuesto
        if (!empty($prefs['presupuesto_max'])) {
            $ahorro = $prefs['presupuesto_max'] - $lente->precio;
            if ($ahorro >= 100) {
                $razones[] = "Bs {$ahorro} por debajo de tu presupuesto";
            }
        }

        if (empty($razones)) {
            $razones[] = "Seleccionado entre las mejores opciones disponibles";
        }

        return implode('. ', $razones) . '.';
    }

    public function guardarRecomendacion(int $usuarioId, array $preferencias, array $resultados): Recomendacion
    {
        $recomendacion = Recomendacion::create([
            'usuario_id' => $usuarioId,
            'analisis_facial_id' => $preferencias['analisis_facial_id'] ?? null,
            'forma_rostro' => $preferencias['forma_rostro'] ?? null,
            'presupuesto_max' => $preferencias['presupuesto_max'] ?? null,
            'uso_lentes' => $preferencias['uso_lentes'],
            'estilo' => $preferencias['estilo'],
            'color_favorito' => $preferencias['color_favorito'] ?? null,
            'tipo_montura' => $preferencias['tipo_montura'],
        ]);

        foreach ($resultados as $index => $item) {
            DetalleRecomendacion::create([
                'recomendacion_id' => $recomendacion->id,
                'lente_id' => $item['lente']->id,
                'compatibilidad' => $item['compatibilidad'],
                'justificacion' => $item['justificacion'],
                'orden' => $index + 1,
            ]);
        }

        return $recomendacion->load('detalles.lente');
    }
}
