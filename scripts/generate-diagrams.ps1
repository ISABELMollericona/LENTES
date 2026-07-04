param(
    [string]$DiagramasDir = "C:\Users\MOLLERICONA\Downloads\LENTES UPDS\diagramas",
    [string]$WorkDir = "C:\Users\MOLLERICONA\Downloads\LENTES UPDS"
)

$HtmlDir = Join-Path $DiagramasDir "html"
$ImgDir = Join-Path $DiagramasDir "imagenes"
$TempDir = Join-Path $env:TEMP "mermaid-gen"

# Create directories
@($HtmlDir, $ImgDir, $TempDir) | ForEach-Object {
    if (-not (Test-Path $_)) { New-Item -ItemType Directory -Path $_ -Force | Out-Null }
}

$mmdc = Join-Path $WorkDir "node_modules\.bin\mmdc.cmd"

# HTML template parts
$htmlHeader = @'
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{TITLE} - Óptica Golden</title>
    <script src="https://cdn.jsdelivr.net/npm/mermaid@11/dist/mermaid.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: white; padding: 20px; }
        h1 { color: #1a1a2e; margin-bottom: 20px; font-size: 22px; text-align: center; }
        h2 { color: #16213e; margin: 25px 0 15px 0; padding-bottom: 8px; border-bottom: 3px solid #0f3460; }
        .diagram-container { padding: 20px; overflow-x: auto; }
        .mermaid { text-align: center; }
        .back { text-align: center; margin-bottom: 20px; }
        .back a { color: #0f3460; text-decoration: none; }
    </style>
</head>
<body>
    <div class="back"><a href="index.html">← Volver al índice</a></div>
    <h1>{TITLE}</h1>
'@

$htmlFooter = @'
    <script>mermaid.initialize({ startOnLoad: true, theme: 'default' });</script>
</body>
</html>
'@

# Index HTML
$indexHtml = @'
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagramas - Óptica Golden</title>
    <script src="https://cdn.jsdelivr.net/npm/mermaid@11/dist/mermaid.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 20px; }
        h1 { color: #1a1a2e; text-align: center; margin-bottom: 30px; font-size: 28px; }
        h2 { color: #16213e; margin: 30px 0 15px 0; padding-bottom: 8px; border-bottom: 3px solid #0f3460; }
        .diagram-container { 
            background: white; border-radius: 12px; padding: 30px; margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow-x: auto;
        }
        .mermaid { text-align: center; }
        .index-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px; margin-bottom: 40px; }
        .index-item { 
            background: white; padding: 15px 20px; border-radius: 10px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.08); text-decoration: none; color: #0f3460;
            font-weight: 500; transition: all 0.3s; border-left: 4px solid #0f3460;
        }
        .index-item:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .stats { text-align: center; color: #666; margin-bottom: 30px; font-size: 14px; }
    </style>
</head>
<body>
    <h1>📐 Diagramas UML - Óptica Golden</h1>
    <div class="stats">Proyecto: E-commerce con Asesor Virtual IA | Laravel 12, MySQL 8.0, MediaPipe, Gemini AI</div>
    <div class="index-grid">
'@

$indexEnd = @'
    </div>
    <div class="diagram-container">
        <pre class="mermaid">
graph LR
    A["📋 18 Diagramas UML"] --> B["🗄️ Modelado de Datos<br/>ER, Relacional, Clases"]
    A --> C["⚙️ Comportamiento<br/>Secuencia, Actividades, Estados"]
    A --> D["🏗️ Arquitectura<br/>Componentes, Despliegue, Paquetes, MVC"]
    A --> E["👤 Interacción<br/>Casos de Uso, Navegación, Colaboración, Objetos"]
    A --> F["🤖 IA & Facial<br/>Timing, Tiempo, Flujo Datos"]
    B --> G["🛒 Compra & Pago"]
    C --> G
    D --> G
    E --> G
        </pre>
    </div>
    <script>mermaid.initialize({ startOnLoad: true, theme: 'default' });</script>
</body>
</html>
'@

function Get-TitleFromMd {
    param([string]$Content)
    if ($Content -match "^#\s+(.+)$") { return $matches[1] }
    return "Diagrama"
}

function Get-MermaidBlocks {
    param([string]$Content)
    $blocks = @()
    $pattern = '(?<=```mermaid\n)(.*?)(?=\n```)'
    $matches = [regex]::Matches($Content, $pattern, [System.Text.RegularExpressions.RegexOptions]::Singleline)
    foreach ($m in $matches) { $blocks += $m.Value }
    return $blocks
}

function Get-SafeName {
    param([string]$Name)
    return $Name -replace '[\\/:*?"<>|]', '_'
}

# Collect entries for index
$indexEntries = @()
$totalDiagrams = 0

# Process each .md file
Get-ChildItem -Path $DiagramasDir -Filter "*.md" | Sort-Object Name | ForEach-Object {
    $mdFile = $_.FullName
    $baseName = $_.BaseName
    $mdContent = Get-Content -Path $mdFile -Raw
    $title = Get-TitleFromMd -Content $mdContent
    $blocks = Get-MermaidBlocks -Content $mdContent
    
    if ($blocks.Count -eq 0) { return } # skip index.md etc
    
    Write-Host "Processing $baseName... ($($blocks.Count) diagrams)"
    
    # Build HTML body
    $htmlBody = @()
    $blockIndex = 1
    $sectionLabel = 1
    
    # Track sections in the markdown for subheadings
    $lines = $mdContent -split "`n"
    $currentSection = ""
    $inBlock = $false
    
    foreach ($line in $lines) {
        if ($line -match "^##\s+(.+)$") {
            $currentSection = $matches[1].Trim()
            if (-not $currentSection.StartsWith("Diagrama")) {
                $htmlBody += "<h2>$currentSection</h2>"
            }
        }
    }
    
    foreach ($block in $blocks) {
        # Generate PNG
        $imgName = "$baseName`_$blockIndex.png"
        $imgPath = Join-Path $ImgDir $imgName
        $mmdPath = Join-Path $TempDir "$baseName`_$blockIndex.mmd"
        
        # Save mermaid content to temp file
        $block | Out-File -FilePath $mmdPath -Encoding utf8
        
        # Convert to PNG using mmdc
        $pngResult = & $mmdc -i $mmdPath -o $imgPath -w 1800 -b transparent 2>&1
        if ($LASTEXITCODE -eq 0 -and (Test-Path $imgPath)) {
            Write-Host "  Generated PNG: $imgName"
        } else {
            Write-Host "  WARNING: Failed to generate PNG for $imgName" -ForegroundColor Yellow
        }
        
        # Add to HTML
        $escapedBlock = $block -replace '&', '&amp;' -replace '<', '&lt;' -replace '>', '&gt;' -replace '"', '&quot;'
        $htmlBody += "<div class=`"diagram-container`">`n<pre class=`"mermaid`">`n$escapedBlock`n</pre>`n</div>"
        
        $blockIndex++
        $totalDiagrams++
    }
    
    # Write HTML file
    $htmlContent = $htmlHeader -replace '{TITLE}', $title
    $htmlContent += $htmlBody -join "`n"
    $htmlContent += $htmlFooter
    
    $htmlFile = Join-Path $HtmlDir "$baseName.html"
    $htmlContent | Out-File -FilePath $htmlFile -Encoding utf8
    Write-Host "  Created HTML: $baseName.html"
    
    $indexEntries += @{ Num = $baseName.Substring(0,2); Name = $title; File = "$baseName.html" }
}

# Generate index.html
$indexBody = ""
foreach ($entry in $indexEntries) {
    $indexBody += "<a href=`"$($entry.File)`" class=`"index-item`">$($entry.Num) - $($entry.Name)</a>`n"
}
$finalIndex = $indexHtml + $indexBody + $indexEnd
$finalIndex | Out-File -FilePath (Join-Path $HtmlDir "index.html") -Encoding utf8

Write-Host "`n=== Generation Complete ===" -ForegroundColor Green
Write-Host "Total .md files processed: $($indexEntries.Count)"
Write-Host "Total diagrams rendered: $totalDiagrams"
Write-Host "HTML files: $(Get-ChildItem $HtmlDir -Filter '*.html' | Measure-Object | Select-Object -ExpandProperty Count)"
Write-Host "PNG files: $(Get-ChildItem $ImgDir -Filter '*.png' | Measure-Object | Select-Object -ExpandProperty Count)"
