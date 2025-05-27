<?php
$url = 'https://www.bcv.org.ve/';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$html = curl_exec($ch);
if ($html === false) {
    $error = curl_error($ch);
    curl_close($ch);
    die("No se pudo obtener el contenido de la página. Error: $error");
}
curl_close($ch);

libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML($html);
libxml_clear_errors();

$xpath = new DOMXPath($dom);
// Buscar el valor dentro del div con id="dolar"
$nodes = $xpath->query("//div[@id='dolar']//strong");

$valor = null;
foreach ($nodes as $node) {
    $texto = trim($node->nodeValue);
    if (preg_match('/^[\d,.]+$/', $texto)) {
        $valor = $texto;
        break;
    }
}

echo "Valor USD encontrado: {$valor}";
