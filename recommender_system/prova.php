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

		<link rel="stylesheet" type="text/css" href="lib/circlebar/circle.css">
		<script src="lib/circlebar/circle.js"></script>
		<!-- Optionally add this to use a skin : -->
		<link rel="stylesheet" type="text/css" href="lib/circlebar/skin/simplecircle.css">


		
	</head>
	
	<body style="background-color:#F0F0F0" style="display:flex;justify-content:center;align-items:center" onload="prova()">
		

			<!-- RIGA CONTENENTE LA FINESTRA DI CARICAMENTO -->
			<div class="row" id="rowContenitorePrincipale" name="downloadError" style="margin-top:5vh;">

				<div class="col-lg-8 offset-lg-2 col-md-12 col-sm-12 col-xs-12" id="contenitorePrincipale">

					<!-- RIGA CONTENENTE IL TITOLO DELLA FINESTRA -->
					<div class="row" id="rowTitleFinestra">
						<div class="col-12">
							<p class="titleFinestra">CARICAMENTO...</p>
						</div>
					</div>

					<div class="row justify-content-center" style="height:50vh;display:flex;align-items:center;">
						<div class="col-auto">

							<div class="row justify-content-center">

								<div class="col-auto">
									<span class="material-icons" style="font-size:100px;color:lightcoral;">error</span>
								</div>

							</div>

							<div class="row justify-content-center" style="text-align:center;">

								<div class="col-auto">
									<span style="font-size:3vh;">Siamo spiacenti, è stato riscontrato un errore durante il caricamento delle risorse...riprova</span>
								</div>

							</div>

						</div>
					</div>


				</div>

			</div>





		
		<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>

								
	</body>
</html>
