<?php
	ini_set('display_errors',1);
	ini_set('display_startup_erros',1);
	error_reporting(E_ALL);
	require_once("../Classes/Ponto.php");
	require_once("../Classes/Geral.php");

    $ponto = new Ponto(); 
    $geral = new Geral(); 

    $retorno = $ponto->listarTodosPonto(2); 
    $optionsBairro = isset($_REQUEST['bairro']) ? $ponto->listarOptionsBairro($_REQUEST['bairro']) : $ponto->listarOptionsBairro(null);

    // Opções únicas para filtros adicionais
    $sentidos = array();
    $tamanhos = array();
    for ($i=0; $i < count($retorno); $i++) {
        if(isset($retorno[$i]['ds_sentido']) && $retorno[$i]['ds_sentido'] !== '' && !in_array($retorno[$i]['ds_sentido'], $sentidos)){
            $sentidos[] = $retorno[$i]['ds_sentido'];
        }
        if(isset($retorno[$i]['ds_tamanho']) && $retorno[$i]['ds_tamanho'] !== '' && !in_array($retorno[$i]['ds_tamanho'], $tamanhos)){
            $tamanhos[] = $retorno[$i]['ds_tamanho'];
        }
    }
    sort($sentidos);
    sort($tamanhos);

    if(isset($_REQUEST['id_tipo'])){
        $optionsTipo = $geral->listarOptionsTipo($_REQUEST['id_tipo']); 
    }else{
        $optionsTipo = $geral->listarOptionsTipo(null); 
    }

