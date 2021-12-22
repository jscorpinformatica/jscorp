<?php

#ini_set('display_errors', 1);
#ini_set('display_startup_errors', 1);
#error_reporting(E_ALL);

require_once ("/var/www/cliente/jscorp/clientes/lartduvin/includes/classes/bling.class.php");
require_once ("/var/www/cliente/jscorp/clientes/lartduvin/includes/classes/shopify.class.php");

CONST CONST_path_log = "/var/www/cliente/jscorp/clientes/lartduvin/logs/";

//6a4b1db16e0ca14d8ce82f7461d7c343243317cee71ca8291ac9575ffeb11a7a

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$json_chamada = file_get_contents('php://input');


$pedido = json_decode($json_chamada,true);
cria_log($pedido['id'],$json_chamada);


function cria_log($pedido=0,$log="---"){

   $fp = fopen(CONST_path_log.$pedido.".log","a");	        
   fwrite($fp, $log);     
   fclose($fp);	     

}

if ($pedido['financial_status'] <> 'paid') exit();

$bling   = new Bling;
$shopify = new Shopify;

if ($bling->insere_pedido($pedido,$resposta)) echo "\n Inseriu pedido ".$pedido["id"]."\n";
	
cria_log($pedido['id'],json_encode($resposta));

# cria o fulfillment com o codigo de rastreio no shopify

if (isset($resposta["retorno"]["pedidos"][0]["pedido"]["codigos_rastreamento"]["codigo_rastreamento"])){
    $codigo_rastreamento = $resposta["retorno"]["pedidos"][0]["pedido"]["codigos_rastreamento"]["codigo_rastreamento"];
    $shopify->cria_fulfillment($pedido['id'],$codigo_rastreamento,"http://www.correios.com.br/rastreamento");
}    