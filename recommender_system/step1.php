<?php

	require_once('../config.php');
	require_once('../search_engine/php/mysql_conn.php');
	require_once('../metadata/metadata_page/aux_functions.php');

	session_start();

    $PAGE->set_pagetype('site-index');
	$PAGE->set_docs_path('');
	$PAGE->set_pagelayout('frontpage');
	//$editing = $PAGE->user_is_editing();
	require_login();
	$PAGE->set_context(context_system::instance());
	$PAGE->set_title($SITE->fullname);
	$PAGE->set_heading($SITE->fullname);
	$PAGE->set_url($CFG->wwwroot."/recommender_system/step1.php");
	//$courserenderer = $PAGE->get_renderer('core', 'course');
	echo $OUTPUT->header();
	
?>

<!-- MEMORIZZO GLOBALMENTE I DATI INSERITI DALL'UTENTE NELLA PRIMA SCHERMATA -->
<?php

	$_SESSION["courseName"]=$_POST["courseName"];
	$_SESSION["categoryName"]=$_POST["categoryName"];

	$courseName=$_SESSION["courseName"];
	$courseCategory=$_SESSION["courseCategory"];

	if(isset($_POST["courseSections"])){
		$_SESSION["courseSections"]=$_POST["courseSections"];
		$courseSections=$_SESSION["courseSections"];
	}

	header('Access-Control-Allow-Origin: *');


