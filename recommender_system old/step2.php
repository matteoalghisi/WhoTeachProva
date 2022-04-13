<?php
	require_once('../config.php');
	require_once('../metadata/metadata_page/aux_functions.php');
	require_once('functions.php');

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
	
	<style>
			.bx-wrapper .bx-viewport{background-color:#e0dddb;}
	</style>
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
		<center>
			<h1>
				<font color="#383a3d" style="text-transform: lowercase;">
					<?php echo convert_RS('RULES')." ".(convert_RS('FOR'))."</br>".strtoupper(convert_RS($_SESSION['ka'])); ?>
				</font>
			</h1>
			<h1><?php echo convert_RS('Compiling part: '); echo $_SESSION["parte_corrente"]; echo convert_RS(' of '); echo $_SESSION['data']['nop'];?></h1>
			<p>
				<strong>
					<font color="#383a3d">
						<?php echo convert_RS('Two or more selected rules are interpreted as the conjunction of them'); ?>
					</font>
				</strong>
			</p>
			<p>
				<font style="font-weight:bold;">
					<?php echo convert_RS('Number of Modules:'); ?>
				</font>
			</p>	
			<p id="result">0</p>
		</center>

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

			echo '<ul class="bxslider">';
				$j=0;
				$i=0;
				$bool = 0; //bool usato per uscire dal secondo while e tornare al primo
				$bool2 = 0; //bool 2 usato per controllare se sono già entrato nell'if con j=10, 20, 30 ecc...
				while($i<$num_schermate)
				{
					$bool = 0;
					echo "<li align='left'>";

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
							$newrule = str_ireplace("&","<br/>",$newrule);
							$newrule = "<div style='margin-left: 35px; margin-bottom:25px; align-items: left;'>".$newrule."</div>";
							echo "<p style='float:left; height: 40px; margin:5px;'><input style='margin-left:5px;' type=\"checkbox\" id=\"".$rule['IDrule']."\" value=\"" . $myModules . "\" name=\"" .$tok. "\" onchange=\"update(this);\"/></p>".$newrule."<br>";
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
	<center>
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
		<form id="myformBack" name="myformBack" action="<?php print $action; ?>" style="display:inline;" method="post">
			<input name="i_back" id="i_back" value="<?php print $value; ?>" style="display: none;"/>
			<input type="submit" name="back" id="back" value="<?php echo convert_RS('BACK'); ?>" class="btn btn-large btn-primary" style="height:40px; width:auto; padding: 5px 5px 5px 5px; font-weight:bold; border-radius: 9px;" >
		</form>
		
		<form id="myform" name="myform" method="post" action="step3.php" style="display:inline;">
			<input type="hidden" name="rules" id="rules" value='{"token":"<?php echo $tok ?>","rules":[],"doFase":"<?php echo $doFase?>"}'/>
			<input type="hidden" name="time" value="first"/>
			<input type="hidden" name="fullrule" id="fullrule" value=""/>
			<input type="submit" name="continue" id="continue" value="<?php echo convert_RS('CONTINUE'); ?>" class="btn btn-large btn-primary" style="height:40px; width:auto; padding: 5px 5px 5px 5px; font-weight:bold; border-radius: 9px;" >
			<p align="center">
				<img id='loading' src='themes/spinner/ajax-loader.gif' style='visibility:hidden;'>
			</p>
		</form>
	<!--/div-->
	<br>
	</center>
		<!-- jQuery library (served from Google) -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<!-- bxSlider Javascript file -->
		<script src="themes/jquery/jquery.bxslider.min.js"></script>
		<!-- Alertify JS -->
		<script src="themes/alertify/lib/alertify.min.js"></script>
		
		
		<script>
			$(document).ready(function(){
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
						alertify.alert("<font color='#5392B3' style='font-weight:bold; font-size:16px;'><?php echo convert_RS('PLEASE SELECT SOME RULES'); ?></font>");
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
			});
			
			
			///mia funzione per quando nn ci sono regole
			function skipStep(){
				alertify.alert("<font color='#383a3d' style='font-weight:bold; font-size:16px;'><?php echo 'no rules found for this part, go to the next phase...'; ?> </font>");
				document.getElementById('alertify-ok').onclick = function(){$("#continue").trigger('click')};
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
			}
		</script>		
		
	</body>
</html>
<?php	
	echo $OUTPUT->footer();
?>
