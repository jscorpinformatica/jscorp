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


class Bling
{
    // property declaration
    private $total_pedido;    
    private $debug                 = false;
    private $token                 = "e1e275f44f6068d982f6a734119771afbbf7b933939738be12bc490669219b9c99ef0ca3";
    private $peso_produto          = 1.2;
    private $tacas_por_volume      = 6;
    private $loja                  = 203514101;
    private $forma_pagamento       = 1263488;

	public function __construct() {
        
		#header("Access-Control-Allow-Origin: *");
		#header("Access-Control-Allow-Headers: Content-Type");        
		        
		$this->guzzle = new Client();		   				
		
		#$parametros["headers"]["Accept"]          = "application/json";		
		#$parametros["headers"]["Content-type"]    = "application/json; charset=utf-8";	       

		$parametros['query']['apikey']            = $this->token;
		#$parametros["headers"]["Accept"]          = "*/*";
		#$parametros["headers"]["Cache-Control"]   = "no_cache";
		#$parametros["headers"]["Accept-Encoding"] = "gzip, deflate";		
		#$parametros["headers"]["Connection"]      = "keep-alive";
		$parametros["headers"]["Host"]            = "bling.com.br";		
		
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
    


    public function retorna_cliente($pedido,&$xml_cliente,&$obs_interna){
        
        /*        
        0 - cliente ja cadastrado
        1 - logradouro bate com o cep cadastrado
        2 - logradouro NAO bate com o cep cadastrado        
        */        
    
        $vet         = array();
        $obs_interna = "";
        
        
		try {
		
		     $url = "https://bling.com.br/Api/v2/contato/".$pedido["number"]."/json/";		     		     		     		      		     
		     		
		     $this->parametros['identificador']	= 'CPF';
		     		     
			 $response = $this->guzzle->request('GET', $url, $this->parametros);			 
			 
			 #echo $response->getStatusCode(); // 200
			 #echo $response->getHeaderLine('content-type'); // 'application/json; charset=utf8'
             
             switch ($response->getStatusCode()) {
             
                     case "200": 
						  $vet_contato = json_decode($response->getBody(),true);  						  
						  if (@is_array($vet_contato['retorno']['contatos'])){
						  
							  $contato = $vet_contato['retorno']['contatos'][0]['contato'];	
							  
							  # Se contato ativo
							  if ($contato["situacao"] == 'A') {
				 
				                      if ($contato["cep"] == $pedido["shipping_address"]["zip"]) // endereco nao mudou
				                      {
										  $xml_cliente =
												"<cliente>					   					   
													<id>".$contato["id"]."</id>
													<nome>".$this->$contato["nome"]."</nome>
													<tipoPessoa>".$contato["tipo"]."</tipoPessoa>
													<cpf_cnpj>".$contato["cnpj"]."</cpf_cnpj>
													<rg>".$contato["ie_rg"]."</rg>
													<contribuinte>".$contato["contribuinte"]."</contribuinte>						   
													<endereco>".$contato["endereco"]."</endereco>
													<numero>".$contato["numero"]."</numero>
													<complemento>".$contato["complemento"]."</complemento>
													<bairro>".$contato["bairro"]."</bairro>						   						   						   
													<cep>".$contato["cep"]."</cep>					   
													<cidade>".$contato["cidade"]."</cidade>						   						   						   
													<uf>".$contato["uf"]."</uf>						   						   						   						   
													<fone>".$contato["fone"]."</fone>
													<celular>".$contato["celular"]."</celular>	
													<email>".$contato["email"]."</email>
													<tipos_contatos><tipo_contato><descricao>Cliente</descricao></tipo_contato></tipos_contatos>																		   
												</cliente>";	
										   $retorno["status"] = 0;		
									  }	   
									  else $retorno = $this->monta_cliente_novo($pedido,$xml_cliente,$obs_interna);										  			 

							  } else $retorno = $this->monta_cliente_novo($pedido,$xml_cliente,$obs_interna);
							  
						  }
						  else $retorno = $this->monta_cliente_novo($pedido,$xml_cliente,$obs_interna);
						  
                     break;
                     
                     default:
						  $retorno = $this->monta_cliente_novo($pedido,$xml_cliente,$obs_interna);
                     break;
             
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
			 
			 $retorno = $this->monta_cliente_novo($pedido);	 
			 
		} finally {	
		
		     #exit(print_r($xml_cliente));						
		     return $retorno;
			
		}        
    
    }


    public function monta_cliente_novo($pedido,&$xml_cliente,&$obs_interna){
     
        $ret_cep = $this->busca_cep($pedido["shipping_address"]["zip"],$info_CEP);
        
		if ( !$ret_cep ) $obs_interna = "CEP ".$pedido["shipping_address"]["zip"]." errado";		
		
		# retira nao numericos
		$telefone = trim(preg_replace("/[^0-9]/i","",$pedido["shipping_address"]["phone"]));
		# retira o 55 do inicio do telefone
		$telefone = ( strlen($telefone) > 11 and substr($telefone,0,2) == 55 ) ? substr($telefone,2) : $telefone ;
		$telefone = ( strlen($telefone) > 11 and substr($telefone,0,1) == 0 )  ? substr($telefone,1) : $telefone ;		
		
		# (DD) 99999-9999
		$telefone = "(".substr($telefone,0,2).")"." ".substr($telefone,2,-4)."-".substr($telefone,-4);
		
		if (preg_match("/\d+/i",$pedido["shipping_address"]["address1"],$matches))
		    $numero = $matches[0];
		else{
		    preg_match('/\d+/',$pedido["shipping_address"]["address2"],$matches);
		    $numero = $matches[0];
		}

        $complemento = $pedido["shipping_address"]["address1"];				
	    $complemento = preg_replace('/\//', '', $complemento);
	    $complemento = preg_replace('/\./', '', $complemento);
	    $complemento = preg_replace('/,/', '', $complemento);
	    $complemento = preg_replace('/'.$numero.'/', '', $complemento);	    
	    $complemento = preg_replace('/'.$info_CEP["logradouro"].'/i', '', $complemento);
	    $complemento = trim($complemento);	    
	    		
		echo "\n ***** NUMERO => $numero ****** \n";
		
		$nome_cliente = ucwords(strtolower($pedido["shipping_address"]["name"]));
		$cpf = $pedido["shipping_address"]['company'];		
		
		$xml_email = (isset($pedido["email"])) ? $pedido["email"] : "";		
			
		# logradouro bate com o que foi digitado pelo cliente
		/*
		if ( $this->sanitizeString($pedido["shipping_address"]["address1"])  ==
			 $this->sanitizeString($info_CEP["logradouro"]) ) {			 
		*/
		
		# nao achou cep
		if ( !$ret_cep ) {
			 
			 $bairro = empty($info_CEP["bairro"]) ? "Centro" : $info_CEP["bairro"];
			 
			 $xml_cliente = 
				   "					   					   
					   <nome>".$nome_cliente."</nome>
					   <tipoPessoa>F</tipoPessoa>
					   <cpf_cnpj>".$cpf."</cpf_cnpj>
					   <cep>".$info_CEP["cep"]."</cep>					   
					   <endereco>".$info_CEP["logradouro"]."</endereco>					   
					   <numero>".$numero."</numero>
					   <complemento>".$complemento."</complemento>
					   <bairro>".$bairro."</bairro>
					   <cidade>".$info_CEP["localidade"]."</cidade>
					   <uf>".$info_CEP["uf"]."</uf>
					   <celular>".$telefone."</celular>
					   $xml_email
                   ";	
				   
				   
				   
			 $retorno["status"] = 1;
		
		}
		else {			         
	
			 $bairro = empty($pedido["shipping_address"]["address2"]) ? "Centro" : $pedido["shipping_address"]["address2"];
			 	
			 $xml_cliente =
				  "
					  <nome>".$nome_cliente."</nome>
					  <tipoPessoa>F</tipoPessoa>
					  <cpf_cnpj>".$cpf."</cpf_cnpj>
					  <cep>".$pedido["shipping_address"]["zip"]."</cep>					   
					  <endereco>".$pedido["shipping_address"]["address1"]."</endereco>					   
					  <numero>".$numero."</numero>
					  <complemento>".$complemento."</complemento>								
					  <bairro>".$bairro."</bairro>
					  <cidade>".$pedido["shipping_address"]["city"]."</cidade>
					  <uf>".$pedido["shipping_address"]["province_code"]."</uf>
					  <celular>".$telefone."</celular>
					  $xml_email				  
				  ";	
				   
			 $retorno["status"]     = 2;
		     $retorno["logradouro"] = $pedido["shipping_address"]["address1"];					 								 		     		     
		}			
			
		/*	
		$xml_cliente =
			 "
				 <nome>".$nome_cliente."</nome>
				 <tipoPessoa>F</tipoPessoa>
				 <cpf_cnpj>".$cpf."</cpf_cnpj>
				 <cep>".$info_CEP["cep"]."</cep>					   
				 <endereco>".$info_CEP["logradouro"]."</endereco>		
				 <numero>".$numero."</numero>
				 <complemento>".$complemento."</complemento>								
				 <bairro>".$info_CEP["bairro"]."</bairro>
				 <cidade>".$info_CEP["localidade"]."</cidade>
				 <uf>".$info_CEP["uf"]."</uf>				 
				 <celular>".$telefone."</celular>
				 $xml_email				  
			 ";	
			  
		$retorno["status"]     = 2;
		#$retorno["logradouro"] = $info_CEP["logradouro"];					 								 		     		     
		*/		


        # insere contato		

		$xml = "<contato>
				<contribuinte>9</contribuinte>
				$xml_cliente
				<tipos_contatos><tipo_contato><descricao>Cliente</descricao></tipo_contato></tipos_contatos>
			    </contato>";
		
		#echo '\n'.$xml.'\n';
		
		if ($this->insere_contato($xml,$contato_id))
			$xml_cliente = "<cliente><id>".$contato_id."</id>".$xml_cliente."</cliente>";			
		else 
		    $xml_cliente = "<cliente>".$xml_cliente."</cliente>";							
	
	    #echo "\n".$xml_cliente."\n";
	
		return $retorno;    
    
    }
    
    public Function insere_contato($xml,&$contato_id) {
        
        $retorno = false;
    
		try {		
		
			 $this->parametros['query']['xml'] = $xml;					 
			 
			 $url      = "https://bling.com.br/Api/v2/contato/json/";
			 $response = $this->guzzle->request('POST', $url, $this->parametros);			 			 

			 if ($response->getStatusCode() == 200) {

                 /*
				 <?xml version="1.0" encoding="utf-8"?>
				 <retorno>
					 <contatos>
						 <contato>
							 <id>7844924051</id>
							 <nome>Virginia Schemidt</nome>
							 <cpf_cnpj>086.532.497-29</cpf_cnpj>
						 </contato>
					 </contatos>
				 </retorno>			
				 */

				 $vet_contato = json_decode($response->getBody(),true);			
			
				 #echo "\n".var_dump($vet_contato)."\n";			
				
				 if (isset($vet_contato['retorno']['contatos']['contato']['id'])) {			
				     $contato_id  = $vet_contato['retorno']['contatos']['contato']['id'];				 
					 $retorno     = true;
				 }	 
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

    public function retorna_transportadora($vet_itens,$cep_original,$total_venda,&$obs_interna,$carrier){
    
        $cep = intval($cep_original);
        
        $peso_bruto   = 0.50;
        $qtde_produto = 0;
        foreach($vet_itens as $item) {            
            $qtde_produto += $item["quantity"];            
			$peso_bruto   += $item["quantity"] * $this->peso_produto;            
        }                    
                       
		$xml_transportadora = 
				   "<transporte>
					   <transportadora>COLORTIL</transportadora>						  
					   <tipo_frete>R</tipo_frete>
					   <servico_correios>".$carrier['title']."</servico_correios>
					   <peso_bruto>".$peso_bruto."</peso_bruto>
					   <qtde_volumes>".$qtde_produto."</qtde_volumes>					   
					</transporte>";                               
                
        return $xml_transportadora;
    }
    
    public function retorna_itens($vet_itens){
    
       $itens        = "";
       $total_pedido = 0;
       
       foreach($vet_itens as $item){
       
               $itens .=        
				   "<itens>
					   <item>
						   <codigo>".$item["sku"]."</codigo>
						   <qtde>".$item["quantity"]."</qtde>
						   <vlr_unit>".$item["price"]."</vlr_unit>
					   </item>
				   </itens>";
       
                $total_pedido += $item["quantity"];
       
       }
       
       $this->total_pedido = $total_pedido;
       
       return $itens;
    
    }    
	
    public function insere_pedido($pedido,&$vet_pedido) {    

        $vet_pedido = [];
        /*
        03/04/2020 - retirada do tag <dias> para que o bling calcule automaticamente baseado na forma de pagamento
        
				   <parcelas>
					   <parcela>
						   <dias>30</dias>
						   <vlr>".$pedido["items_total_sum"]."</vlr>
						   <forma_pagamento>
						       <id>".$this->cod_stripe."</id>						       
						   </forma_pagamento>
					   </parcela>
				   </parcelas>				           
        
        */
       
        # retira alfanumericos
        $pedido["shipping_address"]["zip"] = preg_replace("/[^0-9]/","",$pedido["shipping_address"]["zip"]);
       
        $retorna = false;
        
		try {		
		     
			 # calcula dias parcela
			 
			 $CONST_DIAS = 30;
			 $diasemana = array('Domingo','Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sabado');
			 $data = strftime( "%FT%T%z", mktime ( 12, 0, 0, date("m"), date("d")+$CONST_DIAS, date("Y") ) );

			 #echo $data."</br>";

			 $dia = date('w', strtotime($data));

			 switch ($diasemana[$dia]) {

					default:        $desloca = 0; break;
					case "Sabado" : $desloca = 2; break;	   
					case "Domingo": $desloca = 1; break;

			 }

             $dias_parcela = $CONST_DIAS + $desloca;
		     
		     # trata cliente
		     
			 $status_endereco = $this->retorna_cliente($pedido,$xml_cliente,$obs_interna);			 			 
		
		     $desconto = $pedido['total_discounts'];
		
		     # pega data do pedido vivino
		     $data = $pedido['created_at'];
		     $data = substr($data,8,2)."/".substr($data,5,2)."/".substr($data,0,4);		
		
		     $xml_transportadora = $this->retorna_transportadora($pedido["line_items"],$pedido["shipping_address"]["zip"],$pedido["total_price"],$obs_interna,$pedido["shipping_lines"][0]);

			 $this->parametros['query']['xml']  = 
			 "
			 <?xml version='1.0' encoding='UTF-8'?>
			   <pedido>
				   <numero_loja>".$pedido["id"]."</numero_loja>
				   <loja>".$this->loja."</loja>
				   <nat_operacao>Venda de mercadorias</nat_operacao>
				   <data>".$data."</data>
				   <data_saida>".$data."</data_saida>
				   <obs>".$pedido["shipping_address"]["address1"]."</obs>
				   <vlr_frete>".$pedido["total_shipping_price_set"]["shop_money"]["amount"]."</vlr_frete>	
				   <vlr_desconto>".$desconto."</vlr_desconto>
				   <idFormaPagamento>".$this->forma_pagamento."</idFormaPagamento>				   
				   <parcela></parcela>
                   ".$xml_cliente."
                   ".$xml_transportadora."
				   ".$this->retorna_itens($pedido["line_items"])."
			   </pedido>
			 ";
			 
			 $this->parametros['query']['xml'] = preg_replace('/\>\s+\</m', '><', $this->parametros['query']['xml']);
			 #exit($this->parametros['query']['xml']);
			 
			 /*		
			 $this->parametros['query']['xml']  = 
			 "
			 
			   <?xml version='1.0' encoding='UTF-8'?>
			   <pedido>
				   <numero_loja>".$pedido["id"]."</numero_loja>
				   <loja>".$this->loja."</loja>
				   <nat_operacao>Vendas</nat_operacao>
				   <data>".$data."</data>
				   <data_saida>".$data."</data_saida>
                   <obs_internas>".$obs_interna."</obs_internas>
				   <vlr_frete>".$pedido["items_shipping_sum"]."</vlr_frete>	
				   <vlr_desconto>".$desconto."</vlr_desconto>
				   <idFormaPagamento>".$this->cod_stripe_365."</idFormaPagamento>
				   <parcela></parcela>
                   ".$xml_cliente."
                   ".$xml_transportadora."
				   ".$this->retorna_itens($pedido["items"])."
			   </pedido>			 
			 
			 <?xml version='1.0' encoding='UTF-8'?>
			   <pedido>
				   <numero_loja>".$pedido["id"]."</numero_loja>
				   <loja>".$this->loja."</loja>
				   <nat_operacao>Vendas</nat_operacao>
				   <data>".$data."</data>
				   <data_saida>".$data."</data_saida>
                   <obs_internas>".$obs_interna."</obs_internas>
				   <vlr_frete>".$pedido["items_shipping_sum"]."</vlr_frete>	
				   <vlr_desconto>".$desconto."</vlr_desconto>
                   ".$xml_cliente."
                   ".$xml_transportadora."
				   ".$this->retorna_itens($pedido["items"])."
				   <parcelas>
					   <parcela>
						   <dias>".$dias_parcela."</dias>					   
						   <vlr>".$pedido["items_total_sum"]."</vlr>
						   <forma_pagamento>
						       <id>".$this->cod_stripe."</id>						       
						   </forma_pagamento>
					   </parcela>
				   </parcelas>				   
			   </pedido>
			 ";
			 */
		
		     #exit($this->parametros['query']['xml']);
		
		     $url = "https://bling.com.br/Api/v2/pedido/json/";			     		     
		     		     
		     #echo(var_dump($this->parametros));
		     		     
			 $response = $this->guzzle->request('POST', $url, $this->parametros);			 
			 
			 #echo $response->getStatusCode(); // 200
			 #echo  $this->parametros['query']['xml'];

			 #echo $response->getHeaderLine('content-type'); // 'application/json; charset=utf8'
			 $vet_pedido = json_decode($response->getBody(),true); // '{"id": 1420053, "name": "guzzle", ...}'			 
			
			 echo (var_dump($vet_pedido));
			 
			 if (isset($vet_pedido["retorno"]["pedidos"][0]["pedido"]["numero"])){
				 $bling_id =  $vet_pedido["retorno"]["pedidos"][0]["pedido"]["numero"];				 				 				 
			 
				 $retorna = true;
		     }		 
			 
		} catch (Exception $e) {
		
			 $request = Psr7\str($e->getRequest());   
			
			 echo "\n REQUEST: \n".$request;			   

			 $response = "";
			 if ($e->hasResponse()) {
				 $response  = Psr7\str($e->getResponse());				   
			 }			   
			
			 $status_code = $e->getResponse()->getStatusCode();			   
			 $vet_res     = json_decode($e->getResponse()->getBody(),true);
			 $response    = (isset($vet_res[0]["code"])) ? $vet_res[0]["code"] : @$vet_res[0]["statusText"];
			 #$e->getResponse()->getReasonPhrase();
			
			 $log = "RESPONSE: \n\n".$status_code."\n\n".$retorno."\n\n";
			
			 echo $log;
			 
			 
		} finally {
		
			 return $retorna;
		}
            
    }	


    public function retorna_pedido_especifico($bling_id, &$pedido) {    		

        $pedido  = array();
        $retorna = false;
        
		try {
		     				
		     $url = "https://bling.com.br/Api/v2/pedido/".$bling_id."/json/";			     		     
		     		     
		     #echo(var_dump($this->parametros));
		     		     
			 $response = $this->guzzle->request('GET', $url, $this->parametros);			 
			 
			 #echo $response->getStatusCode(); // 200
			 #echo $response->getHeaderLine('content-type'); // 'application/json; charset=utf8'
			 $vet_pedido = json_decode($response->getBody(),true); // '{"id": 1420053, "name": "guzzle", ...}'			 
			
			 if (isset($vet_pedido["retorno"]["pedidos"][0]["pedido"])) {
				 $pedido = $vet_pedido["retorno"]["pedidos"][0]["pedido"];
				 $retorna = true;
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
		
			 return $retorna;
		}
            
    }	

    public function busca_produto($codigo,&$vet_response){
        
        /*  idSituacao     
        6 - pedidos em aberto
        9 - pedidos atentido
        */        
    
        $resposta     = false;
        $vet_response = array();        
        
		try {
		
			 $url = "https://bling.com.br/Api/v2/produto/".$codigo."/json/";			 
		 
			 #exit(var_dump($this->parametros));
					 
			 $response = $this->guzzle->request('GET', $url, $this->parametros);			 
		 
			 #echo $response->getStatusCode(); // 200
			 #echo $response->getHeaderLine('content-type'); // 'application/json; charset=utf8'
		 
			 switch ($response->getStatusCode()) {
		 
					 case "200": 
						  $response     = json_decode($response->getBody(),true);
						  #echo(var_dump($response));
						  if (isset($response["retorno"]["produtos"])) {
						  		$vet_response = $response["retorno"]["produtos"][0]["produto"];
								$resposta = true;						  		
						  }		
					 break;
				 
					 default:
					 break;
		 
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
		     return $resposta;			
		}        
    
    }

    public function atualiza_produto($sku, $xml_produto,&$vet_response){
    
        $resposta     = false;
        $vet_response = array();        
        
		try {
		
			 $url = "https://bling.com.br/Api/v2/produto/".$sku."/json/";			 
			 
			 $this->parametros['form_params']['xml']    = rawurlencode($xml_produto);
		     
			 #exit(var_dump($this->parametros));
					 
			 $response = $this->guzzle->request('POST', $url, $this->parametros);			 
		 
			 #echo $response->getStatusCode(); // 200
			 #echo $response->getHeaderLine('content-type'); // 'application/json; charset=utf8'
		 
		     #exit(print_r($response));
		     
			 switch ($response->getStatusCode()) {
		 
					 case "200": 
					 case "201": 					 
						  $response     = json_decode($response->getBody(),true);
						  #var_dump($response);
						  if (isset($response["retorno"]["produtos"])) {
						  		$vet_response = $response["retorno"]["produtos"][0][0]["produto"];
								$resposta = true;						  		
						  }		
					 break;
				 
					 default:
					 break;
		 
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
		     return $resposta;			
		}        
    
    }

    
    public function insere_contas_receber($pedido, $data, &$retorno) {    

		$retorna = false;
					
		$data_emissao = $pedido['data']; // YYYY-MM-DD
		$data_emissao = substr($data,8,2)."/".substr($data,5,2)."/".substr($data,0,4); // DD/MM/YYYY
		
		# calcula dias parcela
		
		
		//$data = $pedido['nota']['dataEmissao']; // YYYY-MM-DD hh:mm:ss
		
		$dia = date("d",strtotime($data));
		$mes = date("m",strtotime($data));
		$ano = date("Y",strtotime($data)); 			 		
		
		$CONST_DIAS = 30;
		$diasemana = array('Domingo','Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sabado');
		if ($mes == 12) $ano++;
		$data = strftime( "%FT%T%z", mktime ( 12, 0, 0, $mes, $dia+$CONST_DIAS, $ano ) );

		#exit("$data | $dia | $mes | $ano");

		$dia_semana = date('w', strtotime($data));
		$dia        = date("d",strtotime($data));		
		$mes        = date("m",strtotime($data));		

		switch ($diasemana[$dia_semana]) {

			   default:        $desloca = 0; break;
			   case "Sabado" : $desloca = 2; break;	   
			   case "Domingo": $desloca = 1; break;

		}
		
		#exit("$data | $dia | $mes | $ano | $desloca");		

		#$dias_parcela    = $CONST_DIAS + $desloca;
		$data_vencimento = strftime( "%d/%m/%Y", mktime ( 12, 0, 0, $mes, $dia+$desloca, $ano ) );
		$data_formatada   = strftime( "%Y/%m/%d", mktime ( 12, 0, 0, $mes, $dia+$desloca, $ano ) );		
		$dia_vencimento  = date("d",strtotime($data_formatada));
		$mes_vencimento  = date("m",strtotime($data_formatada));
		$ano_vencimento  = date("Y",strtotime($data_formatada));				
		
		#exit("$data_vencimento | $dia_vencimento | $mes_vencimento | $ano_vencimento");
	   
		# NF
		$nf       = $pedido['nota']['numero'].'/'.str_pad($pedido['nota']['serie'], 2, "0", STR_PAD_LEFT);
		$nf_valor = $pedido['nota']['valorNota'];

        /*
		$this->parametros['query']['xml']  = 
		'
		<?xml version="1.0" encoding="UTF-8"?>
		<contareceber>
			<dataEmissao>'.$data_emissao.'</dataEmissao>
			<vencimentoOriginal>'.$data_vencimento.'</vencimentoOriginal>
			<competencia>'.$data_emissao.'</competencia>
			<nroDocumento>'.$nf.'</nroDocumento>
			<valor>'.$nf_valor.'</valor>
			<historico>Ref. ao pedido de venda nº '.$pedido['numero'].' | '.$pedido['numeroPedidoLoja'].'</historico>
			<categoria>Vendas</categoria>
			<idFormaPagamento>'.$this->cod_stripe.'</idFormaPagamento>
			<portador>CEF</portador>
			<vendedor>Vivino</vendedor>
			<ocorrencia>
			   <ocorrenciaTipo>U</ocorrenciaTipo>
			   <diaVencimento>'.$dia_vencimento.'</diaVencimento>
			</ocorrencia>
			<cliente>
			   <nome>'.$pedido['cliente']['nome'].'</nome>
			   <id>'.$pedido['cliente']['id'].'</id>
			</cliente>
		 </contareceber>
        */
	   
		$this->parametros['query']['xml']  = 
		'
		<?xml version="1.0" encoding="UTF-8"?>
		<contareceber>
			<dataEmissao>'.$data_emissao.'</dataEmissao>
			<vencimentoOriginal>'.$data_vencimento.'</vencimentoOriginal>
			<competencia>'.$data_emissao.'</competencia>
			<nroDocumento>'.$nf.'</nroDocumento>
			<valor>'.$nf_valor.'</valor>
			<historico>Ref. ao pedido de venda nº '.$pedido['numero'].' | '.$pedido['numeroPedidoLoja'].'</historico>
			<categoria>Vendas de produtos</categoria>
			<idFormaPagamento>'.$this->cod_stripe.'</idFormaPagamento>
			<portador>CEF</portador>
			<ocorrencia>
			   <ocorrenciaTipo>U</ocorrenciaTipo>
			</ocorrencia>
			<cliente>
			   <nome>'.$pedido['cliente']['nome'].'</nome>
			   <id>'.$pedido['cliente']['id'].'</id>
			</cliente>
		 </contareceber>
		 ';		
  
        #exit($this->parametros['query']['xml']);
		# chama api bling
		try {
			 $url = "https://bling.com.br/Api/v2/contareceber/json/";			     		     
				   
			 #echo(var_dump($this->parametros));
				   
			 $response = $this->guzzle->request('POST', $url, $this->parametros);			 
	   
			 #echo $response->getStatusCode(); // 200
			 #echo  $this->parametros['query']['xml'];
			 #echo $response->getHeaderLine('content-type'); // 'application/json; charset=utf8'

			 $retorno = json_decode($response->getBody(),true); // '{"id": 1420053, "name": "guzzle", ...}'			 
			 $conta_receber_id = $retorno['retorno']['contasreceber'][0]['contaReceber']['id'];
			 #exit($conta_receber_id);			 
			 
			 #print_r($retorno);
			 
			 $retorna = true;
			 
	   
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
  
			 return $retorna;
		}       
		
            
    }	
    
	public function busca_dados_nf($notafiscal,$serie,&$vet_nf){        
    
        $retorno = false;
        $vet_nf  = array();        
        
		try {
		
		     $url = "https://bling.com.br/Api/v2/notafiscal/".$notafiscal."/".$serie."/json/";		     		
		     		     
			 $response = $this->guzzle->request('GET', $url, $this->parametros);			 
			 
			 #echo $response->getStatusCode(); // 200
			 #echo $response->getHeaderLine('content-type'); // 'application/json; charset=utf8'
             
             $vet_nf = array();
             
             switch ($response->getStatusCode()) {
             
                     case "200": 
						  $vet_nf = json_decode($response->getBody(),true);  						  
						  						  
						  if (isset($vet_nf['retorno']['notasfiscais'])){
						  
						      echo "NF existe...";
							  $vet_nf  = $vet_nf['retorno']['notasfiscais'][0]['notafiscal'];	
							  $retorno = true;
							  							  
						  }
						  else echo "NF NAO existe... \n";
						  
                     break;                     
             
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
			 
			 $retorno = $this->monta_cliente_novo($pedido);	 
			 
		} finally {	
		
		     #print_r($vet_nf);
		     return $retorno;
			
		}        
    
    }    
    
	public function busca_cep($cep,&$vet_cep){
	
	    $retorno = false;
	    
	    try {
		
			 require '/var/www/cliente/jscorp/clientes/lartduvin/vendor/jarouche/viacep/src/BuscaViaCEP_inc.php';
		
             
			 #echo "busca_cep $cep ...";
		   
			 $class = new Jarouche\ViaCEP\BuscaViaCEPJSONP();
			 $class->setCallbackFunction('resultado_cep');	
			 $result = $class->retornaCEP($cep);			 
			 /*
			 Array
			 (
				 [cep] => 01311-300
				 [logradouro] => Avenida Paulista
				 [complemento] => de 1867 ao fim - lado ímpar
				 [bairro] => Bela Vista
				 [localidade] => São Paulo
				 [uf] => SP
				 [unidade] =>
				 [ibge] => 3550308
				 [gia] => 1004
			 )		
			 
			 ERRO
			 array(2) {
			   ["error"]=>
			   array(2) {
				 ["descricao"]=>
				 string(24) "CEP de destino incorreto"
				 ["id"]=>
				 int(-1)
			   }
			   ["frete"]=>
			   array(1) {
				 [0]=>
				 array(12) {
				   ["cepdes"]=>
				   string(7) "1425001"
				   ["cepori"]=>
				   string(8) "22765240"
				   ["cnpj"]=>
				   string(14) "35787111000174"
				   ["conta"]=>
				   string(5) "79707"
				   ["contrato"]=>
				   string(7) "0161852"
				   ["frap"]=>
				   string(1) "N"
				   ["modalidade"]=>
				   int(3)
				   ["peso"]=>
				   float(4.203)
				   ["tpentrega"]=>
				   string(1) "D"
				   ["tpseguro"]=>
				   string(1) "N"
				   ["vlcoleta"]=>
				   float(0)
				   ["vldeclarado"]=>
				   float(539.7)
				 }
			   }
			 }			   
			 */
			
			 if ( !isset($result['error']) ){ 
			     $vet_cep = $result;
			     $retorno = true;
			 }    
	
            
		} catch (Exception $e) {		
			
			 echo "\n REQUEST: \n".$e;			   			 
			 
		} finally {					
		
		     return $retorno;
			
		}  			
			
	}        
    

	public function sanitizeString($str) {

	   $str = strtolower($str);
	   $str = preg_replace('/[áàãâä]/ui', 'a', $str);
	   $str = preg_replace('/[éèêë]/ui', 'e', $str);
	   $str = preg_replace('/[íìîï]/ui', 'i', $str);
	   $str = preg_replace('/[óòõôö]/ui', 'o', $str);
	   $str = preg_replace('/[úùûü]/ui', 'u', $str);
	   $str = preg_replace('/[ç]/ui', 'c', $str);
	   // $str = preg_replace('/[,(),;:|!"#$%&/=?~^><ªº-]/', '_', $str);
	   $str = preg_replace('/[^a-z0-9]/i', '_', $str);
	   $str = preg_replace('/_+/', '_', $str); // ideia do Bacco :)
	   
	   # retira problemas de typo
	   $str = preg_replace('/av./i', '', $str);
	   $str = preg_replace('/r./i', '', $str);
	   $str = preg_replace('/rua/i', '', $str);
	   $str = preg_replace('/avenida/i', '', $str);	   	   	   	   
	   
	   $str = trim($str);
	   
	   return $str;
	}	


}