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
		<title>Fase 4</title>
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

		<!-- CIRCULAR PROGRESSBAR -->
		<script type="text/javascript" src="lib/circularprogressbar/circularProgressBar.min.js"></script>

		
	</head>
	
	<body style="background-color:#F0F0F0" onload="getRecommendations()">
		

		<div class="container-fluid">

			<form method="POST" action="step4.php" name="myForm" id="myForm" onsubmit="return showSpinner(3)">


						<!-- MODAL NOMI SEZIONI -->
						<div class="modal fade" id="modalSections" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="staticBackdropLabel">Seleziona modulo</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" hidden="hidden"></button>
							</div>
							<div class="modal-body" id="moduleContent">

								

								
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-warning" id="btnSeparaRisorse">Seleziona</button>
							</div>
							</div>
						</div>
						</div>



			<!-- MODAL ANTEPRIMA RISORSA -->
			<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" id="modalRisorsa">
				<div class="modal-content" id="modalRisorsa">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">
							
						</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>


							<div class="modal-body">

							<!-- MODALITA' DESKTOP FULL -->
                            <div class="row" id="rowAnteprimaRisorse" name="rowFullDesktop">

                                <div class="col-auto" id="colAnteprimaImage">
                                    <img src="img/resource3.svg" class="anteprimaImage" id="immagineAnteprima">
                                </div>

								<!-- ATTRIBUTI DELLA RISORSA -->
                                <div class="col-auto" id="colAnteprimaAttributes">


									<div class="containerAttributi">

										<div class="row justify-content-center">

											<div class="col-auto">
												<span>Attributi risorsa</span>
											</div>

										</div>

										<div class="row justify-content-center" id="rowAnteprimaAttribute" style="margin-top:1vh;">

											<div class="col-1" id="colAnteprimaAttribute">

											<span class="material-icons" id="anteprimaIconAttribute">language</span>

											</div>

											<div class="col-7" id="colAnteprimaAttribute">

												<span class="anteprimaTextAttribute" id="languageResource"></span>

											</div>

										</div>

										<div class="row justify-content-center" id="rowAnteprimaAttribute">

											<div class="col-1" id="colAnteprimaAttribute">

												<span class="material-icons" id="anteprimaIconAttribute">wifi_tethering</span>

											</div>

											<div class="col-7" id="colAnteprimaAttribute">

												<span class="anteprimaTextAttribute" id="difficultyResource"></span>

											</div>

										</div>

										<div class="row justify-content-center" id="rowAnteprimaAttribute">

											<div class="col-1" id="colAnteprimaAttribute">

												<span class="material-icons" id="anteprimaIconAttribute">schedule</span>


											</div>

											<div class="col-7" id="colAnteprimaAttribute">

												<span class="anteprimaTextAttribute" id="durationResource"></span>

											</div>

										</div>

										<div class="row justify-content-center" id="rowAnteprimaAttribute">

											<div class="col-1" id="colAnteprimaAttribute">

												<span class="material-icons" id="anteprimaIconAttribute">format_list_bulleted</span>

											</div>

											<div class="col-7" id="colAnteprimaAttribute">

												<span class="anteprimaTextAttribute" id="formatResource"></span>

											</div>

										</div>

										<div class="row justify-content-center" id="rowAnteprimaAttribute">

											<div class="col-1" id="colAnteprimaAttribute">

												<span class="material-icons" id="anteprimaIconAttribute">description</span>


											</div>

											<div class="col-7" id="colAnteprimaAttribute">

												<span class="anteprimaTextAttribute"id="typeResource"></span>

											</div>

										</div>

										<div class="row justify-content-center" id="rowAnteprimaAttribute">

											<div class="col-1" id="colAnteprimaAttribute">

												<span class="material-icons" id="anteprimaIconAttribute">badge</span>


											</div>

											<div class="col-7" id="colAnteprimaAttribute">

												<span class="anteprimaTextAttribute" id="ageResource"></span>

											</div>
										</div>

									</div>

								</div>


								<!-- ATTRIBUTI DELL'UTENTE -->
								<div class="col-auto" id="colAnteprimaAttributes" style="border-radius:0px;">


									<div class="containerAttributi">

										<div class="row justify-content-center">

											<div class="col-auto">
												<span>Attributi utente</span>
											</div>

										</div>

										<div class="row justify-content-center" id="rowAnteprimaAttribute" style="margin-top:1vh;">

											<div class="col-1" id="colAnteprimaAttribute">

											<span class="material-icons" id="anteprimaIconAttribute">language</span>

											</div>

											<div class="col-7" id="colAnteprimaAttribute">

												<span class="anteprimaTextAttribute" id="languageAccount"></span>

											</div>

										</div>

										<div class="row justify-content-center" id="rowAnteprimaAttribute">

											<div class="col-1" id="colAnteprimaAttribute">

												<span class="material-icons" id="anteprimaIconAttribute">wifi_tethering</span>

											</div>

											<div class="col-7" id="colAnteprimaAttribute">

												<span class="anteprimaTextAttribute" id="difficultyAccount"></span>

											</div>

										</div>

										<div class="row justify-content-center" id="rowAnteprimaAttribute">

											<div class="col-1" id="colAnteprimaAttribute">

												<span class="material-icons" id="anteprimaIconAttribute">schedule</span>


											</div>

											<div class="col-7" id="colAnteprimaAttribute">

												<span class="anteprimaTextAttribute" id="durationAccount"></span>

											</div>

										</div>

										<div class="row justify-content-center" id="rowAnteprimaAttribute">

											<div class="col-1" id="colAnteprimaAttribute">

												<span class="material-icons" id="anteprimaIconAttribute">format_list_bulleted</span>

											</div>

											<div class="col-7" id="colAnteprimaAttribute">

												<span class="anteprimaTextAttribute" id="formatAccount"></span>

											</div>

										</div>

										<div class="row justify-content-center" id="rowAnteprimaAttribute">

											<div class="col-1" id="colAnteprimaAttribute">

												<span class="material-icons" id="anteprimaIconAttribute">description</span>


											</div>

											<div class="col-7" id="colAnteprimaAttribute">

												<span class="anteprimaTextAttribute"id="typeAccount"></span>

											</div>

										</div>

										<div class="row justify-content-center" id="rowAnteprimaAttribute">

											<div class="col-1" id="colAnteprimaAttribute">

												<span class="material-icons" id="anteprimaIconAttribute">badge</span>


											</div>

											<div class="col-7" id="colAnteprimaAttribute">

												<span class="anteprimaTextAttribute" id="ageAccount"></span>

											</div>
										</div>

									</div>

								</div>


								<div class="col" id="colAnteprimaDescription">

									<div class="row justify-content-center" style="margin-top:2.2vh;font-family:'Roboto',sans-serif;height:3.5vh;">
										<div class="col-auto">
											<span>Descrizione</span>
										</div>
									</div>

									

									<div class="row justify-content-center" style="margin-top:1vh;display:flex;justify-content:center;text-align:center;height:21vh;overflow-y:scroll;">

										<div class="col-auto">

											<p class="descrizioneRisorsa">

											
											</p>

										</div>

									</div>


									
								</div>

                            </div>


							<!-- MODALITA' DESKTOP RIDOTTO -->
                            <div class="row" id="rowAnteprimaRisorseSmartphone" name="rowSmallDesktop">

                                <div class="col-auto" id="colAnteprimaImage">
                                    <img src="" class="anteprimaImage" id="immagineAnteprimaSmartphone">
                                </div>


								<!-- ATTRIBUTI RISORSA -->
                                <div class="col-auto" id="colAnteprimaAttributesSmartphone">


									<div class="containerAttributi">

										<div class="row justify-content-center">

											<div class="col-auto">
												<span>Attributi risorsa</span>
											</div>

										</div>

										<div class="row justify-content-center" id="rowAnteprimaAttributeSmartphone" style="margin-top:1vh;">

											<div class="col-1" id="colAnteprimaAttributeSmartphone">

											<span class="material-icons" id="anteprimaIconAttributeSmartphone">language</span>

											</div>

											<div class="col-7" id="colAnteprimaAttributeSmartphone">

												<span class="anteprimaTextAttribute" id="languageResourceSmartphone"></span>

											</div>

										</div>

										<div class="row justify-content-center" id="rowAnteprimaAttributeSmartphone">

											<div class="col-1" id="colAnteprimaAttributeSmartphone">

												<span class="material-icons" id="anteprimaIconAttributeSmartphone">wifi_tethering</span>

											</div>

											<div class="col-7" id="colAnteprimaAttributeSmartphone">

												<span class="anteprimaTextAttribute" id="difficultyResourceSmartphone"></span>

											</div>

										</div>

										<div class="row justify-content-center" id="rowAnteprimaAttributeSmartphone">

											<div class="col-1" id="colAnteprimaAttributeSmartphone">

												<span class="material-icons" id="anteprimaIconAttributeSmartphone">schedule</span>


											</div>

											<div class="col-7" id="colAnteprimaAttributeSmartphone">

												<span class="anteprimaTextAttribute" id="durationResourceSmartphone"></span>

											</div>

										</div>

										<div class="row justify-content-center" id="rowAnteprimaAttributeSmartphone">

											<div class="col-1" id="colAnteprimaAttributeSmartphone">

												<span class="material-icons" id="anteprimaIconAttributeSmartphone">format_list_bulleted</span>

											</div>

											<div class="col-7" id="colAnteprimaAttributeSmartphone">

												<span class="anteprimaTextAttribute" id="formatResourceSmartphone"></span>

											</div>

										</div>

										<div class="row justify-content-center" id="rowAnteprimaAttributeSmartphone">

											<div class="col-1" id="colAnteprimaAttributeSmartphone">

												<span class="material-icons" id="anteprimaIconAttributeSmartphone">description</span>


											</div>

											<div class="col-7" id="colAnteprimaAttributeSmartphone">

												<span class="anteprimaTextAttribute"id="typeResourceSmartphone"></span>

											</div>

										</div>

										<div class="row justify-content-center" id="rowAnteprimaAttributeSmartphone">

											<div class="col-1" id="colAnteprimaAttributeSmartphone">

												<span class="material-icons" id="anteprimaIconAttributeSmartphone">badge</span>


											</div>

											<div class="col-7" id="colAnteprimaAttributeSmartphone">

												<span class="anteprimaTextAttribute" id="ageResourceSmartphone"></span>

											</div>
										</div>

									</div>

								</div>


								<!-- ATTRIBUTI UTENTE -->
								<div class="col-auto" style="height:30vh;background-color: #F5F5F5;display:flex;align-items: center;justify-content: center;border-radius:0px 41px 41px 0px;">

									<div class="containerAttributi">

										<div class="row justify-content-center">

											<div class="col-auto">
												<span>Attributi utente</span>
											</div>

										</div>


										<div class="row justify-content-center" id="rowAnteprimaAttributeSmartphone" style="margin-top:1vh;">

											<div class="col-1" id="colAnteprimaAttribute">

											<span class="material-icons" id="anteprimaIconAttribute">language</span>

											</div>

											<div class="col-7" id="colAnteprimaAttribute">

												<span class="anteprimaTextAttribute" id="languageAccountSmartphone"></span>

											</div>

										</div>

										<div class="row justify-content-center" id="rowAnteprimaAttributeSmartphone">

											<div class="col-1" id="colAnteprimaAttribute">

												<span class="material-icons" id="anteprimaIconAttribute">wifi_tethering</span>

											</div>

											<div class="col-7" id="colAnteprimaAttribute">

												<span class="anteprimaTextAttribute" id="difficultyAccountSmartphone"></span>

											</div>

										</div>

										<div class="row justify-content-center" id="rowAnteprimaAttributeSmartphone">

											<div class="col-1" id="colAnteprimaAttribute">

												<span class="material-icons" id="anteprimaIconAttribute">schedule</span>


											</div>

											<div class="col-7" id="colAnteprimaAttribute">

												<span class="anteprimaTextAttribute" id="durationAccountSmartphone"></span>

											</div>

										</div>

										<div class="row justify-content-center" id="rowAnteprimaAttributeSmartphone">

											<div class="col-1" id="colAnteprimaAttribute">

												<span class="material-icons" id="anteprimaIconAttribute">format_list_bulleted</span>

											</div>

											<div class="col-7" id="colAnteprimaAttribute">

												<span class="anteprimaTextAttribute" id="formatAccountSmartphone"></span>

											</div>

										</div>

										<div class="row justify-content-center" id="rowAnteprimaAttributeSmartphone">

											<div class="col-1" id="colAnteprimaAttribute">

												<span class="material-icons" id="anteprimaIconAttribute">description</span>


											</div>

											<div class="col-7" id="colAnteprimaAttribute">

												<span class="anteprimaTextAttribute"id="typeAccountSmartphone"></span>

											</div>

										</div>

										<div class="row justify-content-center" id="rowAnteprimaAttributeSmartphone">

											<div class="col-1" id="colAnteprimaAttribute">

												<span class="material-icons" id="anteprimaIconAttribute">badge</span>


											</div>

											<div class="col-7" id="colAnteprimaAttribute">

												<span class="anteprimaTextAttribute" id="ageAccountSmartphone"></span>

											</div>
										</div>

									</div>

								</div>


                            </div>

							<!-- DESCRIZIONE DESKTOP RIDOTTO -->
							<div class="row" id="descriptionSmartphone">
								<div class="col" style="border">

									<div class="row justify-content-center" style="height:5vh;">
										<div class="col-auto" style="display:flex;align-items:center;">

											<span>Descrizione</span>

										</div>
									</div>

									<div class="row justify-content-center" style="height:12vh;overflow-y:scroll;font-family:'Avenir Light'">
										<div class="col-auto" style="display:flex;align-items:center;text-align:center;">

											<span class="descrizioneRisorsa"></span>
										</div>
									</div>
									
								</div>
							</div>


							<div class="row" style="margin-top:5vh;display:flex;align-items:center;">

								<div class="col-auto">

									<div class="row">
										<div class="col-auto">
											<div id="contenitoreValutazioneRisorsa">
												<span id="valutazioneRisorsa"></span>
											</div>	
										</div>
									</div>

									<div class="row" style="text-align:center">
										<div class="col-12" id="valutazioneStelleRisorsa">

											
											
										</div>
									</div>


								</div>

								<div class="col-auto" style="font-family:'Avenir Light';">


									<div class="row" style="height:auto;display:flex;align-items:center;">
										<div class="col-auto">
											<span>5</span>
										</div>
										<div class="col">
											<div class="middle">
												<div class="bar-container">
													<div class="bar-5"></div>
												</div>
											</div>
										</div>
									</div>

									<div class="row" style="height:auto;display:flex;align-items:center;">
										<div class="col-auto">
											<span>4</span>
										</div>
										<div class="col">
											<div class="middle">
												<div class="bar-container">
													<div class="bar-4"></div>
												</div>
											</div>
										</div>
									</div>

									<div class="row" style="height:auto;display:flex;align-items:center;">
										<div class="col-auto">
										<span>3</span>
										</div>
										<div class="col">
											<div class="middle">
												<div class="bar-container">
													<div class="bar-3"></div>
												</div>
											</div>
										</div>
									</div>

									<div class="row" style="height:auto;display:flex;align-items:center;">
										<div class="col-auto">
										<span>2</span>
										</div>
										<div class="col">
											<div class="middle">
												<div class="bar-container">
													<div class="bar-2"></div>
												</div>
											</div>
										</div>
									</div>

									<div class="row" style="height:auto;display:flex;align-items:center;">
										<div class="col-auto">
										<span>1</span>
										</div>
										<div class="col">
											<div class="middle">
												<div class="bar-container">
													<div class="bar-1"></div>
												</div>
											</div>
										</div>
									</div>

									


								</div>

								<div class="col" style="display:flex;justify-content:center;align-items:center;">


									<div class="row justify-content-center">

										<div class="col-auto">
											
											
											<div class="pie" data-pie='{"percent": 100, "colorSlice": "#50d121", "colorCircle": "#e6e6e6", "fontWeight":"100", "stroke":1, "fontSize": "1.5rem", "fontColor": "#50d121", "size": "120"}' data-bs-toggle="tooltip" data-bs-placement="right" title="Affinità della risorsa"></div>


										</div>

									</div>

									
								</div>


							</div>


							<div class="row" style="margin-top:3vh">
								<div class="col-auto">
									<span class="material-icons" style="font-size:30px;cursor:pointer;" id="infoRegola">info</span>
								</div>
							</div>

							<div class="row justify-content-center" style="display:none" id="rowExplanation">
								<div class="col-auto" id="contenitoreRegola">

									<p id="regolaRisorsa"></p>

								</div>
							</div>

								
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-warning" id="btnAggiungiRisorsa" name=""></button>
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
							<div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="text-align:center;align-items:center;"><span class="textProgressBar">Fase 4</span></div>
						</div>
					</div>

				</div>


				<!-- RIGA CONTENENTE LA FINESTRA PRINCIPALE -->
				<div class="row justify-content-center" id="rowContenitorePrincipale" name="contenitoreFase4" style="display:none;">

					<!-- FRECCIA SCHERMATA PRECEDENTE -->
					<div class="col-lg-1 col-md-12 col-sm-12 col-xs-12" id="colBackArrow">

						<span class="dot" onclick="window.location.href='step2.php'" style="margin-top:40vh;" id="backButton">
							<svg xmlns="http://www.w3.org/2000/svg" width="5vh" height="5vh" viewBox="0 0 50 50" style="margin-left:2.5vh;margin-top:2.5vh;">
								<g id="arrow" transform="translate(50 50) rotate(180)">
									<rect id="Rettangolo_4" data-name="Rettangolo 4" width="50" height="50" fill="rgba(57,14,14,0)"/>
									<path id="Tracciato_26" data-name="Tracciato 26" d="M31.9,5,28.657,9.029,39.191,22.143H2v5.714H39.191L28.634,40.971,31.9,45,48,25Z" transform="translate(0 0)" fill="#707070" stroke="#707070" stroke-width="1"/>
								</g>
							</svg>
						</span>


					</div>


					<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12" id="contenitorePrincipale" style="margin-top:5vh;">

						<!-- RIGA CONTENENTE IL TITOLO DELLA FINESTRA -->
						<div class="row" id="rowTitleFinestra">

							<div class="col-12">
								<p class="titleFinestra">RISORSE CONSIGLIATE</p>
							</div>

						</div>

						<div class="row" id="scrollResources">
							<div class="col-12" id="elencoRisorse">


							</div>
						</div>


					
					</div>



					<!-- FRECCIA PROSSIMA SCHERMATA -->
					<div class="col-lg-1 col-md-12 col-sm-12 col-xs-12" id="colNextArrow">

						<button type="submit" value="" id="submitForm" style="margin-top:40vh">
							<svg id="newArrow" xmlns="http://www.w3.org/2000/svg" width="5vh" height="5vh" viewBox="0 0 50 50">
								<rect id="Rettangolo_4" data-name="Rettangolo 4" width="50" height="50" fill="rgba(57,14,14,0)"/>
								<path id="Tracciato_26" data-name="Tracciato 26" d="M31.9,5,28.657,9.029,39.191,22.143H2v5.714H39.191L28.634,40.971,31.9,45,48,25Z" transform="translate(0 0)" fill="#707070" stroke="#707070" stroke-width="1"/>
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
								<p class="titleFinestra">STIAMO ELABORANDO LA TUA RICHIESTA...</p>
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
						<button type="button" class="btn btn-secondary" id="bottoneRipristinaRisorse"><span class="textButton">Ripristina</span></button>
					</div>

				</div>




				<!-- RIGA CONTENENTE LA FRECCIA PER TORNARE INDIETRO (SOLO MOBILE) -->
				<div class="row justify-content-center" style="margin-top:5vh;" id="rowArrowMobile">
					<div class="col-auto">
						<button type="button" class="btn btn-warning" id="arrowMobile" onclick="window.location.href='step2.php'">< Torna indietro</button>
					</div>
				</div>



				<!-- RIGA CONTENENTE LA FRECCIA PER ANDARE AVANTI (SOLO MOBILE) -->
				<div class="row justify-content-center" style="margin-top:5vh;" id="rowArrowMobile">
					<div class="col-auto">
						<button type="submit" class="btn btn-warning" id="arrowMobile">Vai avanti ></button>
					</div>
				</div>

				<!-- Button trigger modal -->
				<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalSections" hidden="hidden" id="btnShowModalSections">
				</button>



			</form>

		</div>


		
		<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>  
	

	</body>
</html>
