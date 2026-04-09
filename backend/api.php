<?php 
$csvUrl = 'https://dados.es.gov.br/dataset/80d53564-45d2-459d-9601-77685ab8fdf1/resource/542afb80-66c1-4541-891b-c898a4395b2f/download/mapaobras-2017.csv';

$obras = [];
$municipioCount = [];
$erroApi = false;
$tipoObra = [];
$palavras = ['REFORMA', 'CONSTRUÇÃO', 'PAVIMENTAÇÃO', 'RESTAURAÇÃO',
             'AMPLIAÇÃO', 'IMPLANTAÇÃO', 'AQUISIÇÃO', 'CONTRATAÇÃO'];

// Busca o CSV da API
$context = stream_context_create([
    'http' => [
        'timeout' => 15,
        'user_agent' => 'Mozilla/5.0'
    ]
]);

$csvData = @file_get_contents($csvUrl, false, $context);

if ($csvData === false) {
    $erroApi = true;
} else {
    // Remove BOM UTF-8 se existir
    $csvData = ltrim($csvData, "\xEF\xBB\xBF");
    $linhas = explode("\n", trim($csvData));
    $header = null;

    foreach ($linhas as $i => $linha) {
        $linha = trim($linha);
        if (empty($linha)) continue;

        $campos = explode(';', $linha);

        if ($i === 0) {
            $header = $campos;
            continue;
        }

        if (count($campos) < 7) continue;

        $lat  = trim($campos[5]);
        $lng  = trim($campos[6]);
        $municipio = trim($campos[3]);

        // Valida coordenadas
        if (!is_numeric($lat) || !is_numeric($lng)) continue;
        if ($lat == 0 && $lng == 0) continue;

        $obras[] = [
            'id'        => trim($campos[0]),
            'obra'      => trim($campos[1]),
            'endereco'  => trim($campos[2]),
            'municipio' => $municipio,
            'lat'       => (float) $lat,
            'lng'       => (float) $lng,
        ];

        // Contagem por município
        if (!isset($municipioCount[$municipio])) {
            $municipioCount[$municipio] = 0;
        }
        $municipioCount[$municipio]++;
    }

    // Ordena por quantidade (maior primeiro)
    arsort($municipioCount);

    // Limita a 10 municípios no gráfico e agrupa o resto em "Outros"
    $top = array_slice($municipioCount, 0, 10, true);
    $outros = array_sum(array_slice($municipioCount, 10));
    if ($outros > 0) {
        $top['Outros'] = $outros;
    }
    $municipioCount = $top;

    foreach ($obras as $o) {
        $nome = mb_strtoupper($o['obra'], 'UTF-8');
        $encontrou = false;
        foreach ($palavras as $p) {
            if (str_contains($nome, $p)) {
                $tipoObra[$p] = ($tipoObra[$p] ?? 0) + 1;
                $encontrou = true;
                break;
            }
        }
        if (!$encontrou) {
            $tipoObra['OUTROS'] = ($tipoObra['OUTROS'] ?? 0) + 1;
        }
    }
    arsort($tipoObra);
}

$obrasJson      = json_encode($obras, JSON_UNESCAPED_UNICODE);
$municipiosJson = json_encode(array_keys($municipioCount), JSON_UNESCAPED_UNICODE);
$contagemJson   = json_encode(array_values($municipioCount));
$totalObras     = count($obras);
$totalMunicipios = count(array_unique(array_column($obras, 'municipio')));
$tiposJson    = json_encode(array_keys($tipoObra), JSON_UNESCAPED_UNICODE);
$contagemTipoJson = json_encode(array_values($tipoObra));
?>