<?php
	/************************************************************************
	 *	ATTENZIONE:															*
	 *	IN QUESTA PAGINA SI UTILIZZA IN UN PUNTO DEL CODICE IL BACKQUOTE.	*
	 *  E' IMPORTANTE LASCIARE IL BACKQUOTE E NON SOSTITUIRLO, PENA IL		*
	 *  NON FUNZIONAMENTO DELL'ANTEPRIMA DELLE RISORSE.						*
	 ************************************************************************/

	require_once('../config.php');
	require_once($CFG->libdir .'/mysql_conn.php');
	require_once('../metadata/metadata_page/aux_functions.php');
    
	session_start();

	$PAGE->set_pagetype('site-index');
	$PAGE->set_docs_path('');
	$PAGE->set_pagelayout('frontpage');
	//$editing = $PAGE-±>user_is_editing();
	require_login();
	$PAGE->set_context(context_system::instance());
	$PAGE->set_title($SITE->fullname);
	$PAGE->set_heading($SITE->fullname);
	$PAGE->set_url($CFG->wwwroot."/recommender_system/step4");
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

	//----------------------------------------------------------------------------------------


	$partecorrente = $_SESSION['parte_corrente'];

	if (isset($_POST['back']))	
	{
		if(($key = array_search($partecorrente, $_SESSION['parteFinita'])) !== false)
			unset($_SESSION['parteFinita'][$key]);
	}


	if(isset($_POST["keywords"]) && isset($_POST["token"]))
	{
		$keywords = $_POST['keywords'];
		$token = $_POST['token'];
		
		$_SESSION['keywords'] = $keywords;
		$_SESSION['token'] = $token;
		$_SESSION['primavolta'] = 'true';
		
		// Salvataggio delle keywords per il riepilogo generale	
		$keyw = json_decode($keywords, true);
		$_SESSION['summary']['parts'][$partecorrente]['steps']['key'] = array();
		$_SESSION['summary']['parts'][$partecorrente]['steps']['key'] = $_SESSION['keywords_selezionate'];
		foreach ($keyw["keywords"] as $keywords_rimanenti)
			array_push($_SESSION['summary']['parts'][$partecorrente]['steps']['key'], $keywords_rimanenti);
	}
	else
	{
		$keywords = $_SESSION['keywords'];
		$token = $_SESSION['token'];
		$_SESSION['primavolta'] = 'false';
	}


	//se ho riaggiornato la pagina SENZA CAMBIARE LINGUA
	$valuesvar = array();
	if($_SESSION['scrivi_socket'] == 1)
	{	
		if ($keywords == null)
			$message = "{\"doFase\":3}\r\n";
		else
			$message = $keywords . "\r\n";
	
		error_reporting(E_ALL);
		set_time_limit(0);
		ob_implicit_flush();

		$address = '::1';
		$port = 20004;

		if (($sock = socket_create(AF_INET6, SOCK_STREAM, 0)) === false) {
			echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
			exit(0);
		}
		
		socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 30, 'usec' => 0));
		socket_set_option($sock, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 30, 'usec' => 0)); 
		
		socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 30, 'usec' => 0));
		socket_set_option($sock, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 30, 'usec' => 0)); 

		if (socket_connect($sock, $address, $port) === false) {
			echo "socket_connect() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
			exit(0);
		}
		//echo($message);
		if (socket_write($sock, $message, strlen($message)) === false){
			echo "socket_write() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
			exit(0);
		}

		$values = socket_read($sock,200000,PHP_NORMAL_READ);

		$valuesvar = json_decode($values, true);
		$_SESSION['valuesvar'] = $valuesvar;

		socket_close($sock);
		
		
	}
	else
		$valuesvar = $_SESSION['valuesvar'];
	
}

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<link href="themes/multiselect/css/multi-select.css" media="screen" rel="stylesheet" type="text/css">
		<link href="themes/help/help.css" rel="stylesheet" />
		<link rel="stylesheet" href="themes/alertify/themes/alertify.core.css" />
		<link rel="stylesheet" href="themes/alertify/themes/alertify.default.css" />
		<link rel="stylesheet" href="themes/slidedown/slidedown.css" />
		<!--<link rel="stylesheet" href="themes/improve/progressbar.css" />-->
		<link rel="stylesheet" href="themes/improve/progressbarnormale.css" />
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
		
		<div class="progress" style = "margin:0 10% 3% 10%; width:80%;">
			<div class="progress-done"  step = "3" >
			  0
			</div>
		</div>
	
		<center>
			<h1><font color="#383a3d"><?php echo convert_RS('RESOURCES'); ?></font></h1>
			

			<?php
				// Questo file inserisce uno slider con le azioni precedenti...Regole selezionate, keywords...
				include ('step_prec.php'); 

				$token = $valuesvar["token"];
				$resources = $valuesvar["resources"];
			?>
			
				<p>
					<font style="font-weight: bold;"><h2 style = "text-align:left; margin-left:10%; margin-right : 10%;"><?php echo convert_RS('Select your Resources'); ?></h2></font>
					<h3 style = "text-align:left; margin-left:10%; margin-right : 10%;"> <?php echo convert_RS("step4 advice1");?></h3>
				</p>
		
			<form id="myformEnd" name="myformEnd" method="post" action="step5.php"> 
				<table style="width:auto;  margin-bottom:2%;" align="center">
					<tr>
						<td align="center">
							<p><center><h1>
								<?php echo convert_RS('Module Name'); ?>
								<b
									<tr>
										<td align="center">
											<div class="form-group">
												<label for="metadata_list"></label>
													<div class="controls">
														<input type="text" class="input-large" style="font-size:16px; border-radius: 6px; margin-left:3%; margin-bottom:0%;" id="modn" name="modn" placeholder="<?php echo convert_RS('Insert module name'); ?>" value="<?php $title = $_SESSION['course']['parts'][$partecorrente]['name']; if ($title !== 'Part not inserted yet') print $title; ?>">
													</div>
											</div>
										</td>
									</tr>
								</b>
							
							</h1></center></p>
						</td>
					</tr>
					
					
				</table>
  				
				
				<?php
					
					$connection = GetMyConnection();
					
					if ($connection < 0)
					{
						print errorDB($connection);
						die();
					}
										
					$myres = implode(",",$resources);
	
					//echo ("myres: .$myres.");
					//$query = "SELECT DISTINCT res, module, name FROM mdl_course_modules WHERE res IN (" . $myres . ")";//DA SISTEMARE
					//query seleziona: id_risorsa, tipo risorsa (pdf, ecc...), nome risorsa
					
					//mdifiche: devo andare a pescare le risorse dalle tabelle giuste:
					
					$query = "SELECT DISTINCT id, module, instance FROM mdl_course_modules WHERE id IN (" . $myres . ") ORDER BY module";
					
					$result = mysqli_query($connection, $query);
					//print_object($query);
					if ($result)
					{
						$uniqueModules = array();
						$id_risorsa = array();
						$typeModule = array();
						$resInstance = array();
								
						while($tmp = mysqli_fetch_array($result)){
							array_push($id_risorsa, $tmp["id"]);
							array_push($typeModule, $tmp["module"]);
							array_push($resInstance, $tmp["instance"]);						
						}
						
						
						
						$uniqueModules = (array_unique($typeModule));
						//print_object($uniqueModules);
						$tmp = array();
						foreach ($uniqueModules as $id => $record) 
							array_push($tmp, $record);	
						$uniqueModules = $tmp;
						
						//print_object($uniqueModules);
						
						/* for($h = 0; $h < count($uniqueModules); ++$h) {
							echo "<script>console.log('".$uniqueModules[$h]."');</script>";
						}  */
						
						$tmp = array();
						$resultArray = array();
						array_push($tmp, $resInstance[0]);
						for($h = 1; $h < count($typeModule); ++$h) {
							if($typeModule[$h] == $typeModule[$h-1])
								array_push($tmp, $resInstance[$h]);
							else{
								array_push($resultArray, $tmp);
								$tmp = array();
								array_push($tmp, $resInstance[$h]);
							}		
						}
						array_push($resultArray, $tmp);
						//print_object($resultArray);
						
						$tableArray = array();
						
						for($h = 0; $h < count($uniqueModules); ++$h) {
							/*
							switch($uniqueModules[$h])
							{
								case 1: 
									$tabella_file = 'mdl_assign';
									break;
								case 3: 
									$tabella_file = 'mdl_book';	
									break;
								case 4: 
									$tabella_file = 'mdl_chat';
									break;
								case 5: 
									$tabella_file = 'mdl_choice';
									break;
								case 6:
									$tabella_file = 'mdl_data';
									break;
								case 8: 
									$tabella_file = 'mdl_folder';
									break;
								case 9: 
									$tabella_file = 'mdl_forum';
									break;
								case 10: 
									$tabella_file = 'mdl_glossary';
									break;
								case 11: 
									$tabella_file = 'mdl_imscp';
									break;
								case 12: 
									$tabella_file = 'mdl_label';
									break;
								case 13: 
									$tabella_file = 'mdl_lesson';
									break;
								case 14: 
									$tabella_file = 'mdl_lti';
									break;
								case 15: 
									$tabella_file = 'mdl_page';
									break;
								case 16: 
									$tabella_file = 'mdl_quiz';
									break;
								case 17: 
									$tabella_file = 'mdl_resource';
									break;
								case 18: 
									$tabella_file = 'mdl_scorm'; 
									break;
								case 19: 
									$tabella_file = 'mdl_survey';	
									break;
								case 20: 
									$tabella_file = 'mdl_url';
									break;
								case 21: 
									$tabella_file = 'mdl_wiki';
									break;
								case 22: 
									$tabella_file = 'mdl_workshop';
									break;
								default: 
									$tabella_file = 'error';
									//die;
									break;
							}
							*/
							//sostituito lo switch statico con la ricerca del nome tabella direttamente nel DB
							$tabella_file = ($DB->get_record('modules', array('id'=>$uniqueModules[$h])));
							$tabella_file = $tabella_file->name;
							//echo(var_dump($tabella_file));
							$tabella_file = "mdl_".$tabella_file;
							array_push($tableArray, $tabella_file);
						}
						
						//print_object($tableArray);
						
						$resourceName = array();
						$resourceID = array();
						$moduleID = array();
						$nomeTabella = array();
						$urlList = array();
						$urlCounter = 0;
						//query per ottenere id,nome e modulo di appartenenza per ogni risorsa 
						//e nel caso sia di tipo Url, anche l'url a cui indica la risorsaa
						//forse da fare anche per le risorse di tipo Label
						for($h = 0; $h < count($tableArray); ++$h) {
							if($tableArray[$h] != 'mdl_url'){
								$sql = "SELECT mdl_course_modules.id AS id_risorsa, {$tableArray[$h]}.name AS nome_risorsa, mdl_course_modules.module AS module
								FROM mdl_course_modules
								JOIN {$tableArray[$h]}
								ON mdl_course_modules.instance = {$tableArray[$h]}.id
								WHERE {$tableArray[$h]}.id IN (" . implode(",",$resultArray[$h]) . ") AND mdl_course_modules.module = $uniqueModules[$h]";
									
								$queryResult = $DB->get_records_sql($sql);
								//print_object($queryResult); 
							}
							else{
								$sql = "SELECT mdl_course_modules.id AS id_risorsa, {$tableArray[$h]}.name AS nome_risorsa, mdl_course_modules.module AS module, {$tableArray[$h]}.externalurl as url
								FROM mdl_course_modules
								JOIN {$tableArray[$h]}
								ON mdl_course_modules.instance = {$tableArray[$h]}.id
								WHERE {$tableArray[$h]}.id IN (" . implode(",",$resultArray[$h]) . ") AND mdl_course_modules.module = $uniqueModules[$h]";
									
								$queryResult = $DB->get_records_sql($sql);
								//print_object($queryResult); 								
							}
							
							if(!empty($queryResult)){
								//echo "<script>console.log('is alive');</script>";
								foreach ($queryResult as $id => $record) {
									array_push($resourceID, $record->id_risorsa);
									array_push($resourceName, $record->nome_risorsa);		
									array_push($moduleID, $record->module);	
									array_push($nomeTabella, substr($tableArray[$h],4));
									//echo '<br/>';
									//print_r ($record->id_risorsa);
									if($tableArray[$h] == 'mdl_url'){
										array_push($urlList, $record->url);		
									}
								}	
							}
				 			//print_object($queryResult);
							//print_object($nomeTabella);
						}
						//print_object($nomeTabella);
						//print_object($urlList);
						
						echo'<center><select multiple="multiple"  id="callbacks" name="resources[]">';

						//while($row = mysqli_fetch_array($result))
						/* echo "<script>console.log('".count($id_risorsa)."');</script>"; 
						echo "<script>console.log('".count($resourceID)."');</script>";  */
						for($h = 0; $h < count($id_risorsa); ++$h) 
						{								
							$name = $resourceName[$h];
							
							//echo "<script>console.log('$name');</script>"; 
							if($nomeTabella[$h] == 'url'){
								$url = $urlList[$urlCounter];
								$urlCounter++; 
							}
							else{
								$url = $CFG->wwwroot."/mod/$nomeTabella[$h]/view.php?id=$resourceID[$h]";									
							}
							$module = "mdl_".$nomeTabella[$h];
							?>
							<option onmouseover="Tip('<a href=\'javascript:void(0)\' onclick=\'openPreview(`<?php print $url; ?>`,`<?php print $name; ?>`); return false; \'><?php print convert_RS('Resource Preview'); ?> </a>',CLOSEBTN,'true',STICKY,'true',FONTSIZE,'11.5pt',BGCOLOR,'#d1e0e7',
										BORDERCOLOR,'#5392b3',CLOSEBTNCOLORS,'#74A9CC',DELAY,'300',DURATION,'2000',FOLLOWMOUSE,'false',EXCLUSIVE,'true',TITLE,'<?php print $name; ?>')" onmouseout="UnTip()" value="<?php print $resourceID[$h]; ?>;<?php print $name; ?>;<?php print $module; ?>;" id="<?php print $resourceID[$h]; ?>"><?php print $name; ?>
							</option>	
							<?php
						}
									echo'</select>
						</center>';
									
					}
    				else echo 'No results to be visualized';	// end-if
				?>
				<br>		
				<input type="button" value="<?php echo convert_RS('BACK'); ?>" class="btn btn-large" style="height:40px; width:auto; padding: 5px 5px 5px 5px; font-weight:bold; border-radius: 9px;">
				<input type="button" value="<?php echo convert_RS('CONCLUDE'); ?>" class="btn btn-large" style="height:40px; width:auto; padding: 5px 5px 5px 5px; font-weight:bold; border-radius: 9px;">
				<input type="hidden" name="time" value="restore"/>
				<br>
			</center>
		</form>
		<!--<form id="myFormBack" name="myFormBack" method="post" action="step3.php">
			<input type="hidden" name="time" value="restore"/>
			<input type="submit" name="back" id="back" value="<?php echo convert_RS('BACK'); ?>" class="btn btn-large btn-primary" style="height:40px; width:auto; padding: 5px 5px 5px 5px; font-weight:bold; border-radius: 9px;">
		</form>-->
	<br>


	<!-- jQuery library (served from Google) -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	<script src="themes/multiselect/js/jquery.multi-select.js" type="text/javascript"></script>
    <!-- Alertify JS -->
    <script src="themes/alertify/lib/alertify.min.js"></script>
	<script type="text/javascript" src="themes/js/wz_tooltip.js"></script> <!-- Plugin scaricato da http://www.walterzorn.de/en/index.htm -->
	<script type="text/javascript">
		var res_to_info = new Array();
		$('#callbacks').multiSelect({
		  afterSelect: function(values)
			{
		    	var arr = values + "";
	            arr = arr.split(";");
		    	var id = arr[0];
		  },
		});  	
	</script>

	<script type="text/javascript">
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
				
			//ms-callbacks
			//document.getElementById("ms-callbacks").style.width = "80%";
			

			$("#flip").click(function(){
				$("#hideMe").slideToggle("slow");
				$('.arrow-img').toggleClass('slideUp');
				$('.arrow-img').toggleClass('slideDown');
			});
				
			// Seleziono le risorse già selezionate precedentemente
			/*
			<?php
				foreach($_SESSION['resources'] as $res)
				{
					print ("$('#callbacks').multiSelect('select', '$res');");
				}
			?>
			
			*/
				
			var form = document.myformEnd;
			var modn = document.modn;
			var callbacks = document.callbacks;
    		$(form).find("input[type='button']").click(function() {
        		form.operation = this.value;
        		form.action = this.value == '<?php echo convert_RS('CONCLUDE'); ?>' ? 'step5.php' : 'step3.php';
				
				if ($(form).attr("action") != 'step3.php')
				{
					if (!$("#modn").val())
						alertify.alert("<font color='#5392B3' style='font-weight:bold; font-size:16px;'><?php echo convert_RS('PLEASE INSERT MODULE NAME'); ?></font>");
					else if ($("#callbacks").val() == null)
						alertify.alert("<font color='#5392B3' style='font-weight:bold; font-size:16px;'><?php echo convert_RS('PLEASE SELECT SOME RESOURCES'); ?></font>");
					else
						form.submit();
				}
				else
					form.submit();
        		
				return false;
    		});
		});
	</script>
	<script type="text/javascript">
		function openPreview(url, name) {
			window.open(url, "Preview: " + name, "width=500,height=500");
		}
	</script>
	<!-- <div class="container">
		  <ul class = "progressbar">
			<li class = "active"> <?php echo convert_RS('Choose rules'); ?> </li>
			<li class = "active"> <?php echo convert_RS('Filter Keyword'); ?> </li>
			<li class = "active"> <?php echo convert_RS('Modulo & Keyword'); ?> </li>
			<li> <?php echo convert_RS('Continue or create'); ?> </li>
	</div> -->
	
	
	</body>
	<script scr = "progressbarnormale.js"> </script>
</html>

<?php	
	echo $OUTPUT->footer();
?>
