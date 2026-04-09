<?php
require_once 'backend/api.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Obras ES</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div>
        <h1>Mapa de Obras - Espírito Santo</h1>
        <p>Dados abertos do Governo do ES</p>
    </div>
</header>

<?php if ($erroApi): ?>
<div class="erro">
    <strong>Erro ao acessar a API.</strong> Não foi possível carregar o CSV. Verifique a URL ou tente novamente mais tarde.
</div>
<?php else: ?>

<div class="stats">
    <div class="stat-card">
        <div class="number"><?= $totalObras ?></div>
        <div class="label">Registros de Obras</div>
    </div>
    <div class="stat-card">
        <div class="number"><?= $totalMunicipios ?></div>
        <div class="label">Municípios</div>
    </div>
    <div class="stat-card">
        <div class="number">2017</div>
        <div class="label">Ano de referência</div>
    </div>
</div>

<div class="content">
    <div class="card">
        <div class="card-header">
            <span class="icon">🗺️</span> Localização das Obras
        </div>
        <div id="map"></div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <span class="icon">📊</span> Obras por Município
        </div>
        <div class="chart-container">
            <canvas id="grafico"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="icon">📊</span> Tipos de Obras por Município
        </div>
        <div class="chart-container">
            <canvas id="grafico_tipo"></canvas>
        </div>
    </div>
</div>

<?php endif; ?>

<footer>
    Fonte: <a href="https://dados.es.gov.br/dataset/80d53564-45d2-459d-9601-77685ab8fdf1/resource/542afb80-66c1-4541-891b-c898a4395b2f" target="_blank">Governo ES</a> &nbsp;|&nbsp; Desenvolvido com PHP + Leaflet.js + Chart.js
</footer>

<script>
    const obras      = <?= $obrasJson ?>;
    const municipios = <?= $municipiosJson ?>;
    const contagem   = <?= $contagemJson ?>;
    const tiposObra   = <?= $tiposJson ?>;
    const contagemTipo = <?= $contagemTipoJson ?>;
</script>
<script src="script.js"></script>

</body>
</html>