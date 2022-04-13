<?php
	require_once('../config.php');
	require_once('../course/lib.php');
	require_once('../metadata/metadata_page/aux_functions.php');

	require_once('../metadata/metadata_page/metadata_functions.php');//s_t_mod Hui

	require_once('../search_engine/php/mysql_conn.php');
    $PAGE->set_pagetype('site-index');
	$PAGE->set_docs_path('');
	$PAGE->set_pagelayout('frontpage');
	//$editing = $PAGE->user_is_editing();
	require_login();
	$PAGE->set_context(context_system::instance());
	$PAGE->set_title($SITE->fullname);
	$PAGE->set_heading($SITE->fullname);
	$PAGE->set_url($CFG->wwwroot."/recommender_system");
	//$courserenderer = $PAGE->get_renderer('core', 'course');
	echo $OUTPUT->header();

if (has_capability('moodle/course:managefiles', context_system::instance()))
{
	session_start();

//----------------------------CONTROLLO CAMBIO LINGUA-------------------------------------
	if(!isset($_SESSION['lingua_t0']))
	{
		$_SESSION['lingua_t0'] = current_language();
		$_SESSION['scrivi_socket'] = 1;
	}
	else
	{
		$_SESSION['lingua_t-1'] = $_SESSION['lingua_t0'];
		$_SESSION['lingua_t0'] = current_language();

		if($_SESSION['lingua_t-1'] != $_SESSION['lingua_t0'])
			$_SESSION['scrivi_socket'] = 0;
		else
			$_SESSION['scrivi_socket'] = 1;
	}
//----------------------------------------------------------------------------------------

	// post che arriva dall'index di course --- arrivo dall'index di course e inizio a creare un corso da zero
	$categoryid = $_POST['category'];

	//Creo il token --- è composto da user_id più tempo in millisecondi
	// Il token lo devo creare SOLO se è il primo accesso dell'utente
	$user = $USER->id;
	$milliseconds = round(microtime(true) * 1000);
	$token = $user.$milliseconds;

	// Pulizia variabili per backtracking finale
	$_SESSION['parteFinita'] = array();
	unset($_SESSION['allcompletate']);
	unset($_SESSION['summary']);

}

header('Access-Control-Allow-Origin: *');

?>

