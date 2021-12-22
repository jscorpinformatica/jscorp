<?php
class Pagina
{
	var $header;
	var $content;
	var $footer;
	
	function __construct() 
	{
		$this->creatHeader();
		$this->creatFooter();
	}

	function MontaPagina()
	{
		global $header, $footer, $content;
		global $css;
				
	
		$css .= file_get_contents("includes/css/hover.min.css");
		$css .= file_get_contents("includes/css/jscorp_slider.min.css");
		$css  = file_get_contents("includes/css/estilo.min.css");
		$css = "<style>$css</style>";
		
		$header  = $this->header;
		$footer  = $this->footer;
		$content = $this->content;
		
		$casca  = template("casca.html");
		echo $casca;
	}
	
	function creatContent($content)
	{
		$this->content = $content;
	}

	function creatHeader()
	{
		$this->header = template("menu.html");
	}

	function creatFooter()
	{
		$this->footer = template("rodape.html");
	}

}
?>