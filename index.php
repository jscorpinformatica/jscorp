<?php

#error_reporting(E_ALL);
#ini_set('display_errors', 1);

header('Content-Type: text/html; charset=UTF-8');

session_start();
include_once("includes/funcoes.php");
include_once("includes/classes/pagina.class.php");
include_once("includes/classes/mysql.class.php");
include_once("includes/classes/PHPMailer-master/PHPMailerAutoload.php");

$pageMeta_title       = "JS Corp | Soluções Digitais";
$pageMeta_description = "A JS Corp foi montada por profissionais de Marketing, TI, e Web Design com larga experiência em Soluções de Internet e Marketing Digital.";
$pageMeta_keywords    = "JS Corp, soluções digitais, email marketing, ti, outsourcing ti, Web Metrics, site responsivo, responsivo, Soluções customizadas, Presença Digital, infraestrutura de cloud, cloud, infraestrutura";
$pageDataLayer        = "dataLayer = [{'site':'JS Corp', pagina:'Home'}];";

$request_array = anti_injection($_REQUEST["opcao"]);
@$get_list = explode("/", $request_array);

$opcao = $get_list[0];

#echo("OPCAO: $opcao<br>");
#exit();

switch($opcao)
{
	default:
		f_home();
	break;

	case "produtos":
		f_detalhe_produto();
	break;
	
	case "formOptIn":
		f_formOptIn();
	break;
	
	case "formContato_send":
		f_formContato_send();
	break;

	case "formNewsletter_send":
		f_formNewsletter_send();
	break;
}


function f_home()
{
	$content = NULL;
	// $content .= template("slider.html");
	$content .= template("banner.html");
	$content .= template("quem_somos.html");
	$content .= template("produtos.html");
	$content .= template("clientes_parceiros.html");
	$content .= template("fale_conosco.html");
		
	$pagina = new Pagina();
	$pagina->creatContent($content);
	$pagina->MontaPagina();
	
}

function f_detalhe_produto()
{
	global $nome_produto;
	global $produtos_menu, $formOptIn, $produto;
	global $pageMeta_title, $pageDataLayer, $pageMeta_description;
	
	$request_array = anti_injection($_REQUEST["opcao"]);
	@$get_list = explode("/", $request_array);
	$produto = $get_list[1];

    /*
	$DB = new Jscorp_DB;
	
	$query = "SELECT * FROM produto WHERE slug = '$produto'";
	$DB->query($query);
	$DB->next_record();
	$nome_produto = $DB->f('nome');
	*/
	
	$produtos_menu = template("produtos/produtos_menu.html");
	$formOptIn     = template("formulario_optin.html");
	$content       = template("produtos/$produto.html");

	$produto        = ucfirst($produto);
	$produto        = str_replace("-"," ",$produto);
	$pageMeta_title = "JS Corp | $produto";
	$pageDataLayer  = "dataLayer = [{'site':'JS Corp', pagina:'$produto'}];";





// 	if($produto == "email-marketing")
// 		$pageMeta_description="A JS possui ferramentas de e-mail marketing para campanhas de publicidade e gerenciamento de listas de e-mails, newsletter e campanhas.";
// 	if($produto == "outsourcing")
// 		$pageMeta_description="Sua empresa reduzirá seus custos nessa área e a JS entregará o serviço de outsourcing focado para a sua empresa. Contrate um dos planos dos modelos disponíveis na JS e tenha a flexibilidade que você precisa para focar no seu negócio, aumentando sua produtividade.";
// 	if($produto == "metrics")
// 		$pageMeta_description="Um dos grandes diferenciais de se ter seu negócio na Internet é poder mensurar suas ações e verificar se o online da sua empresa está indo bem.";
// 	if($produto == "responsivo")
// 		$pageMeta_description="Com o novo algorítimo do Google, chamado Page Layout, está priorizando os sites responsivos ou seja, sites que se adaptam a qualquer tipo de tamanho de tela, gerando uma boa experiência em: Celulares, Tablets, Computadores (desktop com telas maiores ou menores)";
// 	if($produto == "solucoes-customizadas")
// 		$pageMeta_description="Precisa desenvolver sites corporativos, hot sites, lojas virtuais, landing pages, aplicativos facebook, startups internet";
// 	if($produto == "parceria-de-midia")
// 		$pageMeta_description="Gere tráfego para o seu site e aumente as chances de sucesso do seu negócio!";
// 	if($produto == "presenca-digital")
// 		$pageMeta_description="Você tem um negócio e ainda não está na Internet? Adquira o pacote Presença Digital e turbine seu negócio!";
// 	if($produto == "outsourcing")
// 		$pageMeta_description="A solução de gestão inclui os serviços de manutenção e assistência técnica especializada para servidores Cloud-Amazon, promovida por uma equipe especializada. Reduza a complexidade na gestão de sua infraestrutura de cloud";


	$pagina = new Pagina();
	$pagina->creatContent($content);
	$pagina->MontaPagina();
}

