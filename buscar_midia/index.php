<?php
	ini_set('display_errors',1);
	ini_set('display_startup_erros',1);
	error_reporting(E_ALL);
	require_once("../Classes/Ponto.php");
	require_once("../Classes/Geral.php");

    $ponto = new Ponto(); 
    $geral = new Geral(); 

    $retorno = $ponto->listarTodosPonto(2); 
    $optionsBairro = $ponto->listarOptionsBairro(null);

    if(isset($_REQUEST['id_tipo'])){
        $optionsTipo = $geral->listarOptionsTipo($_REQUEST['id_tipo']); 
    }else{
        $optionsTipo = $geral->listarOptionsTipo(null); 
    }

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Universo Mídia e Painéis</title>
        <link rel="icon" type="image/x-icon" href="../assets/img/logo-removebg.png" />
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Catamaran:100,200,300,400,500,600,700,800,900" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Lato:100,100i,300,300i,400,400i,700,700i,900,900i" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="../css/styles.css" rel="stylesheet" />
        <style>
            .w-lg-225px{
                width: 225px !important;
            }
            #map {
                width: 100%;
                height: 500px;
            }
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
            <h1 class="text-center my-5" style="font-weight:200;">Buscar Mídias</h1>
            <div class="row mb-5">
                <div class="col-lg-6 col-dm-8 col-sm-12">
                    <div class="row">
                        <div class="col-4">
                            <label for="">Pesquisar</label>
                            <input type="text" class="form-control" id="filtro">
                        </div>
                        <div class="col-4">
                            <label for="">Localização</label>
                            <select name="id_localizacao_filtro" id="id_localizacao_filtro" class="form-control">
                                <option value="todos">Todos</option>
                                <?php
                                    echo $optionsBairro; 
                                ?>
                            </select>
                        </div>
                        <div class="col-4">
                            <label for="">Tipo</label>
                            <select name="id_tipo_filtro" id="id_tipo_filtro" class="form-control">
                                <option value="todos">Todos</option>
                                <?php
                                    echo $optionsTipo; 
                                ?>
                            </select>
                        </div>
                    </div>
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
                            <div class="d-flex flex-stack gap-5">
                                <div class="d-flex flex-stack mb-1">
                                    <i class="ki-outline ki-magnifier text-gray-500 fs-3"></i>
                                    <a href="#" name="titulo" id-ponto='<?php echo $retorno[$i]['id_ponto']; ?>' class="text-primary text-hover-primary-active fw-semibold">
                                        <?php echo $retorno[$i]['ds_localidade']; ?>											
                                    </a>
                                </div>
                                
                                <i class="ki-outline ki-heart text-danger fs-3 me-2"></i>									
                            </div>
                            
                            <div class="text-gray-500 mb-3">
                                <?php echo $retorno[$i]['ds_descricao']; ?>							
                            </div>

                            <div class="d-flex gap-4 mb-3 fw-semibold">
                                <div class='my-8 mx-15 text-left'>
                                    <div class='d-flex ml-n8 my-1'>
                                        <div style='width:30px;text-align:center;'>
                                            <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' fill='currentColor' class='bi bi-display' viewBox='0 0 16 16'>
                                                <path d='M0 4s0-2 2-2h12s2 0 2 2v6s0 2-2 2h-4c0 .667.083 1.167.25 1.5H11a.5.5 0 0 1 0 1H5a.5.5 0 0 1 0-1h.75c.167-.333.25-.833.25-1.5H2s-2 0-2-2V4zm1.398-.855a.758.758 0 0 0-.254.302A1.46 1.46 0 0 0 1 4.01V10c0 .325.078.502.145.602.07.105.17.188.302.254a1.464 1.464 0 0 0 .538.143L2.01 11H14c.325 0 .502-.078.602-.145a.758.758 0 0 0 .254-.302 1.464 1.464 0 0 0 .143-.538L15 9.99V4c0-.325-.078-.502-.145-.602a.757.757 0 0 0-.302-.254A1.46 1.46 0 0 0 13.99 3H2c-.325 0-.502.078-.602.145z'/>
                                            </svg>
                                        </div>
                                        <span class='ml-2 text-dark font-weight-bold mt-1' style='align-items:center;display:flex;'><?php echo $retorno[$i]['ds_tipo']; ?></span><br>
                                    </div>
                                    <div class='d-flex ml-n8 my-1'>
                                        <div style='width:30px;text-align:center;'>
                                            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-arrows-move' viewBox='0 0 16 16'>
                                                <path fill-rule='evenodd' d='M7.646.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 1.707V5.5a.5.5 0 0 1-1 0V1.707L6.354 2.854a.5.5 0 1 1-.708-.708l2-2zM8 10a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 14.293V10.5A.5.5 0 0 1 8 10zM.146 8.354a.5.5 0 0 1 0-.708l2-2a.5.5 0 1 1 .708.708L1.707 7.5H5.5a.5.5 0 0 1 0 1H1.707l1.147 1.146a.5.5 0 0 1-.708.708l-2-2zM10 8a.5.5 0 0 1 .5-.5h3.793l-1.147-1.146a.5.5 0 0 1 .708-.708l2 2a.5.5 0 0 1 0 .708l-2 2a.5.5 0 0 1-.708-.708L14.293 8.5H10.5A.5.5 0 0 1 10 8z'/>
                                            </svg>
                                        </div>	
                                        <span class='ml-2 text-dark font-weight-bold' style='align-items:center;display:flex;'>Sentido <?php echo $retorno[$i]['ds_sentido']; ?></span><br>
                                    </div>
                                    <div class='d-flex ml-n8 my-1'>
                                        <div style='width:30px;text-align:center;'>
                                            <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' fill='currentColor' class='bi bi-aspect-ratio' viewBox='0 0 16 16'>
                                                <path d='M0 3.5A1.5 1.5 0 0 1 1.5 2h13A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 12.5v-9zM1.5 3a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-13z'/>
                                                <path d='M2 4.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1H3v2.5a.5.5 0 0 1-1 0v-3zm12 7a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1 0-1H13V8.5a.5.5 0 0 1 1 0v3z'/>
                                            </svg>
                                        </div>
                                        <span class='ml-2 font-weight-bolder text-dark' style='align-items:center;display:flex;'><?php echo $retorno[$i]['ds_tamanho']; ?></span>
                                    </div>
                                    <div class='separator separator-solid my-4'></div>
                                </div> 
                            </div>
                            <div class="d-flex">
                                <a target="_blank" href='https://wa.me/5561984487408?text=Olá,%20gostaria%20de%20mais%20informações%20sobre%20essa%20mídia%0A%0A<?php echo $retorno[$i]['ds_localidade']; ?>(<?php echo $retorno[$i]['ds_descricao']; ?>)' class="btn btn-success m-3 text-start">
                                    Saiba Mais!
                                    <svg class="ml-3" width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff" transform="rotate(0)"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.624"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M3.50002 12C3.50002 7.30558 7.3056 3.5 12 3.5C16.6944 3.5 20.5 7.30558 20.5 12C20.5 16.6944 16.6944 20.5 12 20.5C10.3278 20.5 8.77127 20.0182 7.45798 19.1861C7.21357 19.0313 6.91408 18.9899 6.63684 19.0726L3.75769 19.9319L4.84173 17.3953C4.96986 17.0955 4.94379 16.7521 4.77187 16.4751C3.9657 15.176 3.50002 13.6439 3.50002 12ZM12 1.5C6.20103 1.5 1.50002 6.20101 1.50002 12C1.50002 13.8381 1.97316 15.5683 2.80465 17.0727L1.08047 21.107C0.928048 21.4637 0.99561 21.8763 1.25382 22.1657C1.51203 22.4552 1.91432 22.5692 2.28599 22.4582L6.78541 21.1155C8.32245 21.9965 10.1037 22.5 12 22.5C17.799 22.5 22.5 17.799 22.5 12C22.5 6.20101 17.799 1.5 12 1.5ZM14.2925 14.1824L12.9783 15.1081C12.3628 14.7575 11.6823 14.2681 10.9997 13.5855C10.2901 12.8759 9.76402 12.1433 9.37612 11.4713L10.2113 10.7624C10.5697 10.4582 10.6678 9.94533 10.447 9.53028L9.38284 7.53028C9.23954 7.26097 8.98116 7.0718 8.68115 7.01654C8.38113 6.96129 8.07231 7.046 7.84247 7.24659L7.52696 7.52195C6.76823 8.18414 6.3195 9.2723 6.69141 10.3741C7.07698 11.5163 7.89983 13.314 9.58552 14.9997C11.3991 16.8133 13.2413 17.5275 14.3186 17.8049C15.1866 18.0283 16.008 17.7288 16.5868 17.2572L17.1783 16.7752C17.4313 16.5691 17.5678 16.2524 17.544 15.9269C17.5201 15.6014 17.3389 15.308 17.0585 15.1409L15.3802 14.1409C15.0412 13.939 14.6152 13.9552 14.2925 14.1824Z" fill="#ffffff"></path> </g></svg>
                                </a>
                                <!-- <button class="btn btn-primary btn-sm m-3 text-start">
                                    Adicionar ao carrinho 
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-cart2" viewBox="0 0 16 16">
                                        <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5M3.14 5l1.25 5h8.22l1.25-5zM5 13a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0m9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0"/>
                                    </svg>
                                </button> -->
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
                    <p class="text-primary" style="font-size:18px;">Clique no ponto para pesquisa-lo</p>
                </div>
            </div>
        </div>
        <footer class="py-5 bg-black" id="contato">
            <div class="container text-center">
                <h2 style="color: white;">CONTATO</h2>
                <div style="color: white;">
                    <p>Número <br>+55 61 998420444<br>+55 61 984487408</p>
                    <p>Email <br>contato.universomidia@gmail.com</p>
                </div>
                <a class="btn btn-dark btn-social mx-2" target="_blank" href="https://www.instagram.com/universo.midia/" aria-label="Instagram"><svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M12 18C15.3137 18 18 15.3137 18 12C18 8.68629 15.3137 6 12 6C8.68629 6 6 8.68629 6 12C6 15.3137 8.68629 18 12 18ZM12 16C14.2091 16 16 14.2091 16 12C16 9.79086 14.2091 8 12 8C9.79086 8 8 9.79086 8 12C8 14.2091 9.79086 16 12 16Z" fill="#ffffff"></path> <path d="M18 5C17.4477 5 17 5.44772 17 6C17 6.55228 17.4477 7 18 7C18.5523 7 19 6.55228 19 6C19 5.44772 18.5523 5 18 5Z" fill="#ffffff"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M1.65396 4.27606C1 5.55953 1 7.23969 1 10.6V13.4C1 16.7603 1 18.4405 1.65396 19.7239C2.2292 20.8529 3.14708 21.7708 4.27606 22.346C5.55953 23 7.23969 23 10.6 23H13.4C16.7603 23 18.4405 23 19.7239 22.346C20.8529 21.7708 21.7708 20.8529 22.346 19.7239C23 18.4405 23 16.7603 23 13.4V10.6C23 7.23969 23 5.55953 22.346 4.27606C21.7708 3.14708 20.8529 2.2292 19.7239 1.65396C18.4405 1 16.7603 1 13.4 1H10.6C7.23969 1 5.55953 1 4.27606 1.65396C3.14708 2.2292 2.2292 3.14708 1.65396 4.27606ZM13.4 3H10.6C8.88684 3 7.72225 3.00156 6.82208 3.0751C5.94524 3.14674 5.49684 3.27659 5.18404 3.43597C4.43139 3.81947 3.81947 4.43139 3.43597 5.18404C3.27659 5.49684 3.14674 5.94524 3.0751 6.82208C3.00156 7.72225 3 8.88684 3 10.6V13.4C3 15.1132 3.00156 16.2777 3.0751 17.1779C3.14674 18.0548 3.27659 18.5032 3.43597 18.816C3.81947 19.5686 4.43139 20.1805 5.18404 20.564C5.49684 20.7234 5.94524 20.8533 6.82208 20.9249C7.72225 20.9984 8.88684 21 10.6 21H13.4C15.1132 21 16.2777 20.9984 17.1779 20.9249C18.0548 20.8533 18.5032 20.7234 18.816 20.564C19.5686 20.1805 20.1805 19.5686 20.564 18.816C20.7234 18.5032 20.8533 18.0548 20.9249 17.1779C20.9984 16.2777 21 15.1132 21 13.4V10.6C21 8.88684 20.9984 7.72225 20.9249 6.82208C20.8533 5.94524 20.7234 5.49684 20.564 5.18404C20.1805 4.43139 19.5686 3.81947 18.816 3.43597C18.5032 3.27659 18.0548 3.14674 17.1779 3.0751C16.2777 3.00156 15.1132 3 13.4 3Z" fill="#ffffff"></path> </g></svg></a>
                <a class="btn btn-dark btn-social mx-2" target="_blank" href="https://wa.me/5561984487408?text=Olá,%20gostaria%20de%20mais%20informações%20sobre%20seus%20serviços!" aria-label="Whatsapp"><svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff" transform="rotate(0)"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.624"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M3.50002 12C3.50002 7.30558 7.3056 3.5 12 3.5C16.6944 3.5 20.5 7.30558 20.5 12C20.5 16.6944 16.6944 20.5 12 20.5C10.3278 20.5 8.77127 20.0182 7.45798 19.1861C7.21357 19.0313 6.91408 18.9899 6.63684 19.0726L3.75769 19.9319L4.84173 17.3953C4.96986 17.0955 4.94379 16.7521 4.77187 16.4751C3.9657 15.176 3.50002 13.6439 3.50002 12ZM12 1.5C6.20103 1.5 1.50002 6.20101 1.50002 12C1.50002 13.8381 1.97316 15.5683 2.80465 17.0727L1.08047 21.107C0.928048 21.4637 0.99561 21.8763 1.25382 22.1657C1.51203 22.4552 1.91432 22.5692 2.28599 22.4582L6.78541 21.1155C8.32245 21.9965 10.1037 22.5 12 22.5C17.799 22.5 22.5 17.799 22.5 12C22.5 6.20101 17.799 1.5 12 1.5ZM14.2925 14.1824L12.9783 15.1081C12.3628 14.7575 11.6823 14.2681 10.9997 13.5855C10.2901 12.8759 9.76402 12.1433 9.37612 11.4713L10.2113 10.7624C10.5697 10.4582 10.6678 9.94533 10.447 9.53028L9.38284 7.53028C9.23954 7.26097 8.98116 7.0718 8.68115 7.01654C8.38113 6.96129 8.07231 7.046 7.84247 7.24659L7.52696 7.52195C6.76823 8.18414 6.3195 9.2723 6.69141 10.3741C7.07698 11.5163 7.89983 13.314 9.58552 14.9997C11.3991 16.8133 13.2413 17.5275 14.3186 17.8049C15.1866 18.0283 16.008 17.7288 16.5868 17.2572L17.1783 16.7752C17.4313 16.5691 17.5678 16.2524 17.544 15.9269C17.5201 15.6014 17.3389 15.308 17.0585 15.1409L15.3802 14.1409C15.0412 13.939 14.6152 13.9552 14.2925 14.1824Z" fill="#ffffff"></path> </g></svg></a>
            </div>
            <div class="container px-5 mt-5"><p class="m-0 text-center text-white small">Copyright &copy; Universo Mídia 2024</p></div>
        </footer>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
        <script src="../js/scripts.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB0sGOoifQgDLzR_xYQbaGiiqXRHaJN2tM"></script>
        <script src="../assets/plugins/custom/gmaps/gmaps.js"></script>
        <script>
			// The following example creates complex markers to indicate beaches near
            // Sydney, NSW, Australia. Note that the anchor is set to (0,32) to correspond
            // to the base of the flagpole.
            $(document).ready(function() { 
                var latitude = -15.8248734;
                var longitude = -48.0607963;
                var zoom = 11;
                demo3(latitude, longitude, zoom);

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
                });
                $('#id_tipo_filtro').change(function () {
                    var tipo = $(this).val();
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

                    markers.forEach(function(m) {
                        if ((m.bairro == localidade || localidade == 'todos') && (m.tipo == tipo || tipo == 'todos')) {
                            m.setVisible(true); // Esconde o marcador
                        }else{
                            m.setVisible(false);
                        }
                    });
                });

                $('#id_localizacao_filtro').change(function () {
                    var localidade = $(this).val();
                    var tipo = $("#id_tipo_filtro").val();

                    if(localidade == 'todos'){
                        $('#lista .coluna-card').each(function () {
                            if($(this).find('input[name="tipo"]').val() == tipo || tipo == 'todos'){
                                $(this).closest('.coluna-card').show();
                            }
                        });
                    }else{
                        $('#lista .coluna-card').each(function () {
                            if($(this).find('input[name="nome"]').val() == localidade && ($(this).find('input[name="tipo"]').val() == tipo || tipo == 'todos')) {
                                $(this).closest('.coluna-card').show();
                                return true;
                            } else {
                                $(this).closest('.coluna-card').hide(); 
                            }
                        });
                    }

                    markers.forEach(function(m) {
                        if ((m.bairro == localidade || localidade == 'todos') && (m.tipo == tipo || tipo == 'todos')) {
                            m.setVisible(true); // Esconde o marcador
                        }else{
                            m.setVisible(false);
                        }
                    });
                });

                $("#mostrar_todos").on("click", function(){
                    markers.forEach(function(m) {
                        m.setVisible(true);
                    });
                    $('.coluna-card').show();
                    $('#div_mostrar_todos').removeClass('d-flex');
                    $('#div_mostrar_todos').addClass('d-none');
                })

                $("[name='titulo']").on("click", function(){
                    id_ponto = $(this).attr('id-ponto');
                    markers.forEach(function(m) {
                        if (m.id !== parseInt(id_ponto, 10)) {
                            m.setVisible(false); // Esconde o marcador
                        }
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
                })
                
            });
            var demo3 = function(latitude, longitude, zoom = 14) {
                var map = new GMaps({
                    div: '#map',
                    lat: latitude,
                    lng: longitude,
                    zoom: zoom,
                    mapTypeControl: false,
                    streetViewControl: false,
                    styles: [
                            {
                                featureType: 'poi',
                                elementType: 'labels',
                                stylers: [{ visibility: 'off' }]
                            },
                            {
                                featureType: 'road',
                                elementType: 'labels',
                                stylers: [{ visibility: 'off' }]
                            },
                            {
                                featureType: 'transit',
                                elementType: 'labels',
                                stylers: [{ visibility: 'off' }]
                            },
                            {
                                featureType: 'water',
                                elementType: 'labels',
                                stylers: [{ visibility: 'off' }]
                            }
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
                                if (m.id !== id_ponto) {
                                    m.setVisible(false); // Esconde o marcador
                                }
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
                            markers.forEach(function(m) {
                                m.setVisible(true);
                            });
                            $('.coluna-card').show();
                            $('#div_mostrar_todos').removeClass('d-flex');
                            $('#div_mostrar_todos').addClass('d-none');
                        });
                        bounds.extend({lat: <?php echo $latitudeElongitude[0]; ?>, lng: <?php echo $latitudeElongitude[1]; ?>});
                    <?php endif; ?>
                <?php } ?>
                map.fitBounds(bounds);
            } 

        </script>
    </body>
</html>
