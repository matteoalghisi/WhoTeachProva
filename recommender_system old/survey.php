<?php
	require_once('../config.php');
	require_once($CFG->libdir .'/mysql_conn.php');
	require_once('../matadata/metadata_page/aux_functions.php');

	session_start();

	// In questa pagina si arriva solo se l'utente può effettivamente valutare l'RS
	$connection = GetMyConnection();
	if ($connection < 0)
	{
		print errorDB($connection);
		die();
	}
	$result = mysqli_query($connection, "SELECT COUNT(*) as totale FROM nett_developedby WHERE user_id = ".$USER->id." AND fromrs = 1" );
	$fromrsResult = mysqli_fetch_assoc($result);
	$fromrs = $fromrsResult['totale'];

	if ($fromrs <= 0)
	{
		// Redirect to home
		header("location: " . $CFG->wwwroot);
	}


    $PAGE->set_pagetype('site-index');
	$PAGE->set_docs_path('');
	$PAGE->set_pagelayout('frontpage');
	//$editing = $PAGE->user_is_editing();
	$PAGE->set_title($SITE->fullname);
	$PAGE->set_heading($SITE->fullname);
	//$courserenderer = $PAGE->get_renderer('core', 'course');
	require_login();
	echo $OUTPUT->header();
	

	//----------------------------CONTROLLO CAMBIO LINGUA-------------------------------------	
	if(!isset($_SESSION['lingua_t0']))
		$_SESSION['lingua_t0'] = current_language();
	else
	{
		$_SESSION['lingua_t-1'] = $_SESSION['lingua_t0'];
		$_SESSION['lingua_t0'] = current_language();
	}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>NETT RS</title>
		<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
		<meta name="generator" content="HAPedit 3.1">
	</head>
	<body>
		<br>
		<div>
			<center>
				<a href="#">
					<img src="themes/img/logoRS.jpg" alt="" />
				</a>
			</center>
		</div>
		<br>
			
		<center>
			<iframe id="google_frame" src="https://docs.google.com/forms/d/1c-nw-BLbSzYUwN-xpRzDZcedvFfF2qg0cnR-qA6thRs/viewform?embedded=true" width="600" height="500" frameborder="0" marginheight="0" marginwidth="0">Caricamento in corso...</iframe>
		</center>

												
	</body>
</html>

<?php	
	echo $OUTPUT->footer();
?> 