function f_formNewsletter_send()
{
	$email_newsletter = anti_injection($_REQUEST["email_newsletter"]);

	######################################## CHECK CAMPOS
	if(empty($email_newsletter)) exit("erro");
	if(!filter_var($email_newsletter, FILTER_VALIDATE_EMAIL)) exit('erroEmail');

	######################################## OPT-IN
	$formOptinId = "MjE1Nl85NjY=";
	$url  = "http://www.iqdirect.com.br/iqdirect/config/form.php";
	$url .= "?id=$formOptinId";
	$url .= "&valor_email=".urlencode($email_newsletter);
	file_get_contents($url);

	######################################## MSG
	echo "ok";
}

function f_formContato_send()
{
	global $homeContato_pagina, $homeContato_categoria;
	global $homeContato_nome, $homeContato_email, $homeContato_empresa;
	global $homeContato_telefone, $homeContato_local, $homeContato_data;
	global $homeContato_Npessoa, $homeContato_mensagem;

	
	$homeContato_pagina   = anti_injection($_REQUEST["homeContato_pagina"]);
	$homeContato_categoria= anti_injection($_REQUEST["homeContato_categoria"]);
	
	$formContato_nome     = anti_injection($_REQUEST["formContato_nome"]);
	$formContato_email    = anti_injection($_REQUEST["formContato_email"]);
	$formContato_empresa  = anti_injection($_REQUEST["formContato_empresa"]);
	$formContato_telefone = anti_injection($_REQUEST["formContato_telefone"]);
	$formContato_mensagem = anti_injection($_REQUEST["formContato_mensagem"]);

	// echo 	$formContato_nome."<br>";
	// echo 	$formContato_email."<br>";
	// echo 	$formContato_empresa."<br>";
	// echo 	$formContato_telefone."<br>";
	// echo 	$formContato_mensagem."<br>";
	// exit();

	######################################## CHECK CAMPOS
	if(empty($formContato_email))    exit('erro');
	if(empty($formContato_telefone)) exit('erro');
	if(!filter_var($formContato_email, FILTER_VALIDATE_EMAIL)) exit('erroEmail');


	######################################## CHECK CAPCHA
	$googleSecret   = "6LcNkh8TAAAAACeFCULwXY1-2u_z12Km2Mbg6RoD";
	$googleResponse = $_POST['g-recaptcha-response'];
	$resposta = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$googleSecret."&response=".$googleResponse."&remoteip=".$_SERVER['REMOTE_ADDR']);
	#if(!$resposta.success) exit('captchaErro');
	if(!preg_match("/true/i", $resposta)) exit('captchaErro');

	######################################## NOVO INSERT
	$campos['var']['nome']       = $formContato_nome;
	$campos['var']['email']      = $formContato_email;
	$campos['var']['empresa']    = $formContato_empresa;
	$campos['var']['telefone']   = $formContato_telefone;
	$campos['var']['msg']        = $formContato_mensagem;
	$campos['var']['produto']    = "Contato Home";
	$campos['var']['ip']         = $_SERVER["REMOTE_ADDR"];

	$insertCampo = f_trata_campo_insert($campos);
	$parametro   = $insertCampo['parametro'];
	$valor       = $insertCampo['valor'];


	$DB = new Jscorp_DB;
	$query = "INSERT INTO pedido ($parametro) VALUES ($valor)";
	$DB->query($query);


	######################################## OPT-IN
	$formOptinId = "MjE1Nl85NjU=";
	$url  = "http://www.iqdirect.com.br/iqdirect/config/form.php";
	$url .= "?id=$formOptinId";
	$url .= "&valor_nome=".urlencode($formContato_nome);
	$url .= "&valor_email=".urlencode($formContato_email);
	$url .= "&valor_empresa=".urlencode($formContato_empresa);
	$url .= "&valor_telefone=".urlencode($formContato_telefone);
	$url .= "&valor_orcamento=".urlencode($formContato_mensagem);
	file_get_contents($url);
	
	
	######################################## ENVIO DE EMAIL 
	$email = "comercial@jscorp.com.br";
	$body  = "Contato - Site JS Corp (HOME)<br><br>";
	$body .= "Nome: $formContato_nome<br>";
	$body .= "Email: $formContato_email<br>";
	$body .= "Empresa: $formContato_empresa<br>";
	$body .= "Telefone: $formContato_telefone<br>";
	$body .= "Mensagem: $formContato_mensagem<br>";
	$body .= "IP: ".$_SERVER["REMOTE_ADDR"]."<br>";
	f_envio_email($email, $body);
	
	######################################## MENSAGEM 
	echo "ok";
}