<?php if (has_capability('moodle/course:managefiles', context_system::instance())){?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Fase 1</title>

		<link href="themes/help/help.css" rel="stylesheet" />
		<link rel="stylesheet" href="themes/alertify/themes/alertify.core.css" />
		<link rel="stylesheet" href="themes/alertify/themes/alertify.default.css" />
		<link rel="stylesheet" href="themes/nuovostile.css" type="text/css"> <!-- STILE CSS PRINCIPALE -->

		<!-- BOOTSTRAP -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

		<!-- GOOGLE FONTS -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Quicksand">

		<!-- GOOGLE ICONS -->
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

		<!-- MATERIAL.IO -->
		<link href="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.css" rel="stylesheet">
  		<script src="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.js"></script>

		<!-- JQUERY -->
		<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.1.min.js"></script>

		<!-- SWEETALERT 2 -->
		<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

		<!-- JAVASCRIPT -->
		<script type="text/javascript" src="js/script.js"></script>

		<!--SLIDER JS FRAMEWORK -->
		<link rel="stylesheet" href="lib/slider/css/ion.rangeSlider.min.css"/>
    	<script src="lib/slider/js/ion.rangeSlider.min.js"></script>


	</head>

	<body style="background-color:#F0F0F0" onload="window.scrollTo(0,250);">



		<div class="container-fluid">

			<form method="POST" action="step1.php" name="myForm" id="myForm" onsubmit="return check()">


				<!-- Modal -->
				<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-sm">
					<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Inserisci i nomi delle sezioni</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" hidden="hidden"></button>
					</div>
					<div class="modal-body" style="height:20vh;overflow-y:scroll;" id="sectionsContent">



					</div>
					<div class="modal-footer">
					<button type="submit" class="btn btn-warning" id="btnAggiungiModuli" style="">Fine</button>
					</div>
					</div>
				</div>
				</div>



				<!-- RIGA CONTENENTE BOTTONE ANNULLA E PROGRESS BAR -->
				<div class="row" id="rowProgressBar">

					<!-- BOTTONE ANNULLA -->
					<div class="col-lg-2 offset-lg-1 col-md-12 col-sm-12 ">
						<button type="button" class="btn btn-danger" id="bottoneAnnulla"><span class="textButton">Annulla</span></button>
					</div>

					<!-- PROGRESS BAR -->
					<div class="col-lg-5 offset-lg-1 col-md-10 col-sm-10 offset-md-1 offset-sm-1">
						<div class="progress" id="progressBar">
							<div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="text-align:center;align-items:center;"><span class="textProgressBar">Fase 1</span></div>
						</div>
					</div>

				</div>

				<!-- RIGA CONTENTE L'INTESTAZIONE -->
				<div class="row justify-content-center" id="rowIntestazione">

					<!-- SCRITTA INTESTAZIONE -->
					<div class="col-auto">
						<p id="textIntestazione">Crea un <span id="textCorso">corso</span> a partire da zero</p>
					</div>

					<!-- IMMAGINE LAMPADINA -->
					<div class="col-auto">
						<img src="img/lampadina.png" id="imgLampadina">
					</div>

				</div>

				<!-- RIGA CONTENENTE LA FINESTRA PRINCIPALE -->
				<div class="row" id="rowContenitorePrincipale" name="contenitoreFase1">


					<div class="col-lg-8 offset-lg-2 col-md-12 col-sm-12 col-xs-12" id="contenitorePrincipale">

						<!-- RIGA CONTENENTE IL TITOLO DELLA FINESTRA -->
						<div class="row" id="rowTitleFinestra">
							<div class="col-12">
								<p class="titleFinestra">CARATTERISTICHE CORSO</p>
							</div>
						</div>

						<!-- RIGA DEL NOME DEL CORSO -->
						<div class="row justify-content-center" id="rowAttribute" style="margin-top:1vh;">

							<!-- SCRITTA 'NOME DEL CORSO' -->
							<div class="col-6" id="colAttribute">
								<p class="textAttribute">Nome del corso:</p>
							</div>

							<!-- INPUT TEXT NOME DEL CORSO -->
							<div class="col-6" id="colAttribute" style="display:flex;justify-content:center;">
								
									<input type="text" class="form-control" id="exampleFormControlInput1" placeholder="Nome" name="courseName">
								
							</div>

						</div>

						<!-- RIGA DELLA CATEGORIA DEL CORSO -->
						<div class="row justify-content-center" id="rowAttribute">

							<!-- SCRITTA CATEGORIA DEL CORSO E HELPER -->
							<div class="col-6" id="colAttribute">
								<p class="textAttribute" style="float:left;">Categoria del corso:</p>
											<svg id="HelpIconCategoriaCorso" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 27 27" data-bs-toggle="tooltip" data-bs-placement="right" title="Scegli tra le categorie disponibili quella più adatta al tuo corso">
												<path id="Tracciato_27" data-name="Tracciato 27" d="M0,0H27V27H0Z" fill="rgba(0,0,0,0)"/>
												<path id="Tracciato_28" data-name="Tracciato 28" d="M13.5,2A11.5,11.5,0,1,0,25,13.5,11.5,11.5,0,0,0,13.5,2Zm1.15,19.55h-2.3v-2.3h2.3Zm2.381-8.913L16,13.7A3.915,3.915,0,0,0,14.65,16.95h-2.3v-.575A4.629,4.629,0,0,1,13.7,13.12l1.426-1.449A2.249,2.249,0,0,0,15.8,10.05a2.3,2.3,0,0,0-4.6,0H8.9a4.6,4.6,0,0,1,9.2,0A3.659,3.659,0,0,1,17.031,12.637Z" transform="translate(0 0)"/>
											</svg>
										</button>
							</div>

							<!-- SELECTBOX CATEGORIA -->
							<div class="col-6" id="colAttribute" style="display:flex;justify-content:center;">
								<select class="form-select" aria-label="Default select example" id="selectCategoria" name="categoryCourse" onchange="showSubcategory(this.value)">
									<option selected>Categoria</option>

									<!-- RICHIESTA AL DB PER PRENDERE TUTTE LE CATEGORIE DEI CORSI -->
									<?php

										$connection = GetMyConnection();
										if ($connection < 0)
										{
											print errorDB($connection);
											die();
										}

										$result = mysqli_query($connection, "SELECT DISTINCT name FROM mdl_course_categories" );

										while($row=mysqli_fetch_row($result)){

											if($row[0]!="nuovaCategoria" && $row[0]!="nuovaCategoria2" && $row[0]!="SIAM"){

												echo '<option value="'.$row[0].'">'.$row[0].'</option>';

											}

											
										}


									?>

								</select>
							</div>

						</div>

						<!-- RIGA DELLA SOTTOCATEGORIA DEL CORSO -->
						<div class="row justify-content-center" id="rowAttribute">

							<!-- SCRITTA SOTTOCATEGORIA DEL CORSO E HELPER -->
							<div class="col-6" id="colAttribute">
								<p class="textAttribute" style="float:left;">Sottocategoria:</p>
											<svg id="HelpIconCategoriaCorso" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 27 27" data-bs-toggle="tooltip" data-bs-placement="right" title="Controlla se sono disponibili delle sottocategorie del corso">
												<path id="Tracciato_27" data-name="Tracciato 27" d="M0,0H27V27H0Z" fill="rgba(0,0,0,0)"/>
												<path id="Tracciato_28" data-name="Tracciato 28" d="M13.5,2A11.5,11.5,0,1,0,25,13.5,11.5,11.5,0,0,0,13.5,2Zm1.15,19.55h-2.3v-2.3h2.3Zm2.381-8.913L16,13.7A3.915,3.915,0,0,0,14.65,16.95h-2.3v-.575A4.629,4.629,0,0,1,13.7,13.12l1.426-1.449A2.249,2.249,0,0,0,15.8,10.05a2.3,2.3,0,0,0-4.6,0H8.9a4.6,4.6,0,0,1,9.2,0A3.659,3.659,0,0,1,17.031,12.637Z" transform="translate(0 0)"/>
											</svg>
										</button>
							</div>

							<!-- SELECTBOX SOTTOCATEGORIA -->
							<div class="col-6" id="colAttribute" style="display:flex;justify-content:center;">
								<select class="form-select" aria-label="Default select example" id="selectSottocategoria" name="subcategoryCourse" disabled>
									<option selected name="placeholderSottocategoria">Sottocategoria</option>

									<option value="SIAM" name="SIAM" style="display:none">SIAM</option>
									<option value="nuovaCategoria" name="nuovaCategoria" style="display:none">nuovaCategoria</option>
									<option value="nuovaCategoria2" name="nuovaCategoria2" style="display:none">nuovaCategoria2</option>


								</select>
							</div>

						</div>

						<!-- RIGA SUDDIVISIONE IN SEZIONI -->
						<div class="row justify-content-center" id="rowAttribute">

							<!-- SCRITTA 'SUDDIVIDI IN SEZIONI' E HELPER -->
							<div class="col-6" id="colAttribute">
								<p class="textAttribute" style="float:left;">Suddividi in sezioni:</p>
										<svg id="HelpIconCategoriaCorso" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 27 27" data-bs-toggle="tooltip" data-bs-placement="right" title="Decidi se creare un corso con un'unica sezione o dividerlo in più sezioni">
											<path id="Tracciato_27" data-name="Tracciato 27" d="M0,0H27V27H0Z" fill="rgba(0,0,0,0)"/>
											<path id="Tracciato_28" data-name="Tracciato 28" d="M13.5,2A11.5,11.5,0,1,0,25,13.5,11.5,11.5,0,0,0,13.5,2Zm1.15,19.55h-2.3v-2.3h2.3Zm2.381-8.913L16,13.7A3.915,3.915,0,0,0,14.65,16.95h-2.3v-.575A4.629,4.629,0,0,1,13.7,13.12l1.426-1.449A2.249,2.249,0,0,0,15.8,10.05a2.3,2.3,0,0,0-4.6,0H8.9a4.6,4.6,0,0,1,9.2,0A3.659,3.659,0,0,1,17.031,12.637Z" transform="translate(0 0)"/>
										</svg>
							</div>

							<!-- CHECKBOX -->
							<div class="col-6" id="colAttribute" style="display:flex;justify-content:center;">
								<div id="contenitoreCheckbox">
									<div class="form-check form-switch">
										<input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" onclick="visualizzaSezioni(this)" name="checkboxSections">
									</div>
								</div>
							</div>

						</div>

						<!-- RIGA NUMERO DI SEZIONI -->
						<div class='row justify-content-center' id='rowNumeroSezioni'>

							<!-- SCRITTA NUMERO DI SEZIONI -->
							<div class='col-6' id='colAttribute'>
								<p class='textAttribute' id='textNumeroSezioni'>Numero di sezioni:</p>
							</div>


							<!-- SELECTBOX NUMERO DI SEZIONI -->
							<div class='col-6' id='colAttribute' style="display:flex;justify-content:center;">

								
									<select class='form-select' aria-label='Default select example' id='selectNumeroSezioni' name="courseSections">
										<option selected>Numero</option>
										<option value=2>2</option>
										<option value=3>3</option>
										<option value=4>4</option>
										<option value=5>5</option>
										<option value=6>6</option>
										<option value=7>7</option>
										<option value=8>8</option>
										<option value=9>9</option>
										<option value=10>10</option>
									</select>
								

							</div>

						</div>


					</div>


					<!-- FRECCIA PROSSIMA SCHERMATA -->
					<div class="col-lg-2 col-md-0 col-sm-0 col-xs-0" id="colArrow">

							<span class="dot" onclick="showSectionsModal()" style="" id="backButton" style="cursor:pointer;">
								<svg id="newArrow" xmlns="http://www.w3.org/2000/svg" width="5vh" height="5vh" viewBox="0 0 50 50" style="margin-left:2.5vh;margin-top:2.5vh;">
									<rect id="Rettangolo_4" data-name="Rettangolo 4" width="50" height="50" fill="rgba(57,14,14,0)"/>
									<path id="Tracciato_26" data-name="Tracciato 26" d="M31.9,5,28.657,9.029,39.191,22.143H2v5.714H39.191L28.634,40.971,31.9,45,48,25Z" transform="translate(0 0)" fill="#707070" stroke="#707070" stroke-width="1"/>
								</svg>
							</span>

						
					</div>


				</div>


				<!-- RIGA CONTENENTE LA FINESTRA DI CARICAMENTO -->
				<div class="row" id="rowContenitorePrincipale" name="contenitoreCaricamento">


					<div class="col-lg-8 offset-lg-2 col-md-12 col-sm-12 col-xs-12" id="contenitorePrincipale">

						<!-- RIGA CONTENENTE IL TITOLO DELLA FINESTRA -->
						<div class="row" id="rowTitleFinestra">
							<div class="col-12">
								<p class="titleFinestra">CARICAMENTO...</p>
							</div>
						</div>

						<div class="row" id="rowSpinner">
							<div class="col-12">

								<div class="spinner-border text-warning" role="status">
									<span class="visually-hidden">Loading...</span>
								</div>

							</div>
						</div>


					</div>


				</div>

				<!-- RIGA CONTENENTE IL BOTTONE RIPRISTINA -->
				<div class="row" id="rowBottoneRipristina" style="margin-top:5vh;">

					<!-- BOTTONE RIPRISTINA -->
					<div class="col-12">
						<button type="button" class="btn btn-secondary" id="bottoneRipristinaCorso"><span class="textButton">Ripristina</span></button>
					</div>

				</div>

				<div class="row justify-content-center" id="rowArrowMobile">
						<div class="col-auto">
							<button type="button" class="btn btn-warning" id="arrowMobile">Vai avanti ></button>
						</div>
				</div>


				<!-- Button trigger modal -->
				<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" hidden="hidden" id="btnShowSectionsModal">
				</button>

			</form>

		
		</div>


		<!-- BOOTSTRAP -->
		<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>


	</body>
</html>

<?php } ?>

<?php
	echo $OUTPUT->footer();
?>
