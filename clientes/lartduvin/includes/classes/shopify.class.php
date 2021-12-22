<?php

#error_reporting(E_ALL);
#ini_set('display_errors', 1);


require_once("/var/www/cliente/jscorp/clientes/lartduvin/vendor/autoload.php");
use GuzzleHttp\Client;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\BadResponseException;

use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Request;


class Shopify
{
    // property declaration
    private $total_pedido;    
    private $debug                 = true;
    private $header_token          = "X-Shopify-Access-Token";
    private $token                 = "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";

	public function __construct() {
        
		#header("Access-Control-Allow-Origin: *");
		#header("Access-Control-Allow-Headers: Content-Type");        
		        
		$this->guzzle = new Client();		   				
		
		#$parametros["headers"]["Accept"]          = "application/json";		
		$parametros["headers"]["Content-type"]    = "application/json; charset=utf-8";	       

		$parametros['headers'][$this->header_token] = $this->token;
		#$parametros["headers"]["Accept"]          = "*/*";
		#$parametros["headers"]["Cache-Control"]   = "no_cache";
		#$parametros["headers"]["Accept-Encoding"] = "gzip, deflate";		
		#$parametros["headers"]["Connection"]      = "keep-alive";
		#$parametros["headers"]["Host"]            = "bling.com.br";		
		
        $parametros['version']              = 1.1;		
        #$parametros['read_timeout']         = 300;
        $parametros['timeout']              = 0;  # ilimitado           
        $parametros['debug']                = $this->debug;		
		
		/*
		$parametros["headers"]["Accept"]          = "application/json";		
		$parametros["headers"]["Content-type"]    = "application/json; charset=utf-8";	       
		$parametros["headers"]["Host"]            = "bling.com.br";		
		
		$parametros["headers"]["Accept"]          = "";
		$parametros["headers"]["Cache-Control"]   = "no_cache";
		$parametros["headers"]["Accept-Encoding"] = "gzip, deflate";		
		$parametros["headers"]["Connection"]      = "keep-alive";
		$parametros['query']['apikey']            = $this->token;
		
        $parametros['version']              = 1.1;		
        $parametros['read_timeout']         = 300;
        $parametros['timeout']              = 10;  # ilimitado           
        $parametros['debug']                = $this->debug;
        */
        
        $this->parametros = $parametros;	
        
	}
    
    
    public Function cria_fulfillment($shopify_id,$codigo_rastreamento,$url_rastreamento,&$response) {
        
        $retorno = false;
    
        $json = 
        '{
		   "fulfillment": {
			 "location_id": 54806937751,      
			 "tracking_number": "'.$codigo_rastreamento.'",
			 "tracking_urls": [
			   "'.$url_rastreamento.'"
			 ],
			 "notify_customer": false
		   }
		 }';
    
        echo $json;
		try {		
		
			 $this->parametros['body'] = $json;					 
			 
			 $url      = "https://lart-du-vin-cristais.myshopify.com/admin/api/2021-04/orders/$shopify_id/fulfillments.json";
			 $response = $this->guzzle->request('POST', $url, $this->parametros);			 			 

			 if ($response->getStatusCode() == 200) {

				 $response = json_decode($response->getBody(),true);			
			
			 }
		
		} catch (Exception $e) {
		
			 $request = Psr7\str($e->getRequest());   
			
			 echo "\n REQUEST: \n".$request;			   

			 $retorno = "";
			 if ($e->hasResponse()) {
				 $retorno  = Psr7\str($e->getResponse());				   
			 }			   
			
			 $status_code = $e->getResponse()->getStatusCode();			   
			 $vet_res     = json_decode($e->getResponse()->getBody(),true);
			 $retorno     = (isset($vet_res[0]["code"])) ? $vet_res[0]["code"] : @$vet_res[0]["statusText"];
			 #$e->getResponse()->getReasonPhrase();
			
			 $log = "RESPONSE: \n\n".$status_code."\n\n".$retorno."\n\n";
			
			 echo $log;					 
			 
		} finally {					
		
		     return $retorno;
			
		}            
    
    }    

}