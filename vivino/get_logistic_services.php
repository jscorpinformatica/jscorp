<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

$apikey = "ccb0d51e8179297e3e685e66af29dd3c2ab71af496cf220955d6147d028019d66b82e1b1";
$outputType = "json";
$url = 'https://bling.com.br/Api/v2/logisticas/servicos/' . $outputType;
$retorno = executeGetOrder($url, $apikey);
echo $retorno;

function executeGetOrder($url, $apikey){
    $curl_handle = curl_init();
    curl_setopt($curl_handle, CURLOPT_URL, $url . '&apikey=' . $apikey);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($curl_handle);
    curl_close($curl_handle);
    return $response;
}