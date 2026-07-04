<?php

namespace App\Services\FaceAnalysis;

class FaceShapeClassifier
{
    private const FORMAS = ['ovalado', 'redondo', 'cuadrado', 'rectangular', 'corazon', 'diamante'];

    public function clasificar(array $puntos): array
    {
        if (empty($puntos)) {
            return ['forma' => null, 'confianza' => 0];
        }

        $anchuraMaxilar = $puntos['anchura_maxilar'] ?? 0;
        $anchuraPomulos = $puntos['anchura_pomulos'] ?? 0;
        $alturaRostro = $puntos['altura_rostro'] ?? 1;

        $relacionAnchoAlto = $anchuraPomulos / $alturaRostro;
        $relacionMaxilarPomulos = $anchuraMaxilar / max($anchuraPomulos, 1);

        $puntajes = [];

        $puntajes['ovalado'] = $this->scoreOvalado($relacionAnchoAlto, $relacionMaxilarPomulos);
        $puntajes['redondo'] = $this->scoreRedondo($relacionAnchoAlto, $relacionMaxilarPomulos);
        $puntajes['cuadrado'] = $this->scoreCuadrado($relacionAnchoAlto, $relacionMaxilarPomulos);
        $puntajes['rectangular'] = $this->scoreRectangular($relacionAnchoAlto, $relacionMaxilarPomulos);
        $puntajes['corazon'] = $this->scoreCorazon($relacionAnchoAlto, $relacionMaxilarPomulos);
        $puntajes['diamante'] = $this->scoreDiamante($relacionAnchoAlto, $relacionMaxilarPomulos);

        arsort($puntajes);
        $forma = key($puntajes);
        $maxScore = reset($puntajes);

        $totalScore = array_sum($puntajes);
        $confianza = $totalScore > 0 ? round(($maxScore / $totalScore) * 100, 2) : 0;

        return [
            'forma' => $forma,
            'confianza' => $confianza,
            'puntajes' => $puntajes,
        ];
    }

    private function scoreOvalado(float $relAncho, float $relMaxilar): float
    {
        return $this->gauss($relAncho, 0.75, 0.08) * 0.5 +
               $this->gauss($relMaxilar, 0.85, 0.1) * 0.5;
    }

    private function scoreRedondo(float $relAncho, float $relMaxilar): float
    {
        return $this->gauss($relAncho, 0.9, 0.08) * 0.5 +
               $this->gauss($relMaxilar, 0.95, 0.08) * 0.5;
    }

    private function scoreCuadrado(float $relAncho, float $relMaxilar): float
    {
        return $this->gauss($relAncho, 0.85, 0.06) * 0.4 +
               $this->gauss($relMaxilar, 0.98, 0.06) * 0.6;
    }

    private function scoreRectangular(float $relAncho, float $relMaxilar): float
    {
        return $this->gauss($relAncho, 0.65, 0.06) * 0.5 +
               $this->gauss($relMaxilar, 0.95, 0.06) * 0.5;
    }

    private function scoreCorazon(float $relAncho, float $relMaxilar): float
    {
        return $this->gauss($relAncho, 0.85, 0.08) * 0.3 +
               $this->gauss($relMaxilar, 0.7, 0.08) * 0.7;
    }

    private function scoreDiamante(float $relAncho, float $relMaxilar): float
    {
        return $this->gauss($relAncho, 0.8, 0.06) * 0.4 +
               $this->gauss($relMaxilar, 0.6, 0.08) * 0.6;
    }

    private function gauss(float $x, float $mu, float $sigma): float
    {
        return exp(-0.5 * pow(($x - $mu) / $sigma, 2));
    }
}