function f_formOptIn()
{	
	$formContato_nome     = anti_injection($_REQUEST["formContato_nome"]);
	$formContato_email    = anti_injection($_REQUEST["formContato_email"]);
	$formContato_telefone = anti_injection($_REQUEST["formContato_telefone"]);
	$formContato_mensagem = anti_injection($_REQUEST["formContato_mensagem"]);
	$formOptinId          = anti_injection($_REQUEST["id"]);
	$produto              = anti_injection($_REQUEST["produto"]);


	######################################## CHECK CAMPOS
	if(empty($formContato_email))    exit('erro');
	if(empty($formContato_telefone)) exit('erro');

	if(!filter_var($formContato_email, FILTER_VALIDATE_EMAIL)) exit('erroEmail');
	#if(strlen($formContato_telefone) <= 13 ) exit('erroTelefone');
	
	######################################## CHECK CAPCHA
	$googleSecret   = "6LcNkh8TAAAAACeFCULwXY1-2u_z12Km2Mbg6RoD";
	$googleResponse = $_POST['g-recaptcha-response'];
	$resposta = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$googleSecret."&response=".$googleResponse."&remoteip=".$_SERVER['REMOTE_ADDR']);
	#if(!$resposta.success) exit('captchaErro');
	if(!preg_match("/true/i", $resposta)) exit('captchaErro');

	
	

	######################################## NOVO INSERT
	$campos['var']['nome']       = $formContato_nome;
	$campos['var']['email']      = $formContato_email;
	$campos['var']['msg']        = $formContato_mensagem;
	$campos['var']['telefone']   = $formContato_telefone;
	$campos['var']['produto']    = $produto;
	$campos['var']['ip']         = $_SERVER["REMOTE_ADDR"];
	
	$insertCampo = f_trata_campo_insert($campos);
	$parametro   = $insertCampo['parametro'];
	$valor       = $insertCampo['valor'];
	
	
	$DB = new Jscorp_DB;
	$query = "INSERT INTO pedido ($parametro) VALUES ($valor)";
	$DB->query($query);

	######################################## OPT-IN
	$url  = "http://www.iqdirect.com.br/iqdirect/config/form.php";
	$url .= "?id=$formOptinId";
	$url .= "&valor_nome=".urlencode($formContato_nome);
	$url .= "&valor_email=".urlencode($formContato_email);
	$url .= "&valor_orcamento=".urlencode($formContato_mensagem);
	$url .= "&valor_produto=".urlencode($produto);
	$url .= "&valor_telefone=".urlencode($formContato_telefone);
	file_get_contents($url);

	
	######################################## ENVIO DE EMAIL 
	$email = "comercial@jscorp.com.br";

	$body  = "Pedido de Orçamento - Site JS Corp<br><br>";
	$body .= "Nome:     $formContato_nome<br>";
	$body .= "Email:    $formContato_email<br>";
	$body .= "Telefone: $formContato_telefone<br>";
	$body .= "Produto:  $produto<br>";
	$body .= "Mensagem: $formContato_mensagem<br>";
	$body .= "IP:       ".$_SERVER["REMOTE_ADDR"]."<br>";

	f_envio_email($email, $body);
	
	######################################## MENSAGEM 
	echo "ok";
}

