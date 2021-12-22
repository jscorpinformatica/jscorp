$(function(){
	// VARIAVEIS
	var msg;
	var url      = window.location.href;
	var pathname = window.location.pathname;

	
	// EVENTOS PERSONALIZADOS
	$('#contatoTelefone').click(function(){
		dataLayer.push({'event':'Click Rodape Telefone', 'pagina':dataLayer[0]['pagina']});
	});
	
	// EVENTOS PERSONALIZADOS
	$('#contatoTelefone').click(function(){
		dataLayer.push({'event':'Click Rodape Email', 'pagina':dataLayer[0]['pagina']});
	});

	
	
	// MASCARAS	
	$('#formContato_telefone').mask("(99) 9999-9999?9");


	// MENU SCROLL
	function scrollToAnchor(aid){
    	var aTag = $(aid);
    	var reducao = 70;

		// FECHA MENU
		if($(window).width() < 1024) {
			$("#site_menu").slideUp("slow");
			$("#menu-aparece").css("display", "initial");
			$("#menu-some").css("display", "none");
		}

    	if(aTag.attr('id') == "slider") reducao = 300;
    	if($(window).width() < 1024)    reducao = 60;
    	
    	$('html,body').animate({scrollTop: aTag.offset().top - reducao}, 'slow');    	
	}


	// MENU + SCROLL	
	$('#menu_home').click(function(){
		if(pathname == "/"){ scrollToAnchor('#banner'); return false;}
	});
	
	$('#menu_quem_somos').click(function(){
		if(pathname == "/"){ scrollToAnchor('#quem_somos'); return false;}
	});

	$('#menu_produtos').click(function(){
		if(pathname == "/"){ scrollToAnchor('#produtos'); return false;}
	});
	
	$('#menu_clientes').click(function(){
		if(pathname == "/"){ scrollToAnchor('#clientes'); return false;}
	});
	
	$('#menu_fale_conosco').click(function(){
		if(pathname == "/"){ scrollToAnchor('#contato'); return false;}
	});
	
	$('.orcamento_btn').click(function(){
		scrollToAnchor('#contato');
	});


	$("#formContato_telefone").click(function(){
		if($(this).val() == "(__) ____-_____") $(this).get(0).setSelectionRange(0,0);
	});	
	
	// FORMS HOME
	// SITE [ajaxForm]: http://malsup.com/jquery/form/#ajaxForm 
	$("#form_newsletter").ajaxForm({beforeSubmit: validate, clearForm: false, success:function(responseText){
		dataLayer.push({'event':'envio_formulario' ,'pagina':dataLayer[0]['pagina'], 'rotulo':'Cadastro Newsletter'});

		if(responseText == "ok")        msg = "E-mail cadastrado com sucesso!";
		if(responseText == "erro")      msg = "Preencha os campos obrigatórios";
		if(responseText == "erroEmail") msg = "Email incorreto";
		if(responseText == "captchaErro") msg = "Utilize o Captcha";

		msgOverlay(msg, responseText, true);
	}});
	
	$("#form_contato").ajaxForm({beforeSubmit: validate, clearForm: false, success:function(responseText){
		// SITE [ajaxForm]: http://malsup.com/jquery/form/#ajaxForm
		dataLayer.push({'event':'envio_formulario' ,'pagina':dataLayer[0]['pagina'], 'rotulo':'Home Contato'});
		console.log("form_contato: " + responseText);
		
		if(responseText == "ok")        msg = "Pedido enviado com sucesso!";
		if(responseText == "erro")      msg = "Preencha os campos obrigatórios";
		if(responseText == "erroEmail") msg = "Email incorreto";
		if(responseText == "captchaErro") msg = "Utilize o Captcha";

		msgOverlay(msg, responseText, true);
	}});

	// FORM PAGINA PRODUTO
	// SITE [ajaxForm]: http://malsup.com/jquery/form/#ajaxForm 
	$("#form-optin").ajaxForm({beforeSubmit: validate, clearForm: false, success:function(responseText){
		dataLayer.push({'event':'envio_formulario' ,'pagina':dataLayer[0]['pagina'], 'rotulo':'Orçamento'});
		console.log("form-optin: "+ responseText);
		
		if(responseText == "ok")        msg = "Pedido enviado com sucesso!";
		if(responseText == "erro")      msg = "Preencha os campos obrigatórios";
		if(responseText == "erroEmail") msg = "Email incorreto";
		if(responseText == "captchaErro") msg = "Utilize o Captcha";

		msgOverlay(msg, responseText, true);
	}});

	function msgOverlay(msg, tipo, fadeOutFlag)
	{
		var cor = "#A7CA42";
		if(tipo == "erro")        cor = "#e74c3c";
		if(tipo == "captchaErro") cor = "#e74c3c";
		
		console.log("COR: "+ cor);
		
		$("#overlay_msg").css({"background-color": cor});		
		$("#overlay_msg").fadeIn();
		$("#overlay_msg").fadeIn("slow");
		$("#overlay_msg").fadeIn(3000);
		$("#overlay_msg_text").html(msg);
		$('input[type=submit]').attr('disabled', true);
		
		if(fadeOutFlag)	
		setTimeout(function(){
			$('input[type=submit]').attr('disabled', false);
			
			$("#overlay_msg").fadeOut();
			$("#overlay_msg").fadeOut("slow");
			$("#overlay_msg").fadeOut(3000);
			
			var onloadCallback = function(){ 
				console.log("captch");
				//grecaptcha.render('js-g-recaptcha', {'sitekey':'6LcNkh8TAAAAAJLQ2KoDXg8FqGBP2f_UoUG0mi0h', 'callback':verifyCallback, 'theme':'light'});
			};
			
			//grecaptcha.render('js-g-recaptcha', {'sitekey':'6LcNkh8TAAAAAJLQ2KoDXg8FqGBP2f_UoUG0mi0h', 'theme':'light'});
			//grecaptcha.reset();
			
			if(tipo == "ok"){ $('input[type="text"]').val(''); $('input[type="email"]').val('');}
		}, 3000);
	}
	
	function validate(formData, jqForm, options)
	{	
		msgOverlay("Aguarde", "Aguarde", true);
		
		for (var i=0; i < formData.length; i++){ 
			if(formData[i].required==true && formData[i].value==""){
				msgOverlay("Preencha os campos obrigatórios", 'erro', true);
				return false; 
			} 
		}		
	}
	


	// VOLTAR PAG ANTERIOR
	$('#album_btn_voltar').click(function(){
		parent.history.back();
		return false;
	});
	
	// FORM EVENTOS-ESCOLHA
	$('.tipo-evento').click(function()
	{
		$('.tipo-evento').each(function(i, obj) {
			$(this).css("border", "1px solid #888");
		});
	
		$(this).css("border", "4px solid #21b4d0");
		var evento = $(this).attr('evento');
		var pagina = dataLayer[0]['pagina'];
		
		//ga('send', 'event', evento, 'click', 'label');
		//console.log("EVENTO ESCOLHIDO: "+evento);
		
		dataLayer.push({'event':'escolha_categoria', 'pagina':pagina ,'categoria_nome':evento});
		
		$("#homeContato_categoria").val(evento);
		
		scrollToAnchor('#contato');	
	});


	
	
	// OVERLAY DOS VIDEOS
	var hasVideo = 1;
	
	$(".overlay-video").click(function(){
    	$(".overlay-video").fadeOut();
    	$('.youtube_video').each(function(){
 			this.contentWindow.postMessage('{"event":"command","func":"' + 'stopVideo' + '","args":""}', '*')
		});
	});
	
	$(document).ready(function(){
		$(document).bind('keydown', function(e) { 
			if (e.which == 27) {
				$(".overlay-video").fadeOut();
				$('.youtube_video').each(function(){
					this.contentWindow.postMessage('{"event":"command","func":"' + 'stopVideo' + '","args":""}', '*')
				});
			}
		}); 
	});
	
	

	// OVERLAY DO MAPA
	$(".mapa_btn").click(function(){
    	$(".overlay-full").fadeIn();
	});
	
	$(".overlay-full").click(function(){
    	$(".overlay-full").fadeOut();
	});
	
	$(document).ready(function(){
		$(document).bind('keydown', function(e) { 
			if (e.which == 27) {
				$(".overlay-full").fadeOut();
			}
		}); 
	});
	
	// BOTAO PROXIIMA PAGINA DOS PRODUTOS
	$(".hvr-bounce-in").hover(function(){
		$(this).children(".ver_pagina-produto").css("display", "block");
		}, function(){
		$(this).children(".ver_pagina-produto").css("display", "none");
	});
	
	// MENU DROPDOWN
	$(window).resize(function(){
		if ($(window).width() > 1024)
		{
			$("#site_menu").css("display", "initial");
			$("#menu-aparece").css("display", "none");
			$("#menu-some").css("display", "none");
		}
		else
		{
			$("#site_menu").css("display", "none");
			$("#menu-aparece").css("display", "initial");
			$("#menu-some").css("display", "none");
		}
	});
	
	$("#site_menu").click(function(){
		if($(window).width() < 1024) {
			$("#site_menu").slideUp("slow");
			$("#menu-aparece").css("display", "initial");
			$("#menu-some").css("display", "none");
		}
	});
	
	$("#menu-aparece").click(function(){
		$("#site_menu").slideDown("slow")
		$("#menu-aparece").css("display", "none");
		$("#menu-some").css("display", "initial");
	});
	
	$("#menu-some").click(function(){
		$("#site_menu").slideUp("slow")
		$("#menu-aparece").css("display", "initial");
		$("#menu-some").css("display", "none");
	});


		
});//JQUERY()



