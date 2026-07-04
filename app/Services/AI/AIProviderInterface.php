<?php

namespace App\Services\AI;

interface AIProviderInterface
{
    public function chat(string $mensaje, array $contexto = []): string;
    public function generarRecomendacion(array $preferencias, array $catalogo): array;
    public function explicarRecomendacion(array $lente, array $preferencias): string;
}
