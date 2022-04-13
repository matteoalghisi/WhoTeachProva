<?php
	require_once('../config.php');
	require_once('../metadata/metadata_page/aux_functions.php');
	require_once('functions.php');

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
	
	//echo $OUTPUT->header();	s_t_mod

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

	$doFase = 2; // Questo numero è un ID che identifica la fase che deve fare il server Java
	//----------------------------------------------------------------------------------------
	
	if($_SESSION['scrivi_socket'] == 1 && isset($_POST["rules"])){
		$reg = $_POST["rules"];
		$_SESSION['reg'] = $reg;
	}
	else
		$reg = $_SESSION['reg'];


	$regole = json_decode($reg, true);
	//debug
	//echo(var_dump($regole));
	//
	$tok = $regole["token"];
	$regole = $regole["rules"];
	
	// unset del salvataggio del riepilogo del modulo corrente
	unset($_SESSION['summary']['parts'][$_SESSION["parte_corrente"]]['steps']);
}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<!-- bxSlider CSS file -->
		<link href="themes/jquery/jquery.bxslider.css" rel="stylesheet" />
		<link rel="stylesheet" href="themes/alertify/themes/alertify.core.css" />
		<link rel="stylesheet" href="themes/alertify/themes/alertify.default.css" />
		<!-- <link rel="stylesheet" href="themes/improve/progressbar.css" /> -->
		<link rel="stylesheet" href="themes/improve/progressbarnormale.css" />
		<link rel="stylesheet" href="themes/nuovostile.css">	<!-- s_t_mod Nuovo Stile -->
		
		<meta name="viewport" content="width=device-width, initial-scale=1"> <!-- s_t_mod Nuovo Stile -->
	
	<style>
			.bx-wrapper .bx-viewport{background-color:#e0dddb;}
	</style>
	</head>
	
	<body style="font-family:Arial, Helvetica, sans-serif;"> 
		<center>
		<img src="themes/img/logo_small.png" alt="" class="responsive" style="width:300px; margin:5% 0% 2% 0%;"/>	<!-- s_t_mod Nuovo Stile aggiunta width e class-->
		<!-- <div id="grandparent" style="width:70%;">	s_t_mod Nuovo Stile 
			<br>
			<a href="#">
				<img src="themes/img/logo_small.png" alt="" style="width:35%;"/>	<!-- s_t_mod Nuovo Stile aggiunta width
			</a>
		</div>-->			
		</center>
			
			<br>
			<!-- s_t_mod Chiara -->
			<!--<div class="progress" style="margin : 0 0 0 0;">-->
			<div class="progress"  style="margin:0% 25% 3% 25%; width:50%;"> <!-- s_t_mod Nuovo Stile-->
			<div class="progress-done"  step = "1">
				25%
			</div>
			</div>
			
		<center>
			<!-- <div id="parent" style="width:70%; align-content:center; border:1px solid green;">	s_t_mod Nuovo Stile -->
			<div style="margin:0% 15%; align-content:center;">	<!-- s_t_mod Nuovo Stile -->
				
				
				<!--<h2 style="text-align:left;margin-bottom:3%">-->
				<h1 style="text-align:center; margin:0%;"> <!-- s_t_mod Nuovo Stile: -->
					<font color="#383a3d">
						<!-- 
						<?php echo convert_RS('RULES')." ".(convert_RS('FOR'))."</br>".strtoupper(convert_RS($_SESSION['ka'])); ?> -->
						<?php echo convert_RS('Select rules');?>
					</font>
				</h1>
				
				
				<!-- s_t_mod <p>
					<strong>
						<font color="#383a3d">
						<h5 style="text-align:left; margin-bottom:3%">
							<!-- (era già stato commentato) <//?php echo convert_RS('Two or more selected rules are interpreted as the conjunction of them'); ?> -->
							<!--<//?php echo convert_RS('step2 advice') ?>
						</h5>
						</font>
					</strong>
				</p>-->
				
				<!-- s_t_mod Nuovo Stile: -->
				<div class = "topwords">
					<h5 style="text-align:center; font-size: 100%; font-weight: normal;"><?php echo convert_RS('step2 advice') ?> </h5>
				</div>
				
				
				<!-- <p>
					<font style="font-weight:bold;">
					<h2>
						<//?php echo convert_RS('Number of Modules:'); ?>
					</h2>
					</font>
				</p>	
				<h2>
				<p id="result">0</p>
				</h2>
				-->
				
				<!-- s_t_mod aggiuto da Chiara:-->
				
				
			

			<?php
				$data = $_SESSION["data"];
				$data["doFase"] = $doFase;
				$ruleXpage=$data["ruleXpage"];
				$num_rules = count($regole);
				
				//echo $num_rules;
				
				$num_schermate;
				
				//se non ci sono regole skippo questa fase e vado alla prossima
				if ($num_rules == 0 && !(isset($_POST['back']))){
					echo('No rules returned');
				}
				else{
					if($num_rules%$ruleXpage > 0)
						$num_schermate = ( floor($num_rules/$ruleXpage) )+1;
					else if($num_rules%$ruleXpage == 0)
						$num_schermate = $num_rules/$ruleXpage;
					else
						echo('error');
				}

				//echo '<ul class="bxslider" style="text-align:left; padding-top: 10px; margin-left: 25px;">';
				echo '<ul class="bxslider" style="text-align:left; margin:0px; padding-top:10px; margin-bottom:100px;">'; //s_t_mod Nuovo Stile
					$j=0;
					$i=0;
					$bool = 0; //bool usato per uscire dal secondo while e tornare al primo
					$bool2 = 0; //bool 2 usato per controllare se sono già entrato nell'if con j=10, 20, 30 ecc...
					while($i<$num_schermate)
					{
						$bool = 0;
						echo "<li align='left' style='padding:5px 0px;'>";

						while($bool == 0 && $j<$num_rules)
						{
							if($j%5 == 0 && $j!=0 && $bool2 == 0)
							{
								$bool = 1;
								$bool2 = 1;
							}
							else
							{
								$rule = $regole[$j];
								$myModules = implode(",",$rule["modules"]);
								$newrule = rulesInterpreter($rule['rule']);
								$newrule = str_ireplace("&",",",$newrule);	//Modificato da Hui(02/09): sostituito <br/> con ,
								
								//s_t_mod Nuovo Stile: modifica CSS $newrule e checkbox:
								$newrule = "<div style='margin-left:80px; margin-top:-18px;'>".$newrule."</div>";							
								//echo "<p style='float:left; height: 40px; margin:5px;'><input style='margin-left:5px;' type=\"checkbox\" id=\"".$rule['IDrule']."\" value=\"" . $myModules . "\" name=\"" .$tok. "\" onchange=\"update(this);\"/></p>".$newrule."<br>";
								echo "<p style='margin-left:50px; display:inline;'>
										<input type=\"checkbox\" id=\"".$rule['IDrule']."\" value=\"" . $myModules . "\" name=\"" .$tok. "\" onchange=\"update(this);\"/>
									</p>".$newrule."<br>";
								print "<input type=\"hidden\" id=\"dir_".$rule['IDrule']."\" value=\"" .$newrule. "\">";
								
								$j++;
								$bool2 = 0;
							}
						}

						echo '</li>';
						$i++;
					}
				echo '</ul>';
			?>

			
			<br>
		
			<!--div-->
			<?php
				if (isset($_SESSION['parteFinita']) && count($_SESSION['parteFinita']) > 0)
				{
					// Vuol dire che ho concluso un modulo, devo poter tornare a quello per modificarlo
					$action = "step5.php";
					$value = end($_SESSION['parteFinita']);
				}
				else
				{
					$action = "index.php";
					$value = "stop";
				}
				
				

			?>
			
			<div>
			
			<!-- s_t_mod Chiara: -->
			<!--<p>
					<font style="font-weight:bold;">
						<h5>
							<//?php echo convert_RS('Number of Modules:') ?>
								<b id="result">0</b>
						</h5>
					</font>
				</p>-->
			<div>
				<h4 style="margin-bottom:5%;visibility: hidden;" id="number_of_modules" ><?php echo convert_RS('Number of Modules:') ?> <b id="result">0</b></h4>
			</div>
						
								
					
				
			</div>
			
			<!-- s_t_mod Nuovo Stile -->
			<!--<form id="myformBack" name="myformBack" action="<//?php print $action; ?>" style="display:inline;" method="post">
				<input name="i_back" id="i_back" value="<//?php print $value; ?>" style="display: none;"/>
				<input type="submit" name="back" id="back" value="<//?php echo convert_RS('BACK'); ?>" class="btn btn-large btn-primary" style="height:40px; width:auto; padding: 5px 5px 5px 5px; font-weight:bold; border-radius: 9px; float:left;" >
			</form>
			
			<form id="myform" name="myform" method="post" action="step3.php" style="display:inline;">
				<input type="hidden" name="rules" id="rules" value='{"token":"<//?php echo $tok ?>","rules":[],"doFase":"<//?php echo $doFase?>"}'/>
				<input type="hidden" name="time" value="first"/>
				<input type="hidden" name="fullrule" id="fullrule" value=""/>
				<input type="submit" name="continue" id="continue" value="<//?php echo convert_RS('CONTINUE'); ?>" class="btn btn-large btn-primary" style="height:40px; width:auto; padding: 5px 5px 5px 5px; font-weight:bold; border-radius: 9px; float:right;" >
				<p align="center">
					<img id='loading' src='themes/spinner/ajax-loader.gif' style='visibility:hidden;'>
				</p>
			</form>-->
			<form id="myformBack" name="myformBack" action="<?php print $action; ?>" style="display:inline;" method="post">
				<input name="i_back" id="i_back" value="<?php print $value; ?>" style="display: none;"/>
				<input type="submit" name="back" id="back" value="<?php echo convert_RS('BACK'); ?>" class="btn-nuovostile-back"/> 
			</form>
			
			<button type="button" id="home_button" class="btn-nuovostile-home">Home</button>	<!--S_t_mod Hui onclick="$('#i_back').val('stop');$('#back').trigger('click')"-->
			
			<form id="myform" name="myform" method="post" action="step3.php" style="display:inline;">
				<input type="hidden" name="rules" id="rules" value='{"token":"<?php echo $tok ?>","rules":[],"doFase":"<?php echo $doFase?>"}'/>
				<input type="hidden" name="time" value="first"/>
				<input type="hidden" name="fullrule" id="fullrule" value=""/>
				<input type="submit" name="continue" id="continue" value="<?php echo convert_RS('CONTINUE'); ?>" class="btn-nuovostile-cont"/> 
				<p align="center">
					<img id='loading' src='themes/spinner/ajax-loader.gif' style='visibility:hidden;'>
				</p>
			</form>
			
			
		<!--/div-->
		<br>
		
		
		
			<!-- jQuery library (served from Google) -->
			<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
			<!-- bxSlider Javascript file -->
			<script src="themes/jquery/jquery.bxslider.min.js"></script>
			<!-- Alertify JS -->
			<script src="themes/alertify/lib/alertify.min.js"></script>
			
			
			<script>
				$(document).ready(function(){

				//progressbar, suggerisco di metterlo in uno script e importarlo
					const progress = document.querySelector('.progress-done');
					
					var nop = parseInt("<?php echo $_SESSION["data"]["nop"]; ?>", 10);
					var currentpart =  parseInt("<?php echo $_SESSION["parte_corrente"]; ?>" , 10);
					var step = parseInt(progress.getAttribute('step'), 10);
					var parte = ((currentpart -1) * 4) + step;
					var percentuale = (((parte / (nop *4)) * 100).toPrecision(2) + '%');

					progress.style.opacity = 1;
					progress.style.width = percentuale;
					progress.innerHTML =  percentuale;
				
				
					$('.bxslider').bxSlider();
					
					<?php
						if($num_rules == 0 && !(isset($_POST['back'])))
							echo "skipStep();";
					?>

					// Form di invio delle regole selezionate
					$("#myform").submit(function(event)
					{
						event.preventDefault(); // cancel submit
						
						if( $("#rules").val() == '{"token":"<?php echo $tok ?>","rules":[],"doFase":"<?php echo $doFase?>"}' && <?php if($num_rules != 0) echo "true"; else echo "false"; ?>)
							$("#myform")[0].submit(); // submit form skipping jQuery bound handler	
							//alertify.alert("<font color='#5392B3' style='font-weight:bold; font-size:16px;'><?php echo convert_RS('PLEASE SELECT SOME RULES'); ?></font>");
							
						
						else
						{
							// Prendo il valore della regola selezionata e lo scrivo nel form di invio				
							var fullrule = document.getElementById("fullrule");
							var output = "";
							for (i = 0; i < array_id.length; i++)
							{
								var id = "dir_" + array_id[i];
								var elements = document.getElementById(id).value;
								output += elements + "$$##$$";

							}
							fullrule.value = output;

							// Faccio apparire lo spinner
							$("#continue")[0].style.visibility = "hidden";
							$("#back")[0].style.visibility = "hidden";
							$("#loading")[0].style.visibility = "visible";

							$("#myform")[0].submit(); // submit form skipping jQuery bound handler							
						}
					});	
					
					// Form di backttracking
					$("#myformBack").submit(function(event)
					{
						event.preventDefault(); // cancel submit
						if ($("#myformBack")[0].action == "<?php print ($CFG->wwwroot . '/recommender_system/index.php'); ?>")
						{
							alertify.confirm("<font color='#5392B3' style='font-weight:bold; font-size:16px;'><?php echo convert_RS('Going back at this time you will lose all settings.'); echo "<br />"; echo convert_RS('ARE YOU SURE YOU PROCEED?');  ?></font>", function (e) {
								if (e)
									$("#myformBack")[0].submit(); // submit form skipping jQuery bound handler
							});
						}
						else
							$("#myformBack")[0].submit();
					});	
					
					
					//s_t_mod Hui: 
					//Disattivo l'evento onsubmit collegato al form myformBack per evitare che esce il msg di conferma
					$("#home_button").click(function(){
						$("#i_back").val('stop');
						$("#myformBack" ).unbind();
						$("#myformBack")[0].submit();
	
					});
				});
				
				
				///mia funzione per quando nn ci sono regole
				function skipStep(){
					alertify.alert("<font color='#383a3d' style='font-weight:bold; font-size:16px;'><?php echo convert_RS('No rule'); ?> </font>").set('labels', 'prosegui');
					document.getElementById('alertify-ok').onclick = function(){$("#continue").trigger('click')}
					document.getElementById('alertify-ok').innerHTML = "<?php echo convert_RS('CONTINUA'); ?>";
				}

				var array_modules = new Array();     //conterrà tutti i moduli (con duplicati) delle regole scelte dall'utente [112,112,13,44...]
				var array_id = new Array();          //conterrà tutti gli IDrule delle regole selezionate. Utile per tenere traccia degli IDrule da inviare al RS
				var num_check = 0;
				var token;

				function update(feature) {
					token = feature.name;
					
					
					// ---------------------------------------Check---------------------------------------------------------------------------
					if(feature.checked == true){
						num_check++;  //Numero volte che l'utente EFFETTUA un check
						var str = feature.value.split(",");
						//console.log(str);
						//console.log(num_check);
						for(i=0; i<str.length; i++){
							array_modules.push(str[i]);
						}

						array_id.push(feature.id);
						array_modules.sort();

						var map = {};       //mappa chiave valore che contiene key=numero modulo e value=occorrenze modulo nell'array array_modules
						var current = null;
						var cnt = 0;
						for (var i = 0; i < array_modules.length; i++) {
							if (array_modules[i] != current) {
								if (cnt > 0) {
									map[current] = cnt;								
									//console.log(current + ' comes --> ' + cnt + ' times<br>');
								}
								current = array_modules[i];
								cnt = 1;
							} else {
									cnt++;
								}
						}
						if (cnt > 0) {
							map[current] = cnt;
							//console.log(current + ' comes --> ' + cnt + ' times');
						}

						var count_modules = 0;   //conta i moduli che hanno occorrenza uguale a numero di check (num_check)
						$.each(map, function(key, value) {
							if(value == num_check){
								count_modules++;
								console.log(key);
								console.log(count_modules);
							}
							//alert( "The key is '" + key + "' and the value is '" + value + "'" );
						});

						console.log(array_modules);
						console.log(array_id);
						document.getElementById('result').innerHTML = count_modules;
					
					}
					// ------------------------------------------------------Uncheck---------------------------------------------------------------
					if(feature.checked == false){
						num_check--;
						var str = feature.value.split(",");
						for(i=0; i<str.length; i++){
							var index = array_modules.indexOf(str[i]);
							if (index > -1) {
								array_modules.splice(index, 1);
							}
						}

						var index_id = array_id.indexOf(feature.id);
							if (index_id > -1) {
								array_id.splice(index_id, 1);
							}
						array_modules.sort();

						var map = {};
						var current = null;
						var cnt = 0;
						for (var i = 0; i < array_modules.length; i++) {
							if (array_modules[i] != current) {
								if (cnt > 0) {
									map[current] = cnt;								
									//console.log(current + ' comes --> ' + cnt + ' times<br>');
								}
								current = array_modules[i];
								cnt = 1;
							} else {
									cnt++;
								}
						}
						if (cnt > 0) {
							map[current] = cnt;
							//console.log(current + ' comes --> ' + cnt + ' times');
						}

						var count_modules = 0;
						$.each(map, function(key, value) {
							if(value == num_check){
								count_modules++;
								console.log(key);
								console.log(count_modules);
							}
							//alert( "The key is '" + key + "' and the value is '" + value + "'" );
						});

						//console.log(array_modules);
						//console.log(array_id);
						document.getElementById('result').innerHTML = count_modules;
						
					}
					// token_encode = window.btoa(token);

					var obj = {
								token : "<?php echo $tok?>",
								rules: array_id,
								doFase: <?php echo $doFase?>,
							};	

					array_id_string = JSON.stringify(obj);
					array_id_encode = window.btoa(array_id_string);

					//console.log(array_id_encode);
					console.log(array_id_string);

					document.myform.firstElementChild.setAttribute("value",array_id_string);
					
					//Modificato da Hui (07/09): Faccio comparire il numero di modulo se ci sono regole selezionate-----------------------
					if(num_check>0)
						document.getElementById("number_of_modules").style.visibility="visible";
					else
						document.getElementById("number_of_modules").style.visibility="hidden";
				}
			</script>	
			
			</div>
			</center>
			
			<!-- una progress step bar
			<div class="container">
			  <ul class = "progressbar">
				<li class = "active"> <//?php echo convert_RS('Choose rules'); ?> </li>
				<li> <//?php echo convert_RS('Filter Keyword'); ?> </li>
				<li> <//?php echo convert_RS('Modulo & Keyword'); ?> </li>
				<li> <//?php echo convert_RS('Continue or create'); ?> </li> </div>		-->
		
		<script scr = "progressbarnormale.js"> </script>
		
		
	</body>
</html>
<?php	
	//echo $OUTPUT->footer();	s_t_mod
?>
