<?php
/****
    References
    https://pt.wikipedia.org/wiki/EAN-13#C%C3%A1lculo_do_d%C3%ADgito_verificador_EAN_13    
*****/

$m = [
    "0" => ["0", "0", "0", "0", "0", "0"],
    "1" => ["0", "0", "1", "0", "1", "1"],
    "2" => ["0", "0", "1", "1", "0", "1"],
    "3" => ["0", "0", "1", "1", "1", "0"],
    "4" => ["0", "1", "0", "0", "1", "1"],
    "5" => ["0", "1", "1", "0", "0", "1"],
    "6" => ["0", "1", "1", "1", "0", "0"],
    "7" => ["0", "1", "0", "1", "0", "1"],
    "8" => ["0", "1", "0", "1", "1", "0"],
    "9" => ["0", "1", "1", "0", "1", "0"]
];

$m2 = [ //collection 2
    "0" => ["A", "K", "a"],
    "1" => ["B", "L", "b"],
    "2" => ["C", "M", "c"],
    "3" => ["D", "N", "d"],
    "4" => ["E", "O", "e"],
    "5" => ["F", "P", "f"],
    "6" => ["G", "Q", "g"],
    "7" => ["H", "R", "h"],
    "8" => ["I", "S", "i"],
    "9" => ["J", "T", "j"]
];

$code = $_GET['code'];
$codeLength = strlen($code);
$codeEAN13 = EAN13($code);
$codeEAN13Length = strlen($codeEAN13);
$digitEAN13 = digit($code);
$fontEAN13 = codeFont($codeEAN13);

function EAN13($code)
{
    return $code . digit($code);
}

function digit($code)
{
    global $codeLength;
    if ($codeLength = 12) {
        $codeSplit = str_split($code);
        $sum = 0;
        for ($i = 0; $i < $codeLength; $i++) {
            $sum = $codeSplit[$i] * ($i % 2 ? 3 : 1) + $sum;
        }
        return ((((explode('.', ($sum / 10))[0]) + 1) * 10) - $sum);
    } else {
        echo 'Codigo deve conter 12 digitos';
    }
}

function createJSON($code)
{
    global $codeEAN13, $codeLength, $codeEAN13Length, $digitEAN13, $fontEAN13;

    //criamos o arquivo 
    $file = fopen('./json/' . $codeEAN13 . '.json', 'w');
    //verificamos se foi criado 
    if (!$file) die('Não foi possível criar o JSON.');
    //Criando Json
    $json = '{
        "code":' . $code . ',
        "codeLength":' . $codeLength . ',
        "codeEAN13":' . $codeEAN13 . ',
        "codeEAN13Length":' . $codeEAN13Length . ',
        "digitEAN13":' . $digitEAN13 . ',
        "fontEAN13":"' . $fontEAN13 . '"
    }';
    //escrevemos no arquivo
    fwrite($file, $json);
    //Fechamos o arquivo após escrever nele
    fclose($file);
}

function codeFont($code)
{
    global $m, $m2;
    $code = str_split($code);
    $c = $m[$code[0]];
    $m2c = '';
    for ($i = 0; $i < 6; $i++) {
        $f = $i + 1;
        $m2c = $m2c . $m2[$code[$f]][$c[$i]];
    }
    $e = '';
    for ($i = 7; $i < 13; $i++) {
        $e = $e . $m2[$code[$i]][2];
    }
    return $code[0] . $m2c . '*' . $e . '+';
}

createJSON($code);
header('Location: ' . './json/' . $codeEAN13 . '.json');
