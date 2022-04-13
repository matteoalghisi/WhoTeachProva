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

	<body style="background-color:#F0F0F0">

		<div class="container-fluid">

			<form method="POST" action="step1.php" name="myForm" id="myForm" onsubmit="return check()">

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

				<!-- RIGA CONTENENTE LA FINESTRA PRINCIPALE -->
				<div class="row" id="rowContenitorePrincipale" style="margin-top:10vh;">


					<div class="col-lg-8 offset-lg-2 col-md-12 col-sm-12 col-xs-12" id="contenitorePrincipale">

						<!-- RIGA CONTENENTE IL TITOLO DELLA FINESTRA -->
						<div class="row" id="rowTitleFinestra">
							<div class="col-12">
								<p class="titleFinestra">CARATTERISTICHE CORSO</p>
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


					<!-- FRECCIA PROSSIMA SCHERMATA -->
					<div class="col-lg-2 col-md-0 col-sm-0 col-xs-0" id="colArrow">

							<button type="submit" value="" id="submitForm">
								<svg id="newArrow" xmlns="http://www.w3.org/2000/svg" width="5vh" height="5vh" viewBox="0 0 50 50">
									<rect id="Rettangolo_4" data-name="Rettangolo 4" width="50" height="50" fill="rgba(57,14,14,0)"/>
									<path id="Tracciato_26" data-name="Tracciato 26" d="M31.9,5,28.657,9.029,39.191,22.143H2v5.714H39.191L28.634,40.971,31.9,45,48,25Z" transform="translate(0 0)" fill="#707070" stroke="#707070" stroke-width="1"/>
								</svg>

							</button>

						
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
							<button type="submit" class="btn btn-warning" id="arrowMobile">Vai avanti ></button>
						</div>
					</div>

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