?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Fase 2</title>
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
    	<script src="lib/slider/js/ion.rangeSlider.min.js"></script>

	
		
	</head>
	<body style="background-color:#F0F0F0">


		<div class="container-fluid">

				<form method="POST" action="step2.php" name="myForm" id="myForm" onsubmit=" return showSpinner(1)">

					<!-- RIGA CONTENENTE BOTTONE ANNULLA E PROGRESS BAR -->
					<div class="row" id="rowProgressBar">

						<!-- BOTTONE ANNULLA -->
						<div class="col-lg-2 offset-lg-1 col-md-12 col-sm-12 ">
							<button type="button" class="btn btn-danger" id="bottoneAnnulla"><span class="textButton">Annulla</span></button>
						</div>

						<!-- PROGRESS BAR -->
						<div class="col-lg-5 offset-lg-1 col-md-10 col-sm-10 offset-md-1 offset-sm-1">
							<div class="progress" id="progressBar">
								<div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="text-align:center;align-items:center;"><span class="textProgressBar">Fase 2</span></div>
							</div>
						</div>

					</div>


					<!-- RIGA CONTENENTE LA FINESTRA PRINCIPALE -->
					<div class="row justify-content-center" id="rowContenitorePrincipale" name="contenitoreFase2">

						<!-- FRECCIA SCHERMATA PRECEDENTE -->
						<div class="col-lg-1 col-md-12 col-sm-12 col-xs-12" id="colBackArrow">

							<span class="dot" onclick="window.location.href='index.php'" style="margin-top:40vh;" id="backButton">
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
									<p class="titleFinestra">SCELTA DELL'ARGOMENTO</p>
								</div>

							</div>

							<!-- RIGA CERCA ARGOMENTO -->
							<div class="row justify-content-center" id="rowAttribute" style="margin-top:4vh;">

								<!-- INPUT CERCA ARGOMENTO -->
								<div class="col-lg-7 col-md-10 col-sm-12" id="colAttribute">
									<div class="input-group mb-3" id="searchArgument">
										<input type="text" class="form-control" placeholder="Cerca argomento" aria-label="Recipient's username" aria-describedby="button-addon2" id="inputSearchArgument" onkeyup="filtraArgomenti()">
										<svg id="search" xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26">
											<path id="Tracciato_698" data-name="Tracciato 698" d="M0,0H26V26H0Z" fill="none"/>
											<path id="Tracciato_699" data-name="Tracciato 699" d="M16.929,15.258h-.88l-.312-.3a7.254,7.254,0,1,0-.78.78l.3.312v.88L20.83,22.49l1.66-1.66Zm-6.686,0a5.015,5.015,0,1,1,5.015-5.015A5.008,5.008,0,0,1,10.243,15.258Z" fill="rgba(0,0,0,0.5)"/>
										</svg>
									</div>	
								</div>

							</div>


							<!-- RIGA RISULTATI ARGOMENTI -->
							<div class="row justify-content-center" id="rowArgumentResult">

							<!-- CONTENITORE DEI RISULTATI -->
								<div class="col-10" id="contenitoreArgumentResults">

									<div class="row justify-content-center" id="rowResults" style="border-bottom:3px solid white;background-color:#FCC63D">
										<div class="col-10">
											<p>Risultati ricerca</p>
										</div>
									</div>


									<div class="row" id="rowScroll">


										<div class="col-12">

											<ul id="myUL" name="listDisciplines">

												<?php

													$connection = GetMyConnection();

													if ($connection < 0){
														print errorDB($connection);
														die();
													}

													$disciplines=array();


													$result = mysqli_query($connection, "SELECT DISTINCT discipline_level_0,discipline_level_1,discipline_level_2,discipline_level_3,discipline_level_4,discipline_level_5 FROM mdl_merlot_data" );

													while($row=mysqli_fetch_row($result)){

														if(!in_array($row[0],$disciplines)){
															if($row[0]!="Absent"){
																array_push($disciplines,$row[0]);
															}
														}

														if(!in_array($row[1],$disciplines)){
															if($row[1]!="Absent"){
																array_push($disciplines,$row[1]);
															}
														}

														if(!in_array($row[2],$disciplines)){
															if($row[2]!="Absent"){
																array_push($disciplines,$row[2]);
															}
														}

														if(!in_array($row[3],$disciplines)){
															if($row[3]!="Absent"){
																array_push($disciplines,$row[3]);
															}
														}

														if(!in_array($row[4],$disciplines)){
															if($row[4]!="Absent"){
																array_push($disciplines,$row[4]);
															}
														}

														if(!in_array($row[5],$disciplines)){
															if($row[5]!="Absent"){
																array_push($disciplines,$row[5]);
															}
														}

													}

													sort($disciplines);

													$javascriptDisciplines=array();


													for($i=0;$i<count($disciplines);$i++){

														$currentDiscipline = str_replace(" ", "", $disciplines[$i]);

														array_push($javascriptDisciplines,$currentDiscipline);

															echo"<li>"
														."<a href='/' class='result' checked='false' style='border-bottom:3px solid white' id='".$currentDiscipline."' onclick='return false;'>".convert_RS($disciplines[$i])
															."<svg id='confirm' xmlns='http://www.w3.org/2000/svg' width='3vh' height='3vh' viewBox='0 0 24 24' visibility='hidden'>"
																."<path id='Tracciato_700' data-name='Tracciato 700' d='M0,0H24V24H0Z' fill='none'/>"
																."<path id='Tracciato_701' data-name='Tracciato 701' d='M9,16.17,4.83,12,3.41,13.41,9,19,21,7,19.59,5.59Z' fill='#707070'/>"
															."</svg>"
														."</a>"
														."<input type='hidden' value='false' class='inputDiscipline' name='".$currentDiscipline."'>"
													."</li>";


													}

													$_SESSION["disciplines"]=$javascriptDisciplines;
													


												?>

	


												
											</ul>

										</div>

										

									</div>

									


								</div>
							</div>

							<br>
						
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
							<button type="button" class="btn btn-secondary" id="bottoneRipristinaArgomenti"><span class="textButton">Ripristina</span></button>
						</div>

					</div>




					<!-- RIGA CONTENENTE LA FRECCIA PER TORNARE INDIETRO (SOLO MOBILE) -->
					<div class="row justify-content-center" style="margin-top:5vh;" id="rowArrowMobile">
						<div class="col-auto">
							<button type="button" class="btn btn-warning" id="arrowMobile" onclick="window.location.href='index.php'">< Torna indietro</button>
						</div>
					</div>



					<!-- RIGA CONTENENTE LA FRECCIA PER ANDARE AVANTI (SOLO MOBILE) -->
					<div class="row justify-content-center" style="margin-top:5vh;" id="rowArrowMobile">
						<div class="col-auto">
							<button type="submit" class="btn btn-warning" id="arrowMobile">Vai avanti ></button>
						</div>
					</div>



				</form>

		</div>



	<!-- BOOTSTRAP -->
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>

	<script>

		var discipline=<?php echo json_encode($_SESSION["disciplines"]) ?>;
		
		saveDisciplines(discipline);

	</script>

	</body>
</html>

<?php	
	//echo $OUTPUT->footer();	s_t_mod
?> 
