<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

	require_once('../config.php');
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
	$PAGE->set_url($CFG->wwwroot."/recommender_system/step3");
	//$courserenderer = $PAGE->get_renderer('core', 'course');
	
	//echo $OUTPUT->header();	s_t_mod

//if (has_capability('moodle/course:managefiles', context_system::instance()))



	//----------------------------CONTROLLO CAMBIO LINGUA-------------------------------------
	// Verifico se devo chiedere al Server.java di effettuare un restore del dataset

	$corrente = $_SESSION["parte_corrente"];

	if (isset($_POST['time']) && $_POST['time'] === 'restore')
	{
		$address = '::1';
		$port = 20002;
		$token = $_SESSION['data']['token'];
		$message = array('token' => $token, 'doFase' => -6);
		$message = json_encode($message) . "\r\n";		
		
		
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

		if (socket_write($sock, $message, strlen($message)) === false) {
			echo "socket_write() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
			exit(0);
		}

		socket_close($sock);
		
		$_POST['time'] = 'first';
		// unset del salvataggio del riepilogo del modulo corrente
		unset($_SESSION['summary']['parts'][$corrente]['steps']['key']);
	}




	if ($_POST["time"] == "first")
		$_SESSION['seconda_volta'] = 0;
	if (!isset($_SESSION['lingua_t0'])) 
	{
		$_SESSION['lingua_t0'] = current_language();
		$_SESSION['scrivi_socket'] = 1;
	} 
	else 
	{
		$_SESSION['lingua_t-1'] = $_SESSION['lingua_t0'];
		$_SESSION['lingua_t0'] = current_language();

		if ($_SESSION['lingua_t-1'] != $_SESSION['lingua_t0'])
			$_SESSION['scrivi_socket'] = 0;
		else
			$_SESSION['scrivi_socket'] = 1;
	}

	//----------------------------------------------------------------------------------------


	if(isset($_POST["rules"]))
	{
		$rules = $_POST['rules'];
		$time = $_POST['time'];
		$fullrule = $_POST['fullrule'];
		$fullrule = explode("$$##$$", $fullrule);		
		
		
		$_SESSION['rules'] = $rules;
		$_SESSION['time'] = $time;
		
		unset($_SESSION['summary']['parts'][$corrente]['steps']['rules']);
		$_SESSION['summary']['parts'][$corrente]['steps']['rules'] = $fullrule;
	}
	else
	{
		$rules = $_SESSION['rules'];
		
		if (isset($_POST['time']) && ($_POST['time'] == 'next'))
			$time = $_POST['time'];
		else
			$time = $_SESSION['time'];
	}
	

	if ($time == "first") 
	{
		//se ho riaggiornato la pagina SENZA CAMBIARE LINGUA
		if ($_SESSION['scrivi_socket'] == 1 && $_SESSION['seconda_volta'] == 0) 
		{
			$_SESSION['keywords_selezionate'] = array();
			$message = $rules . "\r\n";

			error_reporting(E_ALL);
			set_time_limit(0);
			ob_implicit_flush();

			$address = '::1';
			$port = 20002;

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

			if (socket_write($sock, $message, strlen($message)) === false) {
				echo "socket_write() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
				exit(0);
			}

			$values = socket_read($sock, 200000, PHP_NORMAL_READ);
			$valuesvar = json_decode($values, true);
			$_SESSION['valuesvar'] = $valuesvar;

			socket_close($sock);
		} 
		else
			$valuesvar = $_SESSION['valuesvar'];


		$keywords = $valuesvar['keywords'];
		$list_resource = json_encode($valuesvar['values']);
		$first_value_pos = $valuesvar['values'][0];
		$token = $valuesvar['token'];
	}
	else 
	{
		// echo "seconda volta";
		//se ho riaggiornato la pagina SENZA CAMBIARE LINGUA
		if ($_SESSION['scrivi_socket'] == 1) 
		{
			$_SESSION['seconda_volta'] = 1;
			
			if (isset($_POST))
			{
				$data = $_POST;
				$_SESSION['keywords'] = $data;
			}
			else
				$data = $_SESSION['keywords'];
			
			// Recupero le precedenti keywords selezionate			
			$data_decoded = json_decode($data['keywords'], true);

			$_SESSION['keywords_selezionate'] = $data_decoded['keywords'];
			
			// Salvo nel riepilogo le keywords selezionate + le keywords attuali : Modificato da Hui (03/09)
			
			if($_SESSION['summary']['parts'][$_SESSION["parte_corrente"]]['steps']['key']==null)
				$oldKeywords=array();
			else
				$oldKeywords=$_SESSION['summary']['parts'][$_SESSION["parte_corrente"]]['steps']['key'];
			
			//if(count($_SESSION['keywords_selezionate'])<=0)
			//		$_SESSION['keywords_selezionate']=array();
			
			$_SESSION['summary']['parts'][$_SESSION["parte_corrente"]]['steps']['key'] =array_merge($oldKeywords, $_SESSION['keywords_selezionate']);				
			
			$actualKeywords=$_SESSION['summary']['parts'][$_SESSION["parte_corrente"]]['steps']['key'];
			$data_decoded['keywords']=$actualKeywords;			
				
			$keywords=json_encode($data_decoded,true);
			//$keywords = $data['keywords'];
			//---------------------------------------------------------------------------------------------------
			$message = $keywords . "\r\n";

			error_reporting(E_ALL);
			set_time_limit(0);
			ob_implicit_flush();

			$address = '::1';
			$port = 20003;

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

			if (socket_write($sock, $message, strlen($message)) === false) {
				echo "socket_write() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
				exit(0);
			}

			$values = socket_read($sock, 200000, PHP_NORMAL_READ);
			$valuesvar = json_decode($values, true);
			$_SESSION['valuesvar'] = $valuesvar;

			socket_close($sock);
		} 
		else 
			$valuesvar = $_SESSION['valuesvar'];
			
		
		
		$keywords = $valuesvar['keywords'];
		$list_resource = json_encode($valuesvar['values']);
		$first_value_pos = $valuesvar['values'][0];
		$token = $valuesvar['token'];
		
	}

	// unset del salvataggio del riepilogo del modulo corrente
	//$_SESSION['summary']['parts'][$_SESSION["parte_corrente"]]['steps']['key'] = array();
	

