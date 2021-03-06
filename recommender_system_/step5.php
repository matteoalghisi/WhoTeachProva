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
	
	<body style="background-color:#F0F0F0" onload="stampaNomeCorso()">
		

		<div class="container-fluid">


				<!-- Modal -->
				<div class="modal fade" id="waitingModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-sm modal-dialog-centered">
					<div class="modal-content">

						<div class="modal-body" style="display:flex;justify-content:center;align-items:center;">

							<img src="img/loading.gif" style="width:30vh;height:20vh;"> 
							
						</div>
					</div>
				</div>
				</div>

				<!-- Button trigger modal -->
				<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#waitingModal" hidden="hidden" id="btnWaitingModal">
				</button>


				<!-- RIGA CONTENENTE LA FINESTRA PRINCIPALE -->
				<div class="row justify-content-center" id="rowContenitorePrincipale" name="contenitoreFase6">


					<div class="col-lg-5 col-md-8 col-sm-10 col-xs-10" id="contenitorePrincipale" style="margin-top:5vh;height:auto;">

						<div class="row" id="rowStep5">
							<div class="col-12">

								<svg id="BottoneConferma" xmlns="http://www.w3.org/2000/svg" width="10vh" height="10vh" viewBox="0 0 65 65">
								<circle id="Ellisse_1" data-name="Ellisse 1" cx="32.5" cy="32.5" r="32.5" fill="#5fad55"/>
								<g id="confirm" transform="translate(15 15)">
									<path id="Tracciato_688" data-name="Tracciato 688" d="M0,0H35V35H0Z" fill="none"/>
									<path id="Tracciato_689" data-name="Tracciato 689" d="M12.5,24.849,5.718,17.258,3.41,19.825,12.5,30,32,8.157,29.708,5.59Z" transform="translate(0)" fill="#fff"/>
								</g>
								</svg>


							</div>
						</div>

						<div class="row" id="rowStep5">
							<div class="col-12">

								<p style="font-family:'Avenir Black';font-size:2.5vh;">Complimenti!</p>

							</div>
						</div>

						<div class="row" id="rowStep5">
							<div class="col-12" style="display:flex;justify-content:center;">

								<p style="width:60vh;">Il tuo corso <span id="confirmCourseName"></span> ?? stato creato correttamente</p>

							</div>
						</div>

						<div class="row" id="rowStep5" style="margin-top:1vh;">
							<div class="col-12">

									<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="100" height="44" viewBox="0 0 100 44">
									<defs>
										<pattern id="pattern" preserveAspectRatio="none" width="100%" height="100%" viewBox="0 0 763 333">
										<image width="763" height="333" xlink:href="data:image/png;base64,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"/>
										</pattern>
									</defs>
									<rect id="logors" width="100" height="44" fill="url(#pattern)"/>
									</svg>

							</div>
						</div>

						


					
					</div>



				</div>



				<!-- RIGA CONTENENTE LA FINESTRA DI CARICAMENTO -->
				<div class="row justify-content-center" id="rowContenitorePrincipale" name="contenitoreCaricamento" style="margin-top:5vh;">


					<div class="col-lg-3 col-md-5 col-sm-10 col-xs-10" id="contenitorePrincipale">


						<div class="row" id="rowSpinner" style="height:30vh;line-height:45vh;">
							<div class="col-12">

								<div class="spinner-border text-warning" role="status">
									<span class="visually-hidden">Loading...</span>
								</div>

							</div>
						</div>


					</div>


				</div>


				<!-- RIGA CONTENENTE I DUE BOTTONI -->
				<div class="row justify-content-center" id="rowBottoneRipristina" style="margin-top:5vh;">

					<!-- BOTTONE VAI AL CORSO -->
					<div class="col-auto">
						<button type="button" class="btn btn-secondary" id="bottoneVaiAlCorso">

							<div class="row justify-content-center">
								<div class="col-auto" style="display:flex;align-items:center;height:auto;">
									<span class="material-icons" id="homeIcon">east</span>
								</div>
								<div class="col" style="display:flex;align-items:center;height:auto;">
									<span class="textButton" style="margin-top:0.5vh;" id="homeText">Vai al corso</span>
								</div>
							</div>

							
						</button>
					</div>

					<!-- BOTTONE TORNA ALLA HOME -->
					<div class="col-auto">
						<button type="button" class="btn btn-secondary" id="bottoneTornaHome" onclick="window.location.href='../'">

							<div class="row justify-content-center">
								<div class="col-auto" style="display:flex;align-items:center;height:auto;">
									<span class="material-icons" id="homeIcon">home</span>
								</div>
								<div class="col" style="display:flex;align-items:center;height:auto;">
									<span class="textButton" style="margin-top:0.5vh;" id="homeText">Torna alla home</span>
								</div>
							</div>

							
						</button>
					</div>

				</div>

		</div>


		
		<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
		
    	

															
															
	</body>
</html>
