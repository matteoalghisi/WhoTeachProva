<?php
	require_once('../config.php');
	require_once('../metadata/metadata_page/aux_functions.php');
	require_once('functions.php');
	require_once('../search_engine/php/mysql_conn.php');
	require_once('dbConnection.php');

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
		<title>Fase 3</title>
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
		
	</head>
	
	<body style="background-color:#F0F0F0"> 
		

		<div class="container-fluid">

			<form method="POST" action="step3.php" name="myForm" id="myForm" onsubmit="return showSpinner(2)">

				<!-- RIGA CONTENENTE BOTTONE ANNULLA E PROGRESS BAR -->
				<div class="row" id="rowProgressBar">

					<!-- BOTTONE ANNULLA -->
					<div class="col-lg-2 offset-lg-1 col-md-12 col-sm-12 ">
						<button type="button" class="btn btn-danger" id="bottoneAnnulla"><span class="textButton">Annulla</span></button>
					</div>

					<!-- PROGRESS BAR -->
					<div class="col-lg-5 offset-lg-1 col-md-10 col-sm-10 offset-md-1 offset-sm-1">
						<div class="progress" id="progressBar">
							<div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="text-align:center;align-items:center;"><span class="textProgressBar">Fase 3</span></div>
						</div>
					</div>

				</div>


				<!-- RIGA CONTENENTE LA FINESTRA PRINCIPALE -->
				<div class="row justify-content-center" id="rowContenitorePrincipale" name="contenitoreFase3">

					<!-- FRECCIA SCHERMATA PRECEDENTE -->
					<div class="col-lg-1 col-md-12 col-sm-12 col-xs-12" id="colBackArrow">

						<span class="dot" onclick="window.location.href='step1.php'" style="margin-top:40vh;" id="backButton">
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
								<p class="titleFinestra">SCELTA DELLE PREFERENZE</p>
							</div>

						</div>


						<!-- RIGA CONTENENTE LA LINGUA DELLE RISORSE -->
						<div class="row" id="rowPreference">

							<div class="col-1 offset-1" id="colIconaAttributo">
								<span class="material-icons" id="iconAttribute">translate</span>
							</div>

							<div class="col-3" id="colAttributo">
								<p id="textPreference">Lingua:</p>
							</div>

							
							<div class="col-7" id="colSelectAttribute">

								<div id="contenitoreMultiselect">

											<div class="dropdown">
												<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false" name="btnMultiselect">
													Seleziona lingua
												</button>
												<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1" id="dropdownMenuButton1" style="height:20vh;overflow-y:auto;" name="listaLingue">

														<?php

															$connection = connectToDB();

															if ($connection < 0){
																print errorDB($connection);
																die();
															}

														
															$result = mysqli_query($connection, "SELECT DISTINCT language FROM mdl_merlot_data ORDER BY language ASC" );

															while($row=mysqli_fetch_row($result)){

																echo "<div class='row' id='multiselectElement'>"
																."<div class='col-2 offset-1'>"
																	."<input  class='form-check-input' type='checkbox' id='flexCheckDefault' style='margin-top:1.2vh;margin-left:0.5vh;width:2.5vh;height:2.5vh;border-radius:5px !important;' name='".$row[0]."'>"
																."</div>"
																."<div class='col-9'>"
																	."<li style='cursor:pointer' onclick='clickCheckbox(this)'  name='".$row[0]."'><a class='dropdown-item' name='dropdown-language'>".$row[0]."</a></li>"
																."</div>"
															."</div>";


															}

														?>
														
													
												</ul>
										
											
											</div>
									
								</div>
							</div>

						</div>

						<!-- RIGA CONTENENTE LA DIFFICOLTA' DELLE RISORSE -->
						<div class="row" id="rowPreference">

							<div class="col-1 offset-1" id="colIconaAttributo">
								<img src="img/difficulty.png" id="iconAttribute" style="width:7vh;height:4vh;">
							</div>

							<div class="col-3" id="colAttributo">
								<p id="textPreference">Difficoltà:</p>
							</div>

							<div class="col-7" id="colSelectAttribute">

								<div id="contenitoreMultiselect">

											<div class="dropdown">
												<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false" name="btnMultiselect">
													Seleziona difficoltà
												</button>
												<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1" id="dropdownMenuButton1" style="height:20vh;overflow-y:auto;" name="listaDifficolta">

														<?php

															$connection = connectToDB();

															if ($connection < 0){
																print errorDB($connection);
																die();
															}

														
															$result = mysqli_query($connection, "SELECT DISTINCT difficulty FROM mdl_merlot_data ORDER BY difficulty ASC" );


															while($row=mysqli_fetch_row($result)){


																	echo "<div class='row' id='multiselectElement'>"
																	."<div class='col-2 offset-1'>"
																		."<input class='form-check-input' type='checkbox' value='false' id='flexCheckDefault' style='margin-top:1.2vh;margin-left:0.5vh;width:2.5vh;height:2.5vh;border-radius:5px !important;' name='".$row[0]."'>"
																	."</div>"
																	."<div class='col-9'>"
																		."<li id='topLanguage' style='cursor:pointer' onclick='clickCheckbox(this)'  name='".$row[0]."'><a class='dropdown-item' name='dropdown-difficulty'>".$row[0]."</a></li>"
																	."</div>"
																."</div>";

														

															}

														?>
														
													
												</ul>
										
											
											</div>
									
								</div>
							</div>

						</div>

						<!-- RIGA CONTENENTE IL TEMPO DI LETTURA DELLE RISORSE -->
						<div class="row" id="rowPreference">

							<div class="col-1 offset-1" id="colIconaAttributo">
								<img src="img/tempodilettura.png" id="iconAttribute" style="width:5vh;height:5vh;">
							</div>

							<div class="col-3" id="colAttributo">
								<p id="textPreference">Tempo di lettura:</p>
							</div>

							<div class="col-7" id="colSelectAttribute">

								<div id="contenitoreMultiselect">

											<div class="dropdown">
												<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false" name="btnMultiselect">
													Seleziona tempo
												</button>
												<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1" id="dropdownMenuButton1" style="height:20vh;overflow-y:auto;" name="listaTempo">

														<?php

															$connection = connectToDB();

															if ($connection < 0){
																print errorDB($connection);
																die();
															}

														
															$result = mysqli_query($connection, "SELECT DISTINCT duration FROM mdl_merlot_data ORDER BY duration ASC" );

															while($row=mysqli_fetch_row($result)){

																echo "<div class='row' id='multiselectElement'>"
																."<div class='col-2 offset-1'>"
																	."<input class='form-check-input' type='checkbox' value='false' id='flexCheckDefault' style='margin-top:1.2vh;margin-left:0.5vh;width:2.5vh;height:2.5vh;border-radius:5px !important;' name='".$row[0]."'>"
																."</div>"
																."<div class='col-9'>"
																	."<li style='cursor:pointer' onclick='clickCheckbox(this)'  name='".$row[0]."'><a class='dropdown-item' name='dropdown-duration'>".$row[0]."</a></li>"
																."</div>"
															."</div>";

													

															}

														?>
														
													
												</ul>
										
											
											</div>
									
								</div>
							</div>

						</div>

						<!-- RIGA CONTENENTE IL FORMATO DELLE RISORSE -->
						<div class="row" id="rowPreference">

							<div class="col-1 offset-1" id="colIconaAttributo">
								<span class="material-icons" id="iconAttribute">feed</span>
							</div>

							<div class="col-3" id="colAttributo">
								<p id="textPreference">Formato:</p>
							</div>

							<div class="col-7" id="colSelectAttribute">

								<div id="contenitoreMultiselect">

											<div class="dropdown">
												<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false" name="btnMultiselect">
													Seleziona formato
												</button>
												<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1" id="dropdownMenuButton1" style="height:20vh;overflow-y:auto;" name="listaFormato">

														<?php

															$connection = connectToDB();

															if ($connection < 0){
																print errorDB($connection);
																die();
															}

														
															$result = mysqli_query($connection, "SELECT DISTINCT format FROM mdl_merlot_data ORDER BY format ASC" );

															while($row=mysqli_fetch_row($result)){

																echo "<div class='row' id='multiselectElement'>"
																."<div class='col-2 offset-1'>"
																	."<input class='form-check-input' type='checkbox' value='false' id='flexCheckDefault' style='margin-top:1.2vh;margin-left:0.5vh;width:2.5vh;height:2.5vh;border-radius:5px !important;' name='".$row[0]."'>"
																."</div>"
																."<div class='col-9'>"
																	."<li style='cursor:pointer' onclick='clickCheckbox(this)'  name='".$row[0]."'><a class='dropdown-item' name='dropdown-format'>".$row[0]."</a></li>"
																."</div>"
															."</div>";

													

															}

														?>
														
													
												</ul>
										
											
											</div>
									
								</div>
							</div>

						</div>

						<!-- RIGA CONTENENTE IL TIPO DELLE RISORSE -->
						<div class="row" id="rowPreference">

							<div class="col-1 offset-1" id="colIconaAttributo">
								<img src="img/type.png" id="iconAttribute" style="width:5vh;height:5vh;">
							</div>

							<div class="col-3" id="colAttributo">
								<p id="textPreference">Tipo:</p>
							</div>

							<div class="col-7" id="colSelectAttribute">

								<div id="contenitoreMultiselect">

											<div class="dropdown">
												<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false" name="btnMultiselect">
													Seleziona tipo
												</button>
												<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1" id="dropdownMenuButton1" style="height:20vh;overflow-y:auto;" name="listaTipo">

														<?php

															$connection = connectToDB();

															if ($connection < 0){
																print errorDB($connection);
																die();
															}

														
															$result = mysqli_query($connection, "SELECT DISTINCT type FROM mdl_merlot_data ORDER BY type ASC" );

															while($row=mysqli_fetch_row($result)){

																echo "<div class='row' id='multiselectElement'>"
																."<div class='col-2 offset-1'>"
																	."<input class='form-check-input' type='checkbox' value='false' id='flexCheckDefault' style='margin-top:1.2vh;margin-left:0.5vh;width:2.5vh;height:2.5vh;border-radius:5px !important;' name='".$row[0]."'>"
																."</div>"
																."<div class='col-9'>"
																	."<li style='cursor:pointer' onclick='clickCheckbox(this)'  name='".$row[0]."'><a class='dropdown-item' name='dropdown-type'>".$row[0]."</a></li>"
																."</div>"
															."</div>";


															}

														?>
														
													
												</ul>
										
											
											</div>
									
								</div>
							</div>

						</div>

						<!-- RIGA CONTENENTE L'ETA'' DELLE RISORSE -->
						<div class="row" id="rowPreference">

							<div class="col-1 offset-1" id="colIconaAttributo">
								<img src="img/age.png" id="iconAttribute" style="width:5vh;height:5vh;">
							</div>

							<div class="col-3" id="colAttributo">
								<p id="textPreference">Età:</p>
							</div>

							<div class="col-7" id="colSelectAttribute">

							 	<input type="text" class="js-range-slider" name="my_range" value="" />
								
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
						<button type="button" class="btn btn-secondary" id="bottoneRipristinaPreferenze"><span class="textButton">Ripristina</span></button>
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
						<button type="button" class="btn btn-warning" id="arrowMobile" onclick="window.location.href='step1.php'">< Torna indietro</button>
					</div>
				</div>



			</form>

		</div>

		<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    	

															
															
	</body>
</html>
<?php	
	//echo $OUTPUT->footer();	s_t_mod
?>
