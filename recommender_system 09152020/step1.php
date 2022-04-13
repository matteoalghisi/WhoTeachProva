<?php

	require_once('../config.php');
	require_once($CFG->libdir .'/mysql_conn.php');
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

if (has_capability('moodle/course:managefiles', context_system::instance()))
{
	

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

	$doFase = 1; // Questo numero è un ID che identifica la fase che deve fare il server Java

	//----------------------------------------------------------------------------------------

	// unset variabili non per il nuovo modulo
	//unset($_SESSION['modulesvar']);
	unset($_SESSION['reg']);
	unset($_SESSION['rules']);
	unset($_SESSION['valuesvar']);
	unset($_SESSION['keywords']);
	unset($_SESSION['modn']);
	unset($_SESSION['resources']);

	//Controllo che la pagina sia stata riaggiornata SENZA CAMBIARE LINGUA
	if($_SESSION['scrivi_socket'] == 1)
	{
		//header('Content-Type: text/html; charset=utf-8');


		//post che arriva dall'index di my --- arrivo da my
		if(isset($_POST['incomplete_courses']))
		{
			$incomplete_courses = $_POST['incomplete_courses'];
			$nameAndToken = explode("^", $incomplete_courses);
			$incomplete_course_name = $nameAndToken[0];
			$incomplete_course_token = $nameAndToken[1];

			$connection = GetMyConnection();
			if ($connection < 0)
			{
				print("errore in connessione");
				print errorDB($connection);
				die();
			}
			$result = mysqli_query($connection, "SELECT * FROM mdl_rs_incomplete_course WHERE token = '".$incomplete_course_token."'" );
			$row = mysqli_fetch_row($result);

			$incomplete_course_values = array();
			$incomplete_course_values['token'] = $row[0];
			$incomplete_course_values['cn'] = $row[2];
			$incomplete_course_values['ka'] = $row[3];
			$incomplete_course_values['nop'] = $row[4];
			$incomplete_course_values['thresh'] = $row[5];
			$incomplete_course_values['rsup'] = $row[6];
			$incomplete_course_values['kmin'] = $row[7];
			$incomplete_course_values['kmax'] = $row[8];
			$incomplete_course_values['ruleXpage'] = $row[9];

			echo "<script>console.log('Debug Objects: " . $row[4] . "' );</script>";
			//print_r($incomplete_course_values);die();
			
			
			
			$data = $incomplete_course_values;

			//anche qui compongo corso da zero, ma poi lo vado a riempire con le parti gia inserite
			$course["name"]=$data["cn"];
			$course["ka"]=$data["ka"];
			$course["parts"]=array();

			for ($i=1; $i<=$data["nop"]; $i++)
			{
				$partName = 'Part not inserted yet';

				$part = array();
				$part["name"]=$partName;
				$part["resources"]= array();

				$course["parts"][$i]=$part;
			}
			$query = "SELECT module_name,resources_sequence,position FROM mdl_rs_completed_modules WHERE token = '".$data['token']."'";
			$completed_modules = array();
			$result = mysqli_query($connection, $query);
			while ($row = mysqli_fetch_row($result))
				array_push($completed_modules, $row);
			
			
			$connection = GetMyConnection('nettrs');
			if ($connection < 0)
			{
				print errorDB($connection);
				die();
			}
			//per ogni modulo gia completato...
			for($i=0; $i<count($completed_modules); $i++)
			{
				$moduleName = $completed_modules[$i][0];
				$position = $completed_modules[$i][2];
				$course["parts"][$position]['name'] = $moduleName;

				$resources_sequence = $completed_modules[$i][1];
				$moduleResources = explode(",", $resources_sequence);
				
				//per ogni risorsa appartenente al modulo in analisi
				for($j=0; $j<count($moduleResources); $j++)
				{
					//questa query andrà cambiata nel momento in cui si utilizzerà un dataset vero e di conseguenza le risorse nel db moodlenett (c'è il nome del db davanti alla tabella perchè altrimenti non va)
					$query = "SELECT module,name FROM nettrs.mdl_resources_name WHERE res = '".$moduleResources[$j]."'";
					$result2 = mysqli_query($connection, $query);
					$resource_features = mysqli_fetch_assoc($result2);
					$resource_type = $resource_features['module'];
					$resource_name = $resource_features['name'];
					$course["parts"][$position]['resources'][$j]['id'] = $moduleResources[$j];
					$course["parts"][$position]['resources'][$j]['name'] = $resource_name;
					$course["parts"][$position]['resources'][$j]['module'] = $resource_type;
				}
			}
			$_SESSION["course"]=$course;
		}
		
///////////////////////////////////////////////////////////////////////////////////////////////////////
		//nn so cos'è my (parte sopra) e sembra che non ci si arrivi mai quindi lo lascio cosi com'è
		
		
		//post che arriva da index.php --- arrivo da index.php
		else if(isset($_POST['ka']))
		{
			$data = $_POST;
			
			$course["name"]=$data["cn"];
			$course["ka"]=$data["ka"];
			$_SESSION["ka"] = $course["ka"];
			$course["parts"]=array();

			for ($i=1;$i<=$data["nop"];$i++)
			{
				$partName = 'Part not inserted yet';
				$part = array();
				$part["name"]=$partName;
				$part["resources"]= array();
				$course["parts"][$i]=$part;
			}
			$_SESSION["course"]=$course;
		}

		//Se almeno una di queste due post esiste allora vuol dire che sto arrivando da "my" oppure da "index.php". Altrimenti vuol dire che sto tornando indietro da step5
		if (isset($_POST["ka"]) || isset($_POST["incomplete_courses"]))
		{
			// sto arrivando dalla prima pagina. Estraggo le info che mi servono nei vari passaggi

			//salvo in sessione tutti i campi per andare a ricomporre il token nell'else quando torno indietro
			$data["doFase"] = $doFase;
			$_SESSION["data"]=$data;
			
			$token = $data["token"];
			$cn = $data["cn"];
			$ka = $data["ka"];
			$nop = $data["nop"];
			$ruleXpage = $data["ruleXpage"];			
			$data_complete = $data;

			// creo documento json con i dati passati in post e il valore del token
			$data_json = json_encode($data_complete);
			
			error_reporting(E_ALL);

			// Allow the script to hang around waiting for connections.
			set_time_limit(0);

			// Turn on implicit output flushing so we see what we're getting as it comes in.
			ob_implicit_flush();

			$address = '::1';
			$port = 20000;

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

			$message = $data_json. "\r\n";

			// invio primo messaggio alla socket 
			if (socket_write($sock, $message, strlen($message)) === false){
				echo "socket_write() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
				exit(0);
			}
			

			// ricevo la risposta dalla socket 
			$modules=socket_read($sock,200000,PHP_NORMAL_READ);
			
			$modulesvar = json_decode($modules, true);

			$_SESSION['modulesvar'] = $modulesvar;

			//se arrivo da index.php allora la currentPart la prendo dal server visto che la ritorna giusta
			if (isset($_POST["ka"]))
			{
				$_SESSION["fromMy"] = 0; //visto che nn sto arrivando da my lo setto a zero
				$_SESSION["parte_corrente"] = $modulesvar["currentPart"];
			} 
			//se arrivo da my allora la currentPart me la ricavo io da quelle che sn gia state completate perche il server non la ritorna giusta. Ritorna sempre 1
			else if(isset($_POST["incomplete_courses"]))
			{
				$_SESSION["fromMy"] = 1; //visto che sto arrivando da my lo setto a uno
				$parts_completed = count($completed_modules);
				$parte_corrente = $parts_completed + 1;
				$_SESSION["parte_corrente"] = $parte_corrente;
			}	  

			// chiudo il collegamento con la socket	
			socket_close($sock);
		}
		//else arrivo da conclude.php (quindi devo compilare le parti successive del corso)
		else
		{
			$token = $_SESSION['token']; 
			$data = $_SESSION['data'];
			$data_json = json_encode($data);

			error_reporting(E_ALL);

			// Allow the script to hang around waiting for connections.
			set_time_limit(0);

			// Turn on implicit output flushing so we see what we're getting as it comes in.
			ob_implicit_flush();

			$address = '::1';
			$port = 20000;

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

			$message = $data_json. "\r\n";

			// invio primo messaggio alla socket 
			if (socket_write($sock, $message, strlen($message)) === false)
			{
				echo "socket_write() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
				exit(0);
			}

			// ricevo la risposta dalla socket 
			$modules=socket_read($sock,200000,PHP_NORMAL_READ);
			$modulesvar = json_decode($modules, true);
			$_SESSION['modulesvar'] = $modulesvar;

			//se arrivo da conclude.php dopo che inizialmente sn partito da index.php allora la currentPart la prendo dal server visto che la ritorna giusta
			if ($_SESSION["fromMy"] == 0)
				$_SESSION["parte_corrente"] = $modulesvar["currentPart"];
			//se arrivo da conclude.php dopo che inizialmente sn partito da my allora la currentPart la incremento perche il server non la ritorna giusta. Ritorna sempre 1
			else if($_SESSION["fromMy"] == 1)
			{
				$tmp = $_SESSION["parte_corrente"];
				$tmp = $tmp + 1;
				$_SESSION["parte_corrente"] = $tmp;
			}
			// chiudo il collegamento con la socket	$ka
			socket_close($sock);
		}
	}
	
	// a questo punto determino la lista delle parole da visualizzare con la loro frequenza
	require_once('functions.php');
		
	$allStopWords = \file_get_contents('stopwords/stop-words.txt');
	$allStopWords = \explode("\n", $allStopWords);
	cleanStopWords($allStopWords);
	
	//la soluzione al problema cercato in questa debug stotto, sta nel fatto che il Server java non si collegava al db corretto 
	//
	//print debug
	//echo "modulesvar: ". implode("",$_SESSION['modulesvar']) ." fine";

 	$query = "
 		SELECT id, name
 		FROM mdl_course_sections
 		WHERE id IN (" . implode(",",$_SESSION['modulesvar']["modules"]) . ")";	

	//mi connetto all'altro database cioè "nettrs"
	$connection = GetMyConnection();
	if ($connection < 0)
	{
		print errorDB($connection);
		die();
	}
 	$result = mysqli_query($connection, $query);
 	$arr = array();
 	while($row = mysqli_fetch_row($result))
 		$arr[(int)$row[0]] = removeStopWords($row[1], $allStopWords);
	//dbebug
	//echo("arr: ".var_dump($arr));
    // calcolo la frequenza per ogni termine estratto da un titolo
    $frequency = array();
    foreach ($arr as $titolo)
    {
	   $words = explode(" ",$titolo);
	   foreach ($words as $w)
	   { 
		 $w= trim($w);
	     if (strlen($w))
		 {
			//controllo e faccio escape di possibili caratteri speciali
			$special = array('"',"'");
			$w = str_replace($special, "", $w);
			//echo("w: ".$w);
			if (array_key_exists($w, $frequency))
				$frequency[$w]++;
			else
				$frequency[$w]=1;
	     }
	   }
	}
}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>NETT RS</title>
		<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
		<meta name="generator" content="HAPedit 3.1">
		<link rel="stylesheet" href="themes/tagcloud/tagcloud.css" />
		<link rel="stylesheet" href="themes/tagcloud/jqcloud.min.css"/>
		<link rel="stylesheet" href="themes/alertify/themes/alertify.core.css" />
		<link rel="stylesheet" href="themes/alertify/themes/alertify.default.css" />
		
	</head>
	<body>
		<br>
		<div>
			<center>
				<a href="#">
					<img src="themes/img/logo_small.png" alt="" />
				</a>
			</center>
		</div>
		<br>
			<div id="tagcloud" style="height: 280px;"></div>

			<center>
				<div class="controls" style="width: 25%;">
					<img id='loading' src='themes/spinner/ajax-loader.gif'>
					<?php
						if (isset($_POST['parteFinita']))
						{
							// Vuol dire che sto tornando da conclude.php, il tasto back mi deve far tornare al modulo precedente
							$action = "step5.php";
							$value = $_POST['parteFinita'];
						}
						else
						{
							$action = "index.php";
							$value = "stop";
						}

					?>
					<form id="formBack" name="formBack" method="post" action="<?php print $action; ?>" style="float:left;">
						<input name="i_back" id="i_back" value="<?php print $value; ?>" style="display: none;"/>
					</form>
					<form id="formNext" name="formNext" method="post" action="step2.php" style="float:right;"/>					
				</div>
			</center>


			<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
			<script id="_adpacks_projs" type="text/javascript" src="themes/js/pro.js"></script>
			<script>
				var socket = "<?php print $_SESSION['scrivi_socket']; ?>"; 
				var token = "<?php print $data['token']; ?>";

