<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once ("/var/www/cliente/jscorp/clientes/lartduvin/includes/classes/bling.class.php");
require_once ("/var/www/cliente/jscorp/clientes/lartduvin/includes/classes/shopify.class.php");

CONST CONST_path_log = "/var/www/cliente/jscorp/clientes/lartduvin/logs/";

/*
$shopify = new Shopify;

$resposta = json_decode('{"retorno":{"pedidos":[{"pedido":{"numero":"1827","idPedido":12666027717,"codigos_rastreamento":{"codigo_rastreamento":"OO731117388BR"},"volumes":[{"volume":{"servico":"SEDEX CONTRATO AGENCIA TA","codigoRastreamento":"OO731117388BR"}}]}}]}}',true);

print_r($resposta);

if (isset($resposta["retorno"]["pedidos"][0]["pedido"]["codigos_rastreamento"]["codigo_rastreamento"])){
    $codigo_rastreamento = $resposta["retorno"]["pedidos"][0]["pedido"]["codigos_rastreamento"]["codigo_rastreamento"];
    echo "codigo => $codigo_rastreamento";
    $shopify->cria_fulfillment('3859437781143',$codigo_rastreamento,"http://www.correios.com.br/rastreamento",$response);
}    
*/

$bling = new Bling;

$bling->busca_cep(71741803,$vet_cep);

exit(print_r($vet_cep));

function cria_log($pedido=0,$log="---"){

   $fp = fopen(CONST_path_log.$pedido.".log","a");	        
   fwrite($fp, $log);     
   fclose($fp);	     

}