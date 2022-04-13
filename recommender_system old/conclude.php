<?php
	require_once('../config.php');
	require_once($CFG->libdir .'/mysql_conn.php');
	require_once('../metadata/metadata_page/aux_functions.php');
	
	session_start();
	
	$PAGE->set_pagetype('site-index');
	$PAGE->set_docs_path('');
	$PAGE->set_pagelayout('frontpage');
	//$editing = $PAGE->user_is_editing();
	$PAGE->set_title($SITE->fullname);
	$PAGE->set_heading($SITE->fullname);
	//$courserenderer = $PAGE->get_renderer('core', 'course');
	require_login();
	$PAGE->set_context(context_system::instance());
	echo $OUTPUT->header();

if (has_capability('moodle/course:managefiles', context_system::instance()))	
{	
	
	if (isset($_POST['cambia_parte_corrente']))
		$doFase = -5;
	else
		$doFase = 4;

	$token = $_SESSION['data']['token'];
	$parte_corrente = $_SESSION['parte_corrente'];

	$msg = array();
	$msg["token"] = $token;

	if($_POST["step"] == "end")
		$msg["action"] = "stop";
	else
		$msg["action"] = "more";

	$msg["doFase"] = $doFase;
	$message= json_encode($msg);


	$message = $message . "\r\n";

	error_reporting(E_ALL);
	set_time_limit(0);
	ob_implicit_flush();

	$address = '::1';
	$port = 20005;

	if (($sock = socket_create(AF_INET6, SOCK_STREAM, 0)) === false) {
	   echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
	   exit(0);
	}
 	socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 30, 'usec' => 0));
	socket_set_option($sock, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 30, 'usec' => 0)); 

	if (socket_connect($sock, $address, $port) === false) {
		echo "socket_connect() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
		exit(0);
	}

	// invio primo messaggio alla socket 
	if (socket_write($sock, $message, strlen($message)) === false){
		echo "socket_write() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
		exit(0);
	}

	if (!isset($_POST['cambia_parte_corrente']))
	{
		$file = socket_read($sock,2048,PHP_NORMAL_READ);
		$file = json_decode($file, true);
		socket_close($sock);
	}

	//Se per ora vuoi finire la compilazione, allora ritorna alla pagina principale di moodle
	if($_POST["step"] == "end")
	{
		//Faccio una select per vedere se ho già inserito il corso corrente tra quelli incompleti
		$query = "SELECT COUNT(token) FROM mdl_rs_incomplete_course WHERE token = '".$_SESSION['data']['token']."'";
		$connection = GetMyConnection();
		if ($connection < 0)
		{
			print errorDB($connection);
			die();
		}
		$result = mysqli_query($connection, $query );
		$exists = mysqli_fetch_assoc($result);

		//se il corso non esiste allora lo inserisco
		if($exists['COUNT(token)'] == 0)
			$result = mysqli_query($connection, "INSERT INTO mdl_rs_incomplete_course(token, user_id, course_name, knowledge_area, number_of_parts, threshold, rules_support, keywords_min, keywords_max, rules_per_page) VALUES ('".$_SESSION['data']['token']."','".$USER->id."','".$_SESSION['data']['cn']."','".$_SESSION['data']['ka']."','".$_SESSION['data']['nop']."','".$_SESSION['data']['thresh']."','".$_SESSION['data']['rsup']."','".$_SESSION['data']['kmin']."','".$_SESSION['data']['kmax']."','".$_SESSION['data']['ruleXpage']."')" );
		

		//cancello tutte le parti inserite in mdl_rs_completed_modules relative a questo corso per poi andarle a reinserire con l'aggiuna magari di qualche parte in piu
		$query = "DELETE FROM mdl_rs_completed_modules WHERE token='".$token."'";
		$result = mysqli_query($connection, $query );

		//per ogni parte (modulo) già inserita (salvata in sessione)...
		for($j = 1; $j <= $parte_corrente; $j++)
		{
			$resources_sequence = array();
			//...prendo le risorse ad essa associate...
			for($i = 0; $i < count($_SESSION['course']['parts'][$j]['resources']); $i++)
				array_push($resources_sequence, $_SESSION['course']['parts'][$j]['resources'][$i]['id']);
			
			
			$r_sequence = implode(",", $resources_sequence); 
			//...e inserisco il tutto nel db 
			$query = "INSERT INTO mdl_rs_completed_modules(token, module_name, resources_sequence, position) VALUES ('".$_SESSION['data']['token']."','".$_SESSION['course']['parts'][$j]['name']."','".$r_sequence."','".$j."')";
			$result = mysqli_query($connection, $query);
		} 

		echo '<meta http-equiv="refresh" content="0; url="' . $CFG->wwwroot . '/index.php"" />';
	}
	//Altrimenti ritorna a step1 o step5 passandogli in post il token
	else
	{		
		if (!in_array($_SESSION['parte_corrente'], $_SESSION['parteFinita']))
			array_push($_SESSION['parteFinita'], $_SESSION['parte_corrente']);	

		if (isset($_POST['cambia_parte_corrente']))
		{
			print '
				<center>
					<form method="post" action="step5.php" name="formBack" id="formBack">
						<input name="i_back" id="i_back" value="' .$_POST['cambia_parte_corrente']. '" style="display: none;"/>
						<p align="center">
							<img id="loading" src="themes/spinner/ajax-loader.gif">
						</p>
					</form>
				</center>
				<script type="text/javascript">document.formBack.submit();</script>
			';
		}
		else
		{
			$token = $file['token'];
			$value = end($_SESSION['parteFinita']);
			print '
				<center>
					<form method="post" action="step1.php" name="myform">
						<input type="hidden" value="' . $token . '" name="token"/>
						<input type="hidden" value="' . $value . '" name="parteFinita" />
						<p align="center">
							<img id="loading" src="themes/spinner/ajax-loader.gif">
						</p>
					</form>
				</center>
				<script type="text/javascript">document.myform.submit();</script>
			';
		}
	}
}
	echo $OUTPUT->footer();
?>