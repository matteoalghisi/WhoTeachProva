<?php
	require_once('../config.php');
	require_once('../metadata/metadata_page/aux_functions.php');
	require_once('functions.php');
	require_once('../search_engine/php/mysql_conn.php');

	session_start();

    $PAGE->set_pagetype('site-index');
	$PAGE->set_docs_path('');
	$PAGE->set_pagelayout('frontpage');
	//$editing = $PAGE->user_is_editing();
	require_login();
	$PAGE->set_context(context_system::instance());
	$PAGE->set_title($SITE->fullname);
	$PAGE->set_heading($SITE->fullname);
	$PAGE->set_url($CFG->wwwroot."/recommender_system/step2.php");
	//$courserenderer = $PAGE->get_renderer('core', 'course');
	
	echo $OUTPUT->header();

	header('Access-Control-Allow-Origin: *');

	

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
		<title>Fase 5</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="themes/nuovostile.css">
		<meta name="viewport" content="width=device-width, initial-scale=1">


		
		<!-- BOOTSTRAP -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

		<!-- GOOGLE FONTS -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Quicksand">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Monoton">

		<!-- GOOGLE ICONS -->
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

		<!-- MATERIAL.IO -->
		<link href="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.css" rel="stylesheet">
  		<script src="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.js"></script>

		<!-- JQUERY -->
		<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.1.min.js"></script>

		<!-- JAVASCRIPT -->
		<script src="js/script.js"></script>

		<!-- SWEETALERT 2 -->
		<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>


		<!--SLIDER JS FRAMEWORK -->
		<link rel="stylesheet" href="lib/slider/css/ion.rangeSlider.min.css"/>
    	<script type="text/javascript" src="lib/slider/js/ion.rangeSlider.min.js"></script>

		<!-- RATING CSS -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		
	</head>
	
	<body style="background-color:#F0F0F0" onload="stampaRiepilogo()">
		

		<div class="container-fluid">

			<form method="POST" action="step5.php" name="myForm" id="myForm" onsubmit="return showSpinner(4)">

						

				<!-- RIGA CONTENENTE BOTTONE ANNULLA E PROGRESS BAR -->
				<div class="row" id="rowProgressBar">

					<!-- BOTTONE ANNULLA -->
					<div class="col-lg-2 offset-lg-1 col-md-12 col-sm-12 ">
						<button type="button" class="btn btn-danger" id="bottoneAnnulla"><span class="textButton">Annulla</span></button>
					</div>

					<!-- PROGRESS BAR -->
					<div class="col-lg-5 offset-lg-1 col-md-10 col-sm-10 offset-md-1 offset-sm-1">
						<div class="progress" id="progressBar">
							<div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="text-align:center;align-items:center;"><span class="textProgressBar">Fase 5</span></div>
						</div>
					</div>

				</div>


				<!-- RIGA CONTENENTE LA FINESTRA PRINCIPALE -->
				<div class="row justify-content-center" id="rowContenitorePrincipale" name="contenitoreFase5">

					<!-- FRECCIA SCHERMATA PRECEDENTE -->
					<div class="col-lg-1 col-md-12 col-sm-12 col-xs-12" id="colBackArrow">

						<span class="dot" onclick="window.location.href='step3.php'" style="margin-top:40vh;" id="backButton">
							<svg xmlns="http://www.w3.org/2000/svg" width="5vh" height="5vh" viewBox="0 0 50 50" style="margin-left:2.5vh;margin-top:2.5vh;">
								<g id="arrow" transform="translate(50 50) rotate(180)">
									<rect id="Rettangolo_4" data-name="Rettangolo 4" width="50" height="50" fill="rgba(57,14,14,0)"/>
									<path id="Tracciato_26" data-name="Tracciato 26" d="M31.9,5,28.657,9.029,39.191,22.143H2v5.714H39.191L28.634,40.971,31.9,45,48,25Z" transform="translate(0 0)" fill="#707070" stroke="#707070" stroke-width="1"/>
								</g>
							</svg>
						</span>


					</div>


					<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12" id="contenitorePrincipale" style="margin-top:5vh;height:60vh;">

						<!-- RIGA CONTENENTE IL TITOLO DELLA FINESTRA -->
						<div class="row" id="rowTitleFinestra">

							<div class="col-12">
								<p class="titleFinestra">RIEPILOGO</p>
							</div>

						</div>

						<div class="row justify-content-center" style="" id="mainRowStep4">
							<div class="col-10" id="riepilogoCorso"> 
								
								<div class="row" id="singleRowStep4">
									<div class="col-12">
										<span style="" class="paragraphStep4">Caratteristiche corso</span>
									</div>
								</div>

								<div class="row" id="singleRowStep4">
									<div class="col-12">
										<span id="nomeCorso">Nome: <span id="boldCourseName" class="boldInfo"></span></span>
									</div>
								</div>

								<div class="row" id="singleRowStep4">
									<div class="col-12">
										<span id="categoriaCorso">Categoria: <span id="boldCourseCategory" class="boldInfo"></span></span>
									</div>
								</div>

								<div class="row" id="singleRowStep4">
									<div class="col-12">
										<span id="sottoCategoriaCorso">Sottocategoria: <span id="boldCourseSubcategory" class="boldInfo"></span></span>
									</div>
								</div>

								<div class="row" id="singleRowStep4">
									<div class="col-12">
										<span id="numeroSezioniCorso">Numero sezioni: <span id="boldCourseSectionNumbers" class="boldInfo"></span></span>
									</div>
								</div>

								<div class='row' style='height:1vh;background-color:white;margin-top:1vh;'>
									<div class='col-12'>

									</div>
								</div>

								<div class="row" id="singleRowStep4">
									<div class="col-12">
										<span class="paragraphStep4">Risorse aggiunte</span>
									</div>
								</div>

								<div id="risorseAggiunte">

								</div>

							</div>
						</div>


					
					</div>



					<!-- FRECCIA PROSSIMA SCHERMATA -->
					<div class="col-lg-1 col-md-12 col-sm-12 col-xs-12" id="colNextArrow">

						<button type="submit" value="" id="submitForm" style="margin-top:40vh;display:flex;align-items:center;justify-content:center;background-color:transparent;width:11vh;height:11vh;" onmouseover="showGreen()" onmouseout="showTransparent()">

							<svg id="BottoneConferma" xmlns="http://www.w3.org/2000/svg" width="11vh" height="11vh" viewBox="0 0 80 80">
							<g id="Ellisse_1" data-name="Ellisse 1" fill="none" stroke="#5fad55" stroke-width="1">
								<circle cx="40" cy="40" r="40" stroke="none"/>
								<circle cx="40" cy="40" r="39.5" fill="none" id="ellisseConferma"/>
							</g>
							<g id="confirm" transform="translate(15 15)">
								<path id="Tracciato_688" data-name="Tracciato 688" d="M0,0H50V50H0Z" fill="none"/>
								<path id="Tracciato_689" data-name="Tracciato 689" d="M17.263,36.683,6.929,24.428,3.41,28.572,17.263,45,47,9.734,43.506,5.59Z" transform="translate(0 0)" fill="#5fad55" name="tracciatoConferma"/>
							</g>
							</svg>



						</button>


					</div>


				</div>


				<!-- RIGA CONTENENTE LA FINESTRA DI CARICAMENTO -->
				<div class="row" id="rowContenitorePrincipale" name="contenitoreCaricamento" style="margin-top:5vh;">


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


				

				<!-- RIGA CONTENENTE LA FRECCIA PER ANDARE AVANTI (SOLO MOBILE) -->
				<div class="row justify-content-center" style="margin-top:5vh;" id="rowArrowMobile">
					<div class="col-auto">
						<button type="submit" class="btn btn-warning" id="arrowMobile">Vai avanti ></button>
					</div>
				</div>


				<!-- RIGA CONTENENTE LA FRECCIA PER TORNARE INDIETRO (SOLO MOBILE) -->
				<div class="row justify-content-center" style="margin-top:5vh;" id="rowArrowMobile">
					<div class="col-auto">
						<button type="button" class="btn btn-warning" id="arrowMobile" onclick="window.location.href='step3.php'">< Torna indietro</button>
					</div>
				</div>



			</form>

		</div>


		
		<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
		
    	

															
															
	</body>
</html>
