# RIESGOS DEL PROYECTO - Óptica Golden eCommerce

## Matriz de Riesgos

| ID  | Riesgo | Probabilidad | Impacto | Nivel | Mitigación |
|-----|--------|-------------|---------|-------|------------|
| R-01 | API de IA (Gemini/Groq) no disponible temporalmente | Media | Alto | Crítico | Implementar fallback con respuestas predefinidas; cola de reintentos |
| R-02 | MediaPipe no es preciso con todos los tipos de rostro | Alta | Medio | Alto | Implementar clasificador local como respaldo; permitir selección manual de forma |
| R-03 | Tiempo de análisis facial > 10 segundos | Media | Medio | Medio | Procesamiento asíncrono con Laravel Queue; notificación cuando esté listo |
| R-04 | Límite de tasa de API de Gemini/Groq excedido | Alta | Alto | Crítico | Cache de respuestas; rate limiting interno; múltiples API keys |
| R-05 | Conflictos de concurrencia en compra (2 usuarios compran el último lente) | Baja | Alto | Alto | Transacciones BD; validación de disponibilidad antes de confirmar |
| R-06 | Carga de imágenes grandes (>5MB) afecta rendimiento | Media | Medio | Medio | Validación server-side; compresión automática; límite de tamaño |
| R-07 | Usuarios sin cámara o fotografía de baja calidad | Alta | Bajo | Bajo | Análisis facial como opcional; alternativa de selección manual de forma |
| R-08 | Caída del servidor en hora pico | Baja | Alto | Alto | Balanceador de carga; servidor redundante; backup automático |
| R-09 | Fuga de datos de usuarios | Baja | Crítico | Crítico | HTTPS; hash de contraseñas; auditoría de accesos; cifrado de datos sensibles |
| R-10 | Incompatibilidad del frontend con navegadores antiguos | Baja | Medio | Bajo | Polyfills; testing en Chrome/Firefox/Edge; progressive enhancement |
| R-11 | Retraso en la entrega por complejidad de IA | Media | Alto | Alto | MVP con reglas lógicas primero; IA como mejora progresiva |
| R-12 | Costos de API de IA superan el presupuesto | Media | Medio | Medio | Monitoreo de uso; límites por usuario; caché agresiva |
| R-13 | El clasificador de forma facial falla en rostros no occidentales | Alta | Medio | Alto | Dataset diverso de entrenamiento; prueba con múltiples etnias |
| R-14 | Ataque DDoS al sitio en lanzamiento | Baja | Alto | Alto | CloudFlare/WAF; rate limiting; monitoreo de tráfico |
| R-15 | Error en cálculo de compatibilidad genera malas recomendaciones | Media | Alto | Alto | Pruebas exhaustivas del RecommendationEngine; logs de auditoría |

## Plan de Contingencia

### Para riesgos críticos (R-01, R-04, R-05, R-09):

**R-01 / R-04 (IA no disponible):**
```
if (AIProvider::isAvailable()) {
    $respuesta = $aiProvider->chat($mensaje);
} else {
    $respuesta = $fallbackService->respuestaPredefinida($mensaje);
}
```

**R-05 (Concurrencia):**
```php
DB::transaction(function () use ($lenteId, $userId) {
    $lente = Lente::where('id', $lenteId)
        ->where('estado', 'disponible')
        ->lockForUpdate()
        ->firstOrFail();
    // Procesar compra...
});
```

**R-09 (Seguridad):**
- Auditoría semanal de logs
- Pruebas de penetración trimestrales
- Rotación de API keys cada 30 días
- 2FA para admin

## Plan de Recuperación ante Desastres

| Evento | RTO | RPO | Acción |
|--------|-----|-----|--------|
| Caída de servidor | 2 horas | 1 hora | Restaurar desde backup diario |
| Corrupción de BD | 4 horas | 24 horas | Restaurar último backup íntegro |
| Ataque de seguridad | 1 hora | 1 hora | Aislar servidor, restaurar desde backup pre-ataque |
| Falla de API IA | Inmediato | N/A | Activar modo fallback local |

## Monitoreo Continuo

| Herramienta | Qué monitorea | Alerta |
|-------------|---------------|--------|
| Laravel Log | Errores del sistema | Email al admin si > 10 errores/hora |
| Uptime Robot | Disponibilidad 24/7 | SMS si sitio caído > 5 min |
| New Relic | Rendimiento BD y API | Si tiempo respuesta > 3s promedio |
| Google Analytics | Comportamiento usuarios | Caída > 20% en conversiones |
| Custom Dashboard | API IA consumo/costos | Si costo > 80% del presupuesto mensual |