?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<!-- bxSlider CSS file -->
		<link href="themes/jquery/jquery.bxslider.css" rel="stylesheet" />
		<link rel="stylesheet" href="themes/alertify/themes/alertify.core.css" />
		<link rel="stylesheet" href="themes/alertify/themes/alertify.default.css" />
		<link rel="stylesheet" href="themes/slidedown/slidedown.css" />
		<!--<link rel="stylesheet" href="themes/improve/progressbar.css" />-->
		<link rel="stylesheet" href="themes/improve/progressbarnormale.css" />
		
		<link rel="stylesheet" href="themes/nuovostile.css">	<!-- s_t_mod Nuovo Stile -->
		<meta name="viewport" content="width=device-width, initial-scale=1"> <!-- s_t_mod Nuovo Stile -->
		
		<style>
	
		
		
		.htlm, body{
			padding:0;
		}
		
		.topwords h3{
			margin-top:10px;
			margin-left:30px;
			margin-right:30px;
			margin-bottom:10px;
		}
		</style>
		
	</head>
	<body style="font-family:Arial, Helvetica, sans-serif;"> 
		<center>
			<img src="themes/img/logo_small.png" alt="" class="responsive" style="width:300px; margin:5% 0% 2% 0%;"/>	<!-- s_t_mod Nuovo Stile aggiunta width e class-->
		<!--<br>
		<div><center><a href="#"><img src="themes/img/logo_small.png" alt="" /></a></center></div>		
		<br>-->
		
		<!-- s_t_mod Chiara -->
		<!-- <div class="progress" style = "margin:0 20% 0 20%; width:60%;">-->
		</center>

		<div class="progress"  style="margin:0% 25% 3% 25%; width:50%;"> <!-- s_t_mod Nuovo Stile-->
			<div class="progress-done"  step="2">
			  50%
			</div>
		</div>
		
		<center>
		<div style="margin:0% 15%; align-content:center;">	<!-- s_t_mod Nuovo Stile -->
		<!-- s_t_mod Chiara -->
			<!--<h2 style="text-align:left; margin-left: 20%; margin-right:20%;"><font color="#383a3d">-->
			<h1 style="text-align:center; margin:0%;"> <!-- s_t_mod Nuovo Stile: -->
				<font color="#383a3d"><?php echo convert_RS('KEYWORDS'); ?></font>
			</h1>
			
			<!-- s_t_mod Nuovo Stile: -->
			<div class = "topwords">
				<h5 style="text-align:center; font-size: 100%; font-weight:normal;"><?php echo convert_RS('step3 advice'); ?> </h5>
			</div>
			
			
			<?php 
				// Questo file inserisce uno slider con le azioni precedenti...Regole selezionate, keywords...
				include ('step_prec.php'); 
			?>
			
			<!--
			<div id="risposta">
				<h2><font color="#383a3d"><//?php echo convert_RS('Number of resources:'); ?></font></h2>
				<font style="font-weight:bold;"><h2><p class="ka"><//?php echo $first_value_pos; ?></p></h2></font>
			</div>
			
			-->

			
			<!--<div id="kwds" style='	width:56%;
				 text-align:left;
				 -moz-box-shadow: 0 0 5px #ccc;
				 -webkit-box-shadow: 0 0 5px #ccc;
				 box-shadow: 0 0 5px #ccc;
				 border:  2px solid #fff;
				 left: -5px;
				 background: #e0dddb;
				 padding-top: 15px;
				 padding-left: 45px;'>
			</div>-->
			<!-- s_t_mod chiara: ora il box non ha dimensione fissa ma si adatta al contenuto -->
			<div id="kwds" style='	
				 display: inline-block;
				 text-align:left;
				 -moz-box-shadow: 0 0 5px #ccc;
				 -webkit-box-shadow: 0 0 5px #ccc;
				 box-shadow: 0 0 5px #ccc;
				 border:  2px solid #fff;
				 left: -5px;
				 background: #e0dddb;
				 padding: 15px 38px 15px 35px; '>
			</div>

		<!--	<?php
				if ($_SESSION['seconda_volta'] == 1 && !empty( $_SESSION['keywords_selezionate'] )) {
					echo '
							<div style="display: inline-block">
								 <h3><font color="#383a3d">' . convert_RS('Keywords previously selected') . ':</font></h3>
						';
					foreach ($_SESSION['keywords_selezionate'] as $k => $v) { 
						echo '<div id="'.$v.'"> 
								<p>'.$v.'</p>								
							  </div>';
					}
					echo '
							</div>
						';
				}
			?>
			-->
			<br><br>	
		
		<!--div>
				<form id="myformMore" name="myformMore" method="post" action="step3.php"><input type="hidden" id="keymore" name="keywords" value=""/>
					<p align="center" id="loading" style="visibility:hidden; margin-top:-20px;">
						<font color="red" style="display:block;"><strong><-?php print convert_RS("The operation in progress may take some time. Don't reload the page!"); ?></strong></font>
						<img src="themes/spinner/ajax-loader.gif" >						
					</p>
					<input type="hidden" id="time" name="time" value="next"/>
					<input type="submit" id="moreK" value="<-?php print convert_RS("More Keywords"); ?>" class="btn btn-large" style="height:30px; width:auto; padding: 5px 5px 5px 5px; font-weight:bold; border-radius: 9px;"/>
					<input type="button" id="restoreK" value="<-?php print convert_RS("Restore Keywords"); ?>" class="btn btn-large" style="height:30px; width:auto; padding: 5px 5px 5px 5px; font-weight:bold; border-radius: 9px;" onclick="restoreKey()"/>
				</form>
				<br>
				<form id="myform" name="myform" method="post" action="step4.php"><input type="hidden" id="keystop" name="keywords" value=""/>
					<input type="hidden" name="token" value="<-?php echo $token; ?>"/>
					<input type="button" name="back" id="back" value="<-?php echo convert_RS('BACK'); ?>" class="btn btn-large btn-primary" style="height:40px; width:auto; padding: 5px 5px 5px 5px; font-weight:bold; border-radius: 9px;" onclick="location.href='<?php echo ($CFG->wwwroot . '/recommender_system/step2.php'); ?> '"/>
					<input type="submit" value="<-?php echo convert_RS('CONTINUE'); ?>" class="btn btn-large" style="height:40px; width:auto; padding: 5px 5px 5px 5px; font-weight:bold; border-radius: 9px;"/>
				</form>
			</div>
			<br>
		</center-->
		
		<div>
				<form id="myformMore" name="myformMore" method="post" action="step3.php"><input type="hidden" id="keymore" name="keywords" value=""/>
					<p align="center" id="loading" style="visibility:hidden; margin-top:-20px;">
						<font color="red" style="display:block;"><strong><?php print convert_RS("The operation in progress may take some time. Don't reload the page!"); ?></strong></font>
						<img src="themes/spinner/ajax-loader.gif" >						
					</p>

					<input type="hidden" id="time" name="time" value="next"/>
					<input type="submit" id="moreK" value="<?php print convert_RS("More Keywords"); ?>" class="btn btn-large" style="height:40px; width:auto; padding: 5px 5px 5px 5px; font-weight:bold; border-radius: 9px; margin-right:5%;"/>
					<input type="button" id="restoreK" value="<?php print convert_RS("Restore Keywords"); ?>" class="btn btn-large" style="height:40px; width:auto; padding: 5px 5px 5px 5px; font-weight:bold; border-radius: 9px; margin-left:3%;" onclick="restoreKey()"/>
				</form>
				<br>
				
				<center>
					<div id="risposta">
						<!-- s_t_mod Chiara: -->
						<!--<h4>-->
						<h4 style="margin-bottom:5%;"><?php echo convert_RS('Number of resources:'); ?> <b class="ka" id="ka"><?php echo $first_value_pos; ?></b></h4>
					
					</div>
				</center>
				
				
				<form method="post" action="<?php echo ($CFG->wwwroot . '/recommender_system/step2.php'); ?>" style="display: inline;">
					<!-- s_t_mod Nuovo Stile: nuova class -->
					<input type="submit" name="back" id="back" value="<?php echo convert_RS('BACK'); ?>" class="btn-nuovostile-back">
				</form>
				
				<button type="button" onclick="$('#i_back').trigger('click')" class="btn-nuovostile-home">Home</button>	<!--S_t_mod Hui + Nuovo Stile-->
				
				<form id="myform" name="myform" method="post" action="step4.php" style="display: inline"><input type="hidden" id="keystop" name="keywords" value=""/>
					<input type="hidden" name="token" value="<?php echo $token; ?>"/>
					<!-- s_t_mod Nuovo Stile: nuova class -->
					<input type="submit" value="<?php echo convert_RS('CONTINUE'); ?>" class="btn-nuovostile-cont"/>
				</form>
				
				<!--Modificato da Hui(02/09)
					form usato per tornare alla home chiudendo la session attuale-->
					
				<form id="myformBack" name="myformBack" action="<?php echo ($CFG->wwwroot . '/recommender_system/index.php'); ?>" style="display:inline;" method="post">
					<input type="submit" name="i_back" id="i_back" value="stop" style="display: none;"/>
				</form>
				
				
			</div>
			<br>
			</div>
		</center>
		
		

	
		<!-- jQuery library (served from Google) -->
		<!--script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script-->
		<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

		<!-- bxSlider Javascript file -->
		<script src="themes/jquery/jquery.bxslider.min.js"></script>
		<!-- Alertify JS -->
		<script src="themes/alertify/lib/alertify.min.js"></script>
	
		<script>
			$(document).ready(function () {
				//progressbar
				const progress = document.querySelector('.progress-done');
				
				var nop = parseInt("<?php echo $_SESSION["data"]["nop"]; ?>", 10);
				var currentpart =  parseInt("<?php echo $_SESSION["parte_corrente"]; ?>" , 10);
				var step = parseInt(progress.getAttribute('step'), 10);
				var parte = ((currentpart -1) * 4) + step;
				var percentuale = (((parte / (nop *4)) * 100).toPrecision(2) + '%');
				

				progress.style.opacity = 1;
				progress.style.width = percentuale;
				progress.innerHTML =  percentuale;
				
				//s_t_mod Hui 09/09 :				
				//Controllare che il numero di risorsa attuale non è 0 prima di caricare altre keyword
				$("#moreK").click(function(){
					event.preventDefault();
					
					var resourceNumber=parseInt(document.getElementById('ka').textContent);
					
					if (resourceNumber==0)
						alertify.alert("<?php echo convert_RS('No resources found'); ?>");
					else
						$("#myformMore")[0].submit();
					
				});
				//------------------------------------------------------
				
				$("#flip").click(function(){
					$("#hideMe").slideToggle("slow");
					$('.arrow-img').toggleClass('slideUp');
					$('.arrow-img').toggleClass('slideDown');
				});
				
				
				$("#myformMore").submit(function(event)
				{
					hideElement();
				});

				
				//Modificato da Hui (09/09)
				$('.bxslider').bxSlider();
				
				// Form di invio delle keyword
				$("#myform").submit(function(event)
				{
					event.preventDefault(); // cancel submit

					/*alertify.confirm("<font color='#5392B3' style='font-weight:bold; font-size:16px;'><?php echo convert_RS('Due to updates will not be possible go back.'); echo "<br />"; echo convert_RS('ARE YOU SURE YOU PROCEED?');  ?></font>", function (e) {
						if (e)
							$("#myform")[0].submit(); // submit form skipping jQuery bound handler
					});*/
					var resourceNumber=parseInt(document.getElementById('ka').textContent);
					
					if (resourceNumber==0)
						alertify.alert("<?php echo convert_RS('No resources found'); ?>");
					else
						$("#myform")[0].submit();
					
				});	
				
				
				<?php 
					if ($keywords == null && $_SESSION['seconda_volta']==0){	
						echo 'alertify.confirm("<font color=\'#5392B3\' style=\'font-weight:bold; font-size:16px;\'>'.convert_RS("No keywords found").'<br />'.convert_RS("Go to previous step or back to home").'</font>").set("labels", {ok : "Back", cancel : "Home"}); ';
					}
					
					else if($keywords == null && $actualKeywords==null)
						echo 'alertify.confirm("'.convert_RS("keywords exhausted").'<br />'.convert_RS("Go to previous step or back to home").'").set("labels", {ok : "Back", cancel : "Home"}); ';
					
						
				?>
				document.getElementById('alertify-ok').innerHTML = "<?php echo convert_RS('BACK');?>";
				document.getElementById('alertify-ok').onclick = function(){$("#back").trigger('click')};
				document.getElementById('alertify-cancel').onclick = function(){$("#i_back").trigger('click')};
				document.getElementById('alertify-cancel').innerHTML = "Home";
			});
			//---------------------------------------------------------------------------------------------------------------------------------------------
			
			function restoreKey() {
				// Cambio valore e invio la form
				document.getElementById("time").setAttribute("value", "restore");
				hideElement();
				$("#myformMore")[0].submit();
			}
			
			function hideElement() {
				// Nascondo gli elementi
				$("#myform")[0].style.visibility = "hidden";
				$("#moreK")[0].style.visibility = "hidden";
				$("#restoreK")[0].style.visibility = "hidden";

				// Mostro lo spinner
				$("#loading")[0].style.visibility = "visible";
			}
			
			
			var token = <?php echo '"' . $token . '"'; ?>;
			window.tok = token;
			var keywords = <?php echo json_encode($keywords); ?>;
			var values = <?php echo json_encode($list_resource); ?>;

			//console.log(keywords);
			//console.log(values);

			window.listResource = values;
			var lung = keywords.length;

			var array_pos = new Array();
			for (i = 0; i < lung; i++) {
				array_pos[i] = 0;
			}
			window.myPosition = array_pos;

			//console.log(lung);
			window.lung = lung;
			//console.log(window.lung);

			//-------------------------------------Costruzione dinamica dello slider--------------------------------------------------
			var num_rules = keywords.length;
			var num_schermate;

			if (num_rules % 5 > 0) {
				num_schermate = (Math.floor(num_rules / 5)) + 1;
			}
			else if (num_rules % 5 == 0) {
				num_schermate = num_rules / 5;
			}
			else {
				document.write('No rules returned');
			}

			//console.log(num_schermate);
			
			var j = 0;
			var i = 0;
			var bool = 0; //bool usato per uscire dal secondo while e tornare al primo
			var bool2 = 0; //bool 2 usato per controllare se sono già entrato nell'if con j=10, 20, 30 ecc...
			while (i < num_schermate) 
			{
				bool = 0;
				var para = document.createElement("p");
				
				while (bool == 0 && j < keywords.length) 
				{
					if (j % 5 == 0 && j != 0 && bool2 == 0) 
					{
						bool = 1;
						bool2 = 1;
					}
					else 
					{
						/*var para_p = document.createElement("p");
						para_p.innerHTML = '<input type="checkbox" id="' + j + '" name="' + lung + '" value="' + keywords[j] + '" onchange="updater(this, ' + j + ');"/>' + keywords[j] + ' <br>';
						console.log(para_p);
						console.log(window.myPosition);
						var node = document.createTextNode(" ");
						para_p.appendChild(node);
						para.appendChild(para_p);*/
						putElement(j, lung, keywords[j]);
						j++;
						bool2 = 0;
					}
				}
				var element = document.getElementById("kwds");
				
				element.appendChild(para);
				i++;
			}
			
			var lung = <?php echo count($keywords); ?>;
			var listResource = <?php echo $list_resource; ?>;
			var tok = <?php echo '"' . $token . '"'; ?>;


			console.log(lung);
			console.log(listResource);

			var array_pos = new Array();
			for (i = 0; i < lung; i++) {
				array_pos[i] = 0;
			}

			window.myPosition = array_pos;

			var myKeywords = [];
		
			var obj = {
				token: tok,
				action: 'more',
				keywords: myKeywords,
				doFase: 6
			};
			result = JSON.stringify(obj);
			document.getElementById('myformMore').firstChild.setAttribute("value", result);

			var obj1 = {
				token: tok,
				action: 'stop',
				keywords: myKeywords,
				doFase: 3
			};
			result = JSON.stringify(obj1);
			document.getElementById('myform').firstChild.setAttribute("value", result);

			
			function putElement(id, name, value) {
				var para_p = document.createElement("p");
				console.log(name);
				//s_t_mod Chiara : modificato stile checkbox
				//para_p.innerHTML = '<input type="checkbox" id="' + id + '" name="' + name + '" value="' + value + '" onchange="updater(this, ' + id + ');"/>' + value + ' <br>';
				para_p.innerHTML = '<input type="checkbox" style="margin-right:10px;;" id="' + id + '" name="' + name + '" value="' + value + '" onchange="updater(this, ' + id + ');"/>' + value + ' <br>';
				console.log(para_p);
				var node = document.createTextNode(" ");
				para_p.appendChild(node);
				para.appendChild(para_p);
			}
			

			function updater(feature, j) {
				// ---------------------------------------Check---------------------------------------------------------------------------
				var result;
				if (feature.checked == true) {
					myPosition[(array_pos.length-1)-j] = 1;
					myKeywords.push(feature.value);
					console.log(myPosition);
					console.log(myKeywords);
					var risp = listResource[toInteger()];
					$("#risposta .ka").text(risp);


					var obj = {
						token: tok,
						action: 'more',
						keywords: myKeywords,
						doFase: 6
					};
					result = JSON.stringify(obj);
					document.getElementById('myformMore').firstChild.setAttribute("value", result);
					
					var obj1 = {
						token: tok,
						action: 'stop',
						keywords: myKeywords,
						doFase: 3
					};
					result = JSON.stringify(obj1);
					document.getElementById('myform').firstChild.setAttribute("value", result);

				}

				// ------------------------------------------------------Uncheck---------------------------------------------------------------
				else if (feature.checked == false) {
					myPosition[(array_pos.length-1)-j] = 0;
					var index = myKeywords.indexOf(feature.value);
					if (index > -1) {
						myKeywords.splice(index, 1);
					}
					console.log(myPosition);
					console.log(myKeywords);
					var risp = listResource[toInteger()];

					$("#risposta .ka").text(risp);

					var obj = {
						token: tok,
						action: 'more',
						keywords: myKeywords,
						doFase: 6
					};
					result = JSON.stringify(obj);
					//alert(result);
					document.getElementById('myformMore').firstChild.setAttribute("value", result);
					var obj1 = {
						token: tok,
						action: 'stop',
						keywords: myKeywords,
						doFase: 3
					};
					result = JSON.stringify(obj1);
					document.getElementById('myform').firstChild.setAttribute("value", result);
					//alert(result);
				}
				
				console.log(result);
				console.log(myKeywords);

			}
			function toInteger() {
				var arr_string = myPosition.toString();
				arr_string = arr_string.replace(/,/g, "");
				var val = parseInt(arr_string, 2);

				return val;
			}
			// ------------------------------------------------------Delete---------------------------------------------------------------
			/*function deleteKeyword(feature) {
				var index = inArray(feature, myKeywords);

				if (index > -1) {
					myKeywords.splice(index, 1);
					putElement(j++, feature, feature);
					document.getElementById(feature).style.display = "none";
				}

				var obj = {
					token: tok,
					action: 'more',
					keywords: myKeywords,
					doFase: 6
				};
				result = JSON.stringify(obj);

				document.getElementById('myformMore').firstChild.setAttribute("value", result);
				var obj1 = {
					token: tok,
					action: 'stop',
					keywords: myKeywords,
					doFase: 3
				};
				result = JSON.stringify(obj1);
				document.getElementById('myform').firstChild.setAttribute("value", result);

				console.log(result);
				console.log(myKeywords);
			}
			function inArray(element, array) {
				var length = array.length;
				for(var i = 0; i < length; i++) 
				{
					if(array[i] == element) 
						return i;
				}
				return -1;
			}*/
		</script>
		
		<!-- <div class="container">
		  <ul class = "progressbar">
			<li class = "active"> <//?php echo convert_RS('Choose rules'); ?> </li>
			<li class = "active"> <//?php echo convert_RS('Filter Keyword'); ?> </li>
			<li> <//?php echo convert_RS('Modulo & Keyword'); ?> </li>
			<li> <//?php echo convert_RS('Continue or create'); ?> </li>
	</div> -->
	
	
	</body>
	<script scr = "progressbarnormale.js"> </script>
</html>

<?php
	//echo $OUTPUT->footer();	s_t_mod
?>