<?php 
	if (has_capability('moodle/course:managefiles', context_system::instance()))
	{
		echo "
				var promise = $.ajax({
						type: \"POST\",
						url: \"getRules.php\",
						data: {
								action: socket,
								token: token
							  }
				});
			";
	}		
?>


				promise.done(function(data){
					var element = document.getElementById("loading");
					element.parentNode.removeChild(element);
					
					
					//console.log(data);
					

					var i = document.createElement("input"); //input element, text
					i.setAttribute('type',"hidden");
					i.setAttribute('name',"rules");
					i.setAttribute('value',data)

					var s = document.createElement("input"); //input element, Submit button
					s.setAttribute('type',"submit");
					s.setAttribute('value', "<?php echo convert_RS('CONTINUE');?>");
					s.setAttribute('class', "btn btn-large btn-primary");
					s.setAttribute('style', "height:40px; width:auto; padding: 5px 5px 5px 5px; font-weight:bold; border-radius: 9px;");
					
					var u = document.createElement("input"); //input element, Undo button
					u.setAttribute('type',"submit");
					u.setAttribute('value', "<?php echo convert_RS('BACK');?>");
					u.setAttribute('class', "btn btn-large btn-primary");
					u.setAttribute('style', "height:40px; width:auto; padding: 5px 5px 5px 5px; font-weight:bold; border-radius: 9px;");
	
					document.formNext.appendChild(i);
					document.formBack.appendChild(u);
					document.formNext.appendChild(s);


				});
			</script>
			
			<!-- TagCloud -->
			<script src="themes/tagcloud/jqcloud.min.js"></script>
			<script type="text/javascript">
				var word_array = [
					<?php
						$i=0;
						foreach ($frequency as $w => $f)
						{
							$f = $f % 5; // devo normalizzare perché le classi sono al massimo 5
							if($i<50)//stampo solo le prime 50 parole nel tagcloud
							{ 
								echo '{text: "'.$w.'", weight: '.$f.'},';
							}
							else
								break;
							$i++;
						}
					?>
				  ];
			</script>
		
			<script>
				$(document).ready(function(){
					// Form di backttracking
					$("#formBack").submit(function(event)
					{
						event.preventDefault(); // cancel submit
						if ($("#formBack")[0].action == "<?php print ($CFG->wwwroot . '/recommender_system/index.php'); ?>")
						{
							alertify.confirm("<font color='#383a3d' style='font-weight:bold; font-size:16px;'><?php echo convert_RS('Going back at this time you will lose all settings.'); echo "<br />"; echo convert_RS('ARE YOU SURE YOU PROCEED?');  ?></font>", function (e) {
								
								
								if (e)
									$("#formBack")[0].submit(); // submit form skipping jQuery bound handler
							});
							
							document.getElementById('alertify-cancel').innerHTML = "<?php echo convert_RS('Cancel');  ?>";
							document.getElementById('alertify-ok').innerHTML = "<?php echo convert_RS('CONTINUE');  ?>";
						}
						else
							$("#formBack")[0].submit();
					});	
					
					
					
					$("#tagcloud").jQCloud(
						word_array, 
						{
							classPattern: null,
						  	colors: [ "#e04908", "#f4a770","#ed6c09", "#e8853a"],
						  	fontSize: {from: 0.06, to: 0.01}
						}
					)
				});
			</script>		
		
		
			<!-- Alertify JS -->
			<script src="themes/alertify/lib/alertify.min.js"></script>
			<script src="themes/js/urchin.js" type="text/javascript"></script>
			<script type="text/javascript">
				_uacct = "UA-783567-1";
				urchinTracker();
			</script> 
		
			

	</body>
</html>

<?php	
	echo $OUTPUT->footer();
?> 
