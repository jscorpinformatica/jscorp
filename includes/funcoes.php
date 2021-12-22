<?php

function template($nome_arquivo)
{
	global $path_templates;
	
	$output="";

	#$filename = explode("/inovaar", getcwd());

	$filename    = explode("/var/www/cliente", getcwd());
	$filename[1] = str_replace("/", "", $filename[1]);
	$filename    = "/var/www/cliente/".$filename[1]."/includes/template/$nome_arquivo";

	//echo $filename."<br>";
		
	$handle = @fopen($filename, "r");
	
	if ($handle) 
	{
		while (!feof($handle))
		{
			$buffer = fgets($handle, 4096);
			while((strpos($buffer,"<var=")>0) and (strpos($buffer,"</var>")>0))
			{
				#echo "$buffer = ".$nomevar."<br>";

				$temp    = substr($buffer,strpos($buffer,"<var="),strpos($buffer,"</var>") - strpos($buffer,"<var=")+6);
				$nomevar = trim(substr($temp,strpos($temp,"=")+1, strpos($temp,">") - strpos($temp,"=")-1));
				#@$substituicao = $_SESSION[$nomevar];
				@$substituicao = $GLOBALS[$nomevar];
				
				if($_SESSION[$nomevar] <> "") @$substituicao = $_SESSION[$nomevar];
				
				if($nomevar == "cliente_nome")
				{
					#echo "=>". $GLOBALS[$nomevar] . " - ". $_SESSION[$nomevar] ."<br>";
				}
								
				@$buffer = substr($buffer,0,strpos($buffer,"<var=")) . $substituicao . substr($buffer,strpos($buffer,"</var>")+6);

			}
			$output.= trim($buffer);
		}
		fclose($handle);
	}
	
	return($output);	
}

function anti_injection($sql)
{
	// remove palavras que contenham sintaxe sql
	$sql = preg_replace("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/i","",$sql);
	#$sql = preg_replace(my_Sql_regcase("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/"),"",$sql);
	
	$sql = trim($sql);//limpa espaços vazio
	$sql = strip_tags($sql);//tira tags html e php
	$sql = addslashes($sql);//Adiciona barras invertidas a uma string
	$sql = trim($sql);
	return $sql;
}

function my_substring($txt, $tam)
{
	if (strlen($txt) <= $tam) return $txt;
	
	$txt = preg_replace("/\n/i", " ", $txt);
	
	$tam_inicial = $tam;
	while (($tam>0) && ($char != ' '))
	{
		$char = substr($txt, $tam-1, 1);
		$tam--;
	}
	if ($tam>0) return substr($txt, 0, $tam)."..";
	else return substr($txt, 0, $tam_inicial);
}

function f_trata_campo_insert(&$campos)
{
	foreach($campos as $tipo => $campos)
	{
		foreach($campos as $parametro=>$valor)
		{
			$lista_parametro[] = $parametro;
			if($tipo=="var")   $lista_valor[] = "'".$valor."'";
			if($tipo=="mysql") $lista_valor[] = $valor;
		}
	}
	
	$campos    = NULL; # RESTART O VETOR
	
	$parametro = implode(",", $lista_parametro);
	$valor     = implode(",", $lista_valor);
	
	$retorno['parametro'] = $parametro;
	$retorno['valor'] = $valor;

	return $retorno;
}
?>
