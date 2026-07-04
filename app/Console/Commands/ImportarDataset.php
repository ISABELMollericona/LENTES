<?php

namespace App\Console\Commands;

use App\Models\Categoria;
use App\Models\ImagenLente;
use App\Models\Lente;
use App\Models\Marca;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportarDataset extends Command
{
    protected $signature = 'lentes:importar-dataset
                            {--tipo=all : Tipo a importar: eyeglasses, sunglasses, all}
                            {--limit=100 : Máximo de productos por tipo}
                            {--fresh : Eliminar registros de dataset previos antes de importar}';

    protected $description = 'Importa productos de lentes del dataset local a la base de datos con imágenes';

    private string $datasetPath;
    private string $publicPath;

    // Datos de asignación determinística
    private array $monturas  = ['completa', 'semi_al_aire', 'al_aire'];
    private array $generos   = ['hombre', 'mujer', 'unisex'];
    private array $materiales = ['Acetato', 'Metal', 'Titanio', 'Plástico TR90', 'Policarbonato', 'Ultem', 'Madera'];
    private array $colores   = ['Negro', 'Marrón', 'Gris', 'Dorado', 'Plateado', 'Azul', 'Transparente', 'Carey', 'Verde', 'Rojo', 'Morado', 'Rosa'];

    private array $nombresEye = [
        'Clásico Urbano', 'Elegante Oval', 'Cuadrado Moderno', 'Retro Round', 'Executive Pro',
        'Slim Line', 'Heritage Cat', 'Tech Rectangular', 'Vintage Pilot', 'Minimal Wire',
        'Bold Square', 'Soft Cat Eye', 'Academic Round', 'Business Edge', 'Studio Frame',
        'Street Style', 'Architect', 'Designer Plus', 'Carbon Light', 'Ultra Slim',
    ];

    private array $nombresSun = [
        'Aviador Premium', 'Sport Shield', 'Beach Club', 'Sunset Wayfarer', 'Oversize Glam',
        'Desert Racer', 'Island Wrap', 'Mirrored Pro', 'Fashion Cat', 'Urban Shield',
        'Retro Pilot', 'Bold Oversized', 'Classic Ray', 'Sport Wrap', 'Festival Round',
    ];

    public function handle(): int
    {
        $this->datasetPath = base_path('dataset/Glasses Dataset');
        $this->publicPath  = public_path('img/lentes/dataset');

        if (!is_dir($this->datasetPath)) {
            $this->error("No se encontró la carpeta dataset en: {$this->datasetPath}");
            return self::FAILURE;
        }

        // Crear directorio público destino
        foreach (['eyeglasses', 'sunglasses'] as $tipo) {
            $dir = "{$this->publicPath}/{$tipo}";
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        // Obtener IDs de marcas y categorías
        $marcas = Marca::pluck('id')->toArray();
        $catOptica = Categoria::where('slug', 'lentes-opticos')->value('id') ?? 1;
        $catSol    = Categoria::where('slug', 'lentes-de-sol')->value('id') ?? 2;

        if (empty($marcas)) {
            $this->error('No hay marcas en la base de datos. Ejecuta primero: php artisan db:seed --class=MarcaSeeder');
            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            $this->warn('Eliminando registros de dataset previos...');
            Lente::where('dataset_origen', 'LIKE', 'Glasses Dataset%')->get()->each(function ($l) {
                $l->imagenes()->delete();
                $l->delete();
            });
        }

        $tipo  = $this->option('tipo');
        $limit = (int) $this->option('limit');

        $total = 0;

        if (in_array($tipo, ['all', 'eyeglasses'])) {
            $total += $this->importarTipo('eyeglasses', $catOptica, $marcas, $limit);
        }

        if (in_array($tipo, ['all', 'sunglasses'])) {
            $total += $this->importarTipo('sunglasses', $catSol, $marcas, $limit);
        }

        $this->newLine();
        $this->info("✓ Importación completa: {$total} productos registrados.");

        return self::SUCCESS;
    }

    private function importarTipo(string $tipo, int $categoriaId, array $marcas, int $limit): int
    {
        $baseDir = "{$this->datasetPath}/{$tipo}";

        if (!is_dir($baseDir)) {
            $this->warn("Carpeta no encontrada: {$baseDir}");
            return 0;
        }

        $folders = array_values(array_filter(
            scandir($baseDir),
            fn($f) => $f !== '.' && $f !== '..' && is_dir("{$baseDir}/{$f}")
        ));

        // Ordenar numéricamente
        usort($folders, fn($a, $b) => (int)$a - (int)$b);
        $folders = array_slice($folders, 0, $limit);

        $tipoLabel  = $tipo === 'eyeglasses' ? 'Ópticos' : 'De Sol';
        $tipoLente  = $tipo === 'eyeglasses' ? 'optical' : 'sol';
        $prefijo    = $tipo === 'eyeglasses' ? 'E' : 'S';
        $nombres    = $tipo === 'eyeglasses' ? $this->nombresEye : $this->nombresSun;

        $bar = $this->output->createProgressBar(count($folders));
        $bar->setFormat(" <fg=yellow>%current%/%max%</> [%bar%] %percent:3s%% — %message%");
        $bar->setMessage("Importando lentes {$tipoLabel}...");
        $bar->start();

        $importados = 0;

        foreach ($folders as $idx => $folder) {
            $productDir = "{$baseDir}/{$folder}/product";

            if (!is_dir($productDir)) {
                $bar->advance();
                continue;
            }

            $imagenes = array_values(array_filter(
                scandir($productDir),
                fn($f) => preg_match('/\.(jpg|jpeg|png|webp)$/i', $f)
            ));

            if (empty($imagenes)) {
                $bar->advance();
                continue;
            }

            $codigo = "{$prefijo}-{$folder}";

            // Evitar duplicados
            if (Lente::where('codigo', $codigo)->exists()) {
                $bar->advance();
                continue;
            }

            // Determinar atributos de forma determinística
            $marcaId  = $marcas[$idx % count($marcas)];
            $montura  = $this->monturas[$idx % count($this->monturas)];
            $genero   = $this->generos[$idx % count($this->generos)];
            $material = $this->materiales[$idx % count($this->materiales)];
            $color    = $this->colores[$idx % count($this->colores)];
            $nombre   = ($nombres[$idx % count($nombres)]) . ' ' . Str::upper(substr($imagenes[0], 0, 6));
            $precio   = $tipo === 'eyeglasses'
                ? 350 + ($idx % 18) * 45
                : 280 + ($idx % 15) * 38;

            // Copiar imagen principal
            $ext      = strtolower(pathinfo($imagenes[0], PATHINFO_EXTENSION));
            $destName = "{$prefijo}_{$folder}.{$ext}";
            $destPath = "{$this->publicPath}/{$tipo}/{$destName}";
            $srcPath  = "{$productDir}/{$imagenes[0]}";

            if (!file_exists($destPath)) {
                copy($srcPath, $destPath);
            }

            $imagenPrincipal = "img/lentes/dataset/{$tipo}/{$destName}";

            DB::beginTransaction();
            try {
                $lente = Lente::create([
                    'codigo'           => $codigo,
                    'nombre'           => $nombre,
                    'descripcion'      => "Lente {$tipoLabel} de alta calidad con montura {$montura} en {$material}.",
                    'categoria_id'     => $categoriaId,
                    'marca_id'         => $marcaId,
                    'genero'           => $genero,
                    'tipo_lente'       => $tipoLente,
                    'tipo_montura'     => $montura,
                    'material'         => $material,
                    'color'            => $color,
                    'precio'           => $precio,
                    'imagen_principal' => $imagenPrincipal,
                    'estado'           => 'disponible',
                    'fecha_registro'   => now()->toDateString(),
                    'dataset_origen'   => "Glasses Dataset/{$tipo}/{$folder}",
                ]);

                // Registrar hasta 4 imágenes de producto
                foreach (array_slice($imagenes, 0, 4) as $orden => $imgFile) {
                    $imgExt      = strtolower(pathinfo($imgFile, PATHINFO_EXTENSION));
                    $imgDestName = "{$prefijo}_{$folder}_{$orden}.{$imgExt}";
                    $imgDest     = "{$this->publicPath}/{$tipo}/{$imgDestName}";

                    if (!file_exists($imgDest)) {
                        copy("{$productDir}/{$imgFile}", $imgDest);
                    }

                    ImagenLente::create([
                        'lente_id' => $lente->id,
                        'url'      => "img/lentes/dataset/{$tipo}/{$imgDestName}",
                        'orden'    => $orden,
                    ]);
                }

                DB::commit();
                $importados++;
            } catch (\Throwable $e) {
                DB::rollBack();
                $this->newLine();
                $this->error("Error en folder {$folder}: " . $e->getMessage());
            }

            $bar->setMessage("{$nombre}");
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->line("  <fg=green>✓</> {$importados} lentes {$tipoLabel} importados");

        return $importados;
    }
}