?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<meta name="description" content="Veja todos os pontos de Outdoor, Front-light, Empena e LED disponíveis em Brasília. Filtre por bairro e formato e chame no WhatsApp." />
		<meta name="author" content="" />
		<title>Buscar Mídias | Universo Mídia</title>
		<link rel="icon" type="image/x-icon" href="../assets/img/logo-removebg.png" />
		<!-- Font Awesome icons (free version)-->
		<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
		<!-- Google fonts-->
		<link href="https://fonts.googleapis.com/css?family=Catamaran:100,200,300,400,500,600,700,800,900" rel="stylesheet" />
		<link href="https://fonts.googleapis.com/css?family=Lato:100,100i,300,300i,400,400i,700,700i,900,900i" rel="stylesheet" />
		<!-- Core theme CSS (includes Bootstrap)-->
		<link href="../css/styles.css" rel="stylesheet" />
		<style>
			.w-lg-225px{ width: 225px !important; }
			#map{ width:100%; height:500px; }
			.badge-soft{ background: #f1f3f5; color:#111; border-radius: 40px; padding:.25rem .6rem; font-weight:600; }
		</style>
	</head>
	<body id="page-top">
		<!-- Navigation-->
		<nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
			<div class="container px-5">
				<a class="navbar-brand" href="../">
					<img src="../assets/img/logo-removebg.png" alt="" style="width: 50px;">
					Universo Mídia</a>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
				<div class="collapse navbar-collapse" id="navbarResponsive">
					<ul class="navbar-nav ms-auto">
						<li class="nav-item"><a class="nav-link" href="#">Buscar mídias</a></li>
						<li class="nav-item"><a class="nav-link" href="../#sobre_nos">Sobre nós</a></li>
						<li class="nav-item"><a class="nav-link" href="../#parceiros">Parceiros</a></li>
						<li class="nav-item"><a class="nav-link" href="../#contato">Contato</a></li>
					</ul>
				</div>
			</div>
		</nav>
		<div class="m-5" style="margin-top:140px !important;">
			<h1 class="text-center my-2" style="font-weight:200;">Buscar Mídias</h1>
			<p class="text-center text-muted mb-4">Filtre por localização e formato. Clique no card ou no mapa para falar no WhatsApp.</p>
							<div class="row mb-3 align-items-end">
					<div class="col-lg-9 col-sm-12">
						<div class="row g-3">
							<div class="col-12 col-md-3">
								<label for="filtro">Pesquisar</label>
								<input type="text" class="form-control" id="filtro" placeholder="Ex.: Taguatinga, Eixo..."></div>
							<div class="col-6 col-md-3">
								<label for="id_localizacao_filtro">Localização</label>
								<select name="id_localizacao_filtro" id="id_localizacao_filtro" class="form-control">
									<option value="todos">Todos</option>
									<?php echo $optionsBairro; ?>
								</select>
							</div>
							<div class="col-6 col-md-2">
								<label for="id_tipo_filtro">Tipo</label>
								<select name="id_tipo_filtro" id="id_tipo_filtro" class="form-control">
									<option value="todos">Todos</option>
									<?php echo $optionsTipo; ?>
								</select>
							</div>
							<div class="col-6 col-md-2">
								<label for="sentido_filtro">Sentido</label>
								<select name="sentido_filtro" id="sentido_filtro" class="form-control">
									<option value="todos">Todos</option>
									<?php foreach($sentidos as $s){ echo '<option value="'.htmlspecialchars($s).'">'.htmlspecialchars($s).'</option>'; } ?>
								</select>
							</div>
							<div class="col-6 col-md-2">
								<label for="tamanho_filtro">Tamanho</label>
								<select name="tamanho_filtro" id="tamanho_filtro" class="form-control">
									<option value="todos">Todos</option>
									<?php foreach($tamanhos as $t){ echo '<option value="'.htmlspecialchars($t).'">'.htmlspecialchars($t).'</option>'; } ?>
								</select>
							</div>
						</div>
					</div>
									<div class="col-lg-3 col-sm-12 mt-3 mt-lg-0 d-flex justify-content-lg-end gap-2">
						<div class="text-muted align-self-center">Resultados: <span id="count">0</span></div>
						<button class="btn btn-outline-secondary" id="limpar">Limpar filtros</button>
						<a class="btn btn-primary" id="exportar" download="midias.csv">Exportar CSV</a>
					</div>
			</div>
			<div class="row">
				<div class="col-md-6 col-sm-12 mb-5" style="overflow-x:hidden;overflow-y:scroll; height: 600px;" id="lista">
					<?php for ($i=0; $i < count($retorno); $i++) :?>
					<div class="card card-hover-border p-3 flex-lg-row align-items-lg-center gap-5 mb-3 coluna-card">
						<input type='hidden' name='nome' value='<?php echo $retorno[$i]['ds_localidade'];?>'>
						<input type='hidden' name='tipo' value='<?php echo $retorno[$i]['id_tipo'];?>'>
						<input type='hidden' name='id_ponto' value='<?php echo $retorno[$i]['id_ponto'];?>'>
						<img src="https://painelpro.pro/<?php echo $retorno[$i]["ds_foto"]; ?>" class="w-lg-225px rounded w-100">

						<div class="d-flex flex-column flex-grow-1">
							<div class="d-flex flex-stack gap-3">
								<div class="d-flex flex-column mb-1">
									<a href="#" name="titulo" id-ponto='<?php echo $retorno[$i]['id_ponto']; ?>' class="text-primary text-hover-primary-active fw-semibold" style="text-decoration:none;">
										<?php echo $retorno[$i]['ds_localidade']; ?>										
									</a>
									<div class="mt-1">
										<span class="badge-soft me-2"><?php echo $retorno[$i]['ds_tipo']; ?></span>
										<span class="badge-soft"><?php echo $retorno[$i]['ds_tamanho']; ?></span>
									</div>
								</div>
								<i class="ki-outline ki-heart text-danger fs-3 me-2"></i>													
							</div>
							<div class="text-gray-500 mb-2"><?php echo $retorno[$i]['ds_descricao']; ?></div>
							<div class="small text-muted mb-2">Sentido: <span class="sentido"><?php echo $retorno[$i]['ds_sentido']; ?></span> · Tamanho: <span class="tamanho"><?php echo $retorno[$i]['ds_tamanho']; ?></span></div>
							<div class="d-flex">
								<a target="_blank" href='https://wa.me/5561984487408?text=<?php echo urlencode("Olá, vi a mídia ".$retorno[$i]['ds_localidade']." (".$retorno[$i]['ds_tipo'].") e quero mais detalhes."); ?>' class="btn btn-success m-3 text-start">
									Falar no WhatsApp
									<svg class="ml-3" width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff" transform="rotate(0)"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.624"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M3.50002 12C3.50002 7.30558 7.3056 3.5 12 3.5C16.6944 3.5 20.5 7.30558 20.5 12C20.5 16.6944 16.6944 20.5 12 20.5C10.3278 20.5 8.77127 20.0182 7.45798 19.1861C7.21357 19.0313 6.91408 18.9899 6.63684 19.0726L3.75769 19.9319L4.84173 17.3953C4.96986 17.0955 4.94379 16.7521 4.77187 16.4751C3.9657 15.176 3.50002 13.6439 3.50002 12ZM12 1.5C6.20103 1.5 1.50002 6.20101 1.50002 12C1.50002 13.8381 1.97316 15.5683 2.80465 17.0727L1.08047 21.107C0.928048 21.4637 0.99561 21.8763 1.25382 22.1657C1.51203 22.4552 1.91432 22.5692 2.28599 22.4582L6.78541 21.1155C8.32245 21.9965 10.1037 22.5 12 22.5C17.799 22.5 22.5 17.799 22.5 12C22.5 6.20101 17.799 1.5 12 1.5ZM14.2925 14.1824L12.9783 15.1081C12.3628 14.7575 11.6823 14.2681 10.9997 13.5855C10.2901 12.8759 9.76402 12.1433 9.37612 11.4713L10.2113 10.7624C10.5697 10.4582 10.6678 9.94533 10.447 9.53028L9.38284 7.53028C9.23954 7.26097 8.98116 7.0718 8.68115 7.01654C8.38113 6.96129 8.07231 7.046 7.84247 7.24659L7.52696 7.52195C6.76823 8.18414 6.3195 9.2723 6.69141 10.3741C7.07698 11.5163 7.89983 13.314 9.58552 14.9997C11.3991 16.8133 13.2413 17.5275 14.3186 17.8049C15.1866 18.0283 16.008 17.7288 16.5868 17.2572L17.1783 16.7752C17.4313 16.5691 17.5678 16.2524 17.544 15.9269C17.5201 15.6014 17.3389 15.308 17.0585 15.1409L15.3802 14.1409C15.0412 13.939 14.6152 13.9552 14.2925 14.1824Z" fill="#ffffff"></path> </g></svg>
								</a>
							</div>
						</div>
					</div>
					<?php endfor; ?>
					<div class="w-100 justify-content-center m-3 d-none" id="div_mostrar_todos">
						<button class="btn btn-primary" id="mostrar_todos">Mostrar todos</button>
					</div>
				</div>
				<div class="col-md-6 col-sm-12">
					<div id="map" height="350px" class="w-100 rounded"></div>
					<p class="text-primary" style="font-size:18px;" id="texto-mapa">Clique no ponto para pesquisa-lo</p>
					<small class="text-muted">Dica: você pode compartilhar um filtro com seu cliente usando a URL, por exemplo `buscar_midia?bairro=Taguatinga`.</small>
				</div>
			</div>
		</div>
		<footer class="pt-5 pb-2 bg-black container" id="contato" style="max-width: 100%;position: relative;">
			<div class="row">
				<div class="col-lg-6 col-sm-12 px-5 text-center mb-sm-5 mb-lg-1">
					<img style="text-align: center;width: 150px;" src="../assets/img/logo-removebg.png" alt="">
					<p style="color: white;font-size: 12px;">QUADRA QNM 26 CONJUNTO G LOTES 17, 19 E 21 S/N</p>
					<p style="color: white;font-size: 12px;">Ceilandia Norte</p>
				</div>
				<div class="col-lg-6 col-sm-12 container">
					<h2 style="color: white;text-align: center;">CONTATO</h2>
					<div class="row">
						<div class="col-md-6 text-center">
							<div style="color: white;">
								<p>Número <br>+55 61 998420444<br>+55 61 984487408<br>+55 61 998050444</p>
							</div>
						</div>
						<div class="col-md-6 text-center">
							<div style="color: white;">
								<p>Email <br>contato.universomidia@gmail.com<br>rodrigouniversomidia@gmail.com</p>
							</div>
						</div>
					</div>
					<div class="container text-center">
						<a class="btn btn-dark btn-social mx-2" target="_blank" href="https://www.instagram.com/universo.midia/" aria-label="Instagram"><svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M12 18C15.3137 18 18 15.3137 18 12C18 8.68629 15.3137 6 12 6C8.68629 6 6 8.68629 6 12C6 15.3137 8.68629 18 12 18ZM12 16C14.2091 16 16 14.2091 16 12C16 9.79086 14.2091 8 12 8C9.79086 8 8 9.79086 8 12C8 14.2091 9.79086 16 12 16Z" fill="#ffffff"></path> <path d="M18 5C17.4477 5 17 5.44772 17 6C17 6.55228 17.4477 7 18 7C18.5523 7 19 6.55228 19 6C19 5.44772 18.5523 5 18 5Z" fill="#ffffff"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M1.65396 4.27606C1 5.55953 1 7.23969 1 10.6V13.4C1 16.7603 1 18.4405 1.65396 19.7239C2.2292 20.8529 3.14708 21.7708 4.27606 22.346C5.55953 23 7.23969 23 10.6 23H13.4C16.7603 23 18.4405 23 19.7239 22.346C20.8529 21.7708 21.7708 20.8529 22.346 19.7239C23 18.4405 23 16.7603 23 13.4V10.6C23 7.23969 23 5.55953 22.346 4.27606C21.7708 3.14708 20.8529 2.2292 19.7239 1.65396C18.4405 1 16.7603 1 13.4 1H10.6C7.23969 1 5.55953 1 4.27606 1.65396C3.14708 2.2292 2.2292 3.14708 1.65396 4.27606ZM13.4 3H10.6C8.88684 3 7.72225 3.00156 6.82208 3.0751C5.94524 3.14674 5.49684 3.27659 5.18404 3.43597C4.43139 3.81947 3.81947 4.43139 3.43597 5.18404C3.27659 5.49684 3.14674 5.94524 3.0751 6.82208C3.00156 7.72225 3 8.88684 3 10.6V13.4C3 15.1132 3.00156 16.2777 3.0751 17.1779C3.14674 18.0548 3.27659 18.5032 3.43597 18.816C3.81947 19.5686 4.43139 20.1805 5.18404 20.564C5.49684 20.7234 5.94524 20.8533 6.82208 20.9249C7.72225 20.9984 8.88684 21 10.6 21H13.4C15.1132 21 16.2777 20.9984 17.1779 20.9249C18.0548 20.8533 18.5032 20.7234 18.816 20.564C19.5686 20.1805 20.1805 19.5686 20.564 18.816C20.7234 18.5032 20.8533 18.0548 20.9249 17.1779C20.9984 16.2777 21 15.1132 21 13.4V10.6C21 8.88684 20.9984 7.72225 20.9249 6.82208C20.8533 5.94524 20.7234 5.49684 20.564 5.18404C20.1805 4.43139 19.5686 3.81947 18.816 3.43597C18.5032 3.27659 18.0548 3.14674 17.1779 3.0751C16.2777 3.00156 15.1132 3 13.4 3Z" fill="#ffffff"></path> </g></svg></a>
						<a class="btn btn-dark btn-social mx-2" target="_blank" href="https://wa.me/5561984487408?text=Olá,%20gostaria%20de%20mais%20informações%20sobre%20seus%20serviços!" aria-label="Whatsapp"><svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff" transform="rotate(0)"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.624"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M3.50002 12C3.50002 7.30558 7.3056 3.5 12 3.5C16.6944 3.5 20.5 7.30558 20.5 12C20.5 16.6944 16.6944 20.5 12 20.5C10.3278 20.5 8.77127 20.0182 7.45798 19.1861C7.21357 19.0313 6.91408 18.9899 6.63684 19.0726L3.75769 19.9319L4.84173 17.3953C4.96986 17.0955 4.94379 16.7521 4.77187 16.4751C3.9657 15.176 3.50002 13.6439 3.50002 12ZM12 1.5C6.20103 1.5 1.50002 6.20101 1.50002 12C1.50002 13.8381 1.97316 15.5683 2.80465 17.0727L1.08047 21.107C0.928048 21.4637 0.99561 21.8763 1.25382 22.1657C1.51203 22.4552 1.91432 22.5692 2.28599 22.4582L6.78541 21.1155C8.32245 21.9965 10.1037 22.5 12 22.5C17.799 22.5 22.5 17.799 22.5 12C22.5 6.20101 17.799 1.5 12 1.5ZM14.2925 14.1824L12.9783 15.1081C12.3628 14.7575 11.6823 14.2681 10.9997 13.5855C10.2901 12.8759 9.76402 12.1433 9.37612 11.4713L10.2113 10.7624C10.5697 10.4582 10.6678 9.94533 10.447 9.53028L9.38284 7.53028C9.23954 7.26097 8.98116 7.0718 8.68115 7.01654C8.38113 6.96129 8.07231 7.046 7.84247 7.24659L7.52696 7.52195C6.76823 8.18414 6.3195 9.2723 6.69141 10.3741C7.07698 11.5163 7.89983 13.314 9.58552 14.9997C11.3991 16.8133 13.2413 17.5275 14.3186 17.8049C15.1866 18.0283 16.008 17.7288 16.5868 17.2572L17.1783 16.7752C17.4313 16.5691 17.5678 16.2524 17.544 15.9269C17.5201 15.6014 17.3389 15.308 17.0585 15.1409L15.3802 14.1409C15.0412 13.939 14.6152 13.9552 14.2925 14.1824Z" fill="#ffffff"></path> </g></svg></a>
					</div>
				</div>
			</div>
			<div class="container text-center px-5 mt-5">
				<p class="m-0 text-center text-white small">Copyright &copy; Universo Mídia 2024</p>
			</div>
			<!-- <a href="#" style="position: absolute;bottom: 10px; right: 10px;">
				<img src="../assets/img/logo_jlt.png" width="65px" alt="">
			</a> -->
		</footer>
		<!-- Bootstrap core JS-->
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
		<!-- Core theme JS-->
		<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
		<script src="../js/scripts.js"></script>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB0sGOoifQgDLzR_xYQbaGiiqXRHaJN2tM"></script>
		<script src="../assets/plugins/custom/gmaps/gmaps.js"></script>
		<!-- Google tag (gtag.js) -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=AW-17409836521">
		</script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);} 
			gtag('js', new Date());
			gtag('config', 'AW-17409836521');
		</script>
		<script>
			$(document).ready(function() { 
				var latitude = -15.8248734;
				var longitude = -48.0607963;
				var zoom = 11;
				demo3(latitude, longitude, zoom);

				function atualizarContagem(){
					var qt = $('#lista .coluna-card:visible').length; $('#count').text(qt);
				}

				var tipo = $("#id_tipo_filtro").val();
				var localidade = $("#id_localizacao_filtro").val();
				if(tipo == 'todos'){
					$('#lista .coluna-card').each(function () {
						if($(this).find('input[name="nome"]').val() == localidade || localidade == 'todos'){
							$(this).closest('.coluna-card').show();
						}
					});
				}else{
					$('#lista .coluna-card').each(function () {
						if($(this).find('input[name="tipo"]').val() == tipo && ($(this).find('input[name="nome"]').val() == localidade || localidade == 'todos')) {
							$(this).closest('.coluna-card').show();
							return true;
						} else {
							$(this).closest('.coluna-card').hide(); 
						}
					});
				}
				atualizarContagem();

				markers.forEach(function(m) {
					if ((m.bairro == localidade || localidade == 'todos') && (m.tipo == tipo || tipo == 'todos')) {
						m.setVisible(true); // Esconde o marcador
					}else{
						m.setVisible(false);
					}
				});

				$('#filtro').keyup(function () {
					var texto = $(this).val().toLowerCase().split(' ');
					$('#lista .coluna-card').each(function () {
						for (let i = 0; i < texto.length; i++) {
							const element = texto[i];
							var linha = $(this).text().toLowerCase(); 
							if (linha.indexOf(element) === -1) {
								$(this).closest('.coluna-card').hide();
								return true;
							} else {
								$(this).closest('.coluna-card').show(); 
							}
						}
					});
					atualizarContagem();
				});
				function aplicarFiltros(){
					var localidade = $("#id_localizacao_filtro").val();
					var tipo = $("#id_tipo_filtro").val();
					var sentido = $("#sentido_filtro").val();
					var tamanho = $("#tamanho_filtro").val();

					$('#lista .coluna-card').each(function(){
						var ok = true;
						if(localidade !== 'todos' && $(this).find('input[name="nome"]').val() !== localidade){ ok = false; }
						if(tipo !== 'todos' && $(this).find('input[name="tipo"]').val() !== tipo){ ok = false; }
						if(sentido !== 'todos' && $(this).find('.sentido').text().trim() !== sentido){ ok = false; }
						if(tamanho !== 'todos' && $(this).find('.tamanho').text().trim() !== tamanho){ ok = false; }
						$(this).toggle(ok);
					});

					markers.forEach(function(m) {
						var mOk = true;
						if(localidade !== 'todos' && m.bairro !== localidade){ mOk = false; }
						if(tipo !== 'todos' && m.tipo !== tipo){ mOk = false; }
						m.setVisible(mOk);
					});
					atualizarContagem();
				}

				$('#id_tipo_filtro').change(aplicarFiltros);

								$('#id_localizacao_filtro').change(aplicarFiltros);
				$('#sentido_filtro').change(aplicarFiltros);
				$('#tamanho_filtro').change(aplicarFiltros);

				$("#mostrar_todos").on("click", function(){
					markers.forEach(function(m) { m.setVisible(true); });
					$('.coluna-card').show();
					$('#div_mostrar_todos').removeClass('d-flex').addClass('d-none');
					atualizarContagem();
				})

				// Export CSV do que está visível
				$('#exportar').on('click', function(e){
					e.preventDefault();
					var linhas = ['Localidade,Tipo,Sentido,Tamanho'];
					$('#lista .coluna-card:visible').each(function(){
						var loc = $(this).find('[name="titulo"]').text().trim();
						var tipo = $(this).find('.badge-soft').first().text().trim();
						var tam = $(this).find('.tamanho').text().trim();
						var sen = $(this).find('.sentido').text().trim();
						linhas.push([loc,tipo,sen,tam].map(function(x){return '"'+x.replace(/"/g,'""')+'"';}).join(','));
					});
					var blob = new Blob([linhas.join('\n')], {type: 'text/csv;charset=utf-8;'});
					var url = URL.createObjectURL(blob);
					$(this).attr('href', url);
				});

				$("[name='titulo']").on("click", function(){
					id_ponto = $(this).attr('id-ponto');
					markers.forEach(function(m) {
						if (m.id !== parseInt(id_ponto, 10)) { m.setVisible(false); }
					});
					$('#lista .coluna-card').each(function () {
						if($(this).find('input[name="id_ponto"]').val() == id_ponto) {
							$(this).closest('.coluna-card').show();
							return true;
						} else { $(this).closest('.coluna-card').hide(); }
					});
					$('#div_mostrar_todos').removeClass('d-none').addClass('d-flex');
					atualizarContagem();
				})

									$('#limpar').on('click', function(){
						$('#filtro').val('');
						$('#id_tipo_filtro').val('todos');
						$('#id_localizacao_filtro').val('todos');
						$('#sentido_filtro').val('todos');
						$('#tamanho_filtro').val('todos');
						aplicarFiltros();
						$('#div_mostrar_todos').removeClass('d-flex').addClass('d-none');
					});
			});
			var demo3 = function(latitude, longitude, zoom = 14) {
				var map = new GMaps({
					div: '#map',
					lat: latitude,
					lng: longitude,
					zoom: zoom,
					mapTypeControl: false,
					mapTypeId: 'hybrid',
					streetViewControl: false,
					styles: [
							{ featureType: 'poi', elementType: 'labels', stylers: [{ visibility: 'off' }] },
							{ featureType: 'road', elementType: 'labels', stylers: [{ visibility: 'off' }] },
							{ featureType: 'transit', elementType: 'labels', stylers: [{ visibility: 'off' }] },
							{ featureType: 'water', elementType: 'labels', stylers: [{ visibility: 'off' }] }
					]
				});
				var bounds = new google.maps.LatLngBounds();
				markers = [];
				<?php for ($i=0; $i < count($retorno); $i++){ ?>
					<?php                 
						$latitudeElongitude = explode("/", $retorno[$i]["nu_localidade"]);
						if(is_numeric($latitudeElongitude[0]) && is_numeric($latitudeElongitude[1]) && $latitudeElongitude[0] != "00.000000" && $latitudeElongitude[1] != "00.000000") :  
					?>
						var marker<?php echo $retorno[$i]['id_ponto']; ?> = map.addMarker({
							lat: <?php echo $latitudeElongitude[0]; ?>,
							lng: <?php echo $latitudeElongitude[1]; ?>,
							title: '<?php echo $retorno[$i]['ds_descricao']; ?>',
							id: <?php echo $retorno[$i]["id_ponto"]; ?>,
							bairro: '<?php echo $retorno[$i]["ds_localidade"]; ?>',
							tipo: '<?php echo $retorno[$i]["id_tipo"]; ?>'
						});

						markers.push(marker<?php echo $retorno[$i]['id_ponto']; ?>);

						google.maps.event.addListener(marker<?php echo $retorno[$i]['id_ponto']; ?>, 'click', function() {
							id_ponto = <?php echo $retorno[$i]['id_ponto']; ?>;
							markers.forEach(function(m) {
								if (m.id !== id_ponto) { m.setVisible(false); }
							});
							$('#lista .coluna-card').each(function () {
								if($(this).find('input[name="id_ponto"]').val() == id_ponto) {
									$(this).closest('.coluna-card').show();
									return true;
								} else {
									$(this).closest('.coluna-card').hide(); 
								}
							});
							$('#div_mostrar_todos').removeClass('d-none');
							$('#div_mostrar_todos').addClass('d-flex');
						});
						
						google.maps.event.addListener(map.map, 'click', function(event) {
							markers.forEach(function(m) { m.setVisible(true); });
							$('.coluna-card').show();
							$('#div_mostrar_todos').removeClass('d-flex').addClass('d-none');
						});
						bounds.extend({lat: <?php echo $latitudeElongitude[0]; ?>, lng: <?php echo $latitudeElongitude[1]; ?>});
					<?php endif; ?>
				<?php } ?>
				map.fitBounds(bounds);
			} 
		</script>
	</body>
</html>
