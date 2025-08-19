/*!
* Start Bootstrap - One Page Wonder v6.0.6 (https://startbootstrap.com/theme/one-page-wonder)
* Copyright 2013-2023 Start Bootstrap
* Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-one-page-wonder/blob/master/LICENSE)
*/
// Este arquivo agora contém a lógica de conversão de leads e interação com CTA

(function(){
	function $(selector){ return document.querySelector(selector); }
	function $all(selector){ return Array.prototype.slice.call(document.querySelectorAll(selector)); }

	// Pré-seleciona tipo de mídia quando o usuário clica em "Quero alugar!"
	$all('.js-open-lead').forEach(function(el){
		el.addEventListener('click', function(ev){
			var midia = el.getAttribute('data-midia');
			if(midia){
				var select = $('#midia');
				if(select){ select.value = midia; }
			}
		});
	});

	// Máscara simples para telefone (apenas formatação visual)
	var tel = $('#telefone');
	if(tel){
		tel.addEventListener('input', function(){
			var digits = tel.value.replace(/\D/g,'').slice(0,11);
			var out = digits;
			if(digits.length > 2){ out = '('+digits.slice(0,2)+') '+digits.slice(2); }
			if(digits.length > 7){ out = '('+digits.slice(0,2)+') '+digits.slice(2,7)+'-'+digits.slice(7); }
			tel.value = out;
		});
	}

	// Envio do lead -> abre WhatsApp com mensagem estruturada
	var form = $('#leadForm');
	if(form){
		form.addEventListener('submit', function(e){
			e.preventDefault();
			var midia = $('#midia') ? $('#midia').value : '';
			var nome = $('#nome') ? $('#nome').value.trim() : '';
			var telefone = $('#telefone') ? $('#telefone').value.trim() : '';
			var email = $('#email') ? $('#email').value.trim() : '';
			var local = $('#local') ? $('#local').value.trim() : '';

			// Evento de conversão (Google Ads / GA4)
			try{ if(typeof gtag === 'function'){ gtag('event','generate_lead',{ method:'form_whatsapp', midia: midia }); } }catch(err){}

			var linhas = [
				'Olá, sou '+(nome||'interessado(a)')+' e gostaria de um orçamento.',
				'Tipo de mídia: '+midia,
				local ? 'Região de interesse: '+local : null,
				telefone ? 'Meu WhatsApp: '+telefone : null,
				email ? 'Email: '+email : null
			].filter(Boolean);
			var msg = linhas.join('\n');

			// Gravação assíncrona do lead (não bloqueia)
			try{
				fetch('leads/save.php',{
					method:'POST',
					headers:{'Content-Type':'application/json'},
					body: JSON.stringify({ midia: midia, nome: nome, telefone: telefone, email: email, local: local, page: location.pathname+location.search, ts: new Date().toISOString() })
				}).catch(function(){});
			}catch(err){}

			var url = 'https://wa.me/5561998420444?text='+encodeURIComponent(msg);
			window.open(url,'_blank');
		});
	}
})();