function f_envio_email($email, $body)
{	
	$mail = new PHPMailer();
	// Define os dados do servidor e tipo de conexão
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	$mail->IsSMTP(); // Define que a mensagem será SMTP
	$mail->CharSet = 'UTF-8';
	$mail->SMTPDebug = 0; // Debugar: 0=desligado, 1 = erros e mensagens, 2 = mensagens apenas

	$mail->Host = "smtp.gmail.com"; // Endereço do servidor SMTP (caso queira utilizar a autenticação, utilize o host smtp.seudomínio.com.br)
	#$mail->Port = 465;
	$mail->SMTPSecure = "tls";
	$mail->Port = 587;
	#$mail->SMTPSecure = "ssl";
	$mail->SMTPAuth = true; // Usar autenticação SMTP (obrigatório para smtp.seudomínio.com.br)
	$mail->Username = "parceiro@jscorp.com.br"; // Usuário do servidor SMTP (endereço de email)
	$mail->Password = "jscorp01"; // Senha do servidor SMTP (senha do email usado)
	
	// Define o remetente
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	$mail->From     = "contato@jscorp.com.br"; // Seu e-mail
	$mail->Sender   = "contato@jscorp.com.br"; // Seu e-mail
	$mail->FromName = "JS Corp"; // Seu nome

	// Define os destinatário(s)
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	#if($_SERVER["REMOTE_ADDR"] == "152.237.75.237")
	#$mail->AddAddress("rsroriz@gmail.com");
	#else
	$mail->AddAddress($email);
	#$mail->AddCC('tecnologia@jscorp.com.br'); // Copia
	#$mail->AddBCC('rsroriz@gmail.com'); // Cópia Oculta

	// Define os dados técnicos da Mensagem
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	$mail->IsHTML(true); // Define que o e-mail será enviado como HTML
	//$mail->CharSet = 'iso-8859-1'; // Charset da mensagem (opcional)

	// Define a mensagem (Texto e Assunto)
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	$mail->Subject = "Solicitação de Orçamento - Site JS Corp"; // Assunto da mensagem
	$mail->Body    = $body;
	$mail->AltBody = 'Este é o corpo da mensagem de teste, em Texto Plano!';

	// Define os anexos (opcional)
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	//$mail->AddAttachment("/home/login/documento.pdf", "novo_nome.pdf");  // Insere um anexo
	
	if($_SERVER["REMOTE_ADDR"]=="152.237.75.237")
	{
		#echo $mail->ErrorInfo;
		#return "";
	}
	
	// Envia o e-mail
	$mail->Send();

	$mail->ClearAllRecipients();
	$mail->ClearAttachments();
}
?>
