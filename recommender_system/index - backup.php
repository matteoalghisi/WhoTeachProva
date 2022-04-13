<?php
	require_once('../config.php');
	require_once('../course/lib.php');
	require_once('../metadata/metadata_page/aux_functions.php');
	require_once($CFG->libdir .'/mysql_conn.php');
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

	// Se è settata l'opzione di i_back vuol dire che devo killare il thread appeso e rincominciare con uno nuovo
	if (isset($_POST['i_back']) && $_POST['i_back'] === 'stop')
	{
		$address = '::1';
		$port = 20002;

		$token = $_SESSION['data'];
		$token = $token['token'];
		$message = array('token' => $token, 'doFase' => '-1');		
		$data_json = json_encode($message);
		$message = $data_json. "\r\n";
		
		if (($sock = socket_create(AF_INET6, SOCK_STREAM, 0)) === false) {
			echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
			exit(0);
		}

		if (socket_connect($sock, $address, $port) === false) {
			echo "socket_connect() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
			exit(0);
		}

		if (socket_write($sock, $message, strlen($message)) === false) {
			echo "socket_write() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
			exit(0);
		}

		socket_close($sock);
	}



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
		<title>NETT RS</title>
		<link href="themes/help/help.css" rel="stylesheet" />
		<link rel="stylesheet" href="themes/alertify/themes/alertify.core.css" />
		<link rel="stylesheet" href="themes/alertify/themes/alertify.default.css" />
	</head>
	<body>
	
		<br>
		<div>
			<center>
				<a href="#">
					<img src="themes/img/logo.png" alt="whoteach logo" width="40%" height="40%">
				</a>
			</center>
		</div>
		
		<div class="progress" style = "margin-left:10%; margin-right:10%;">
			<div class="progress-done"  data-done = "0">
			  0%
			</div>
		</div>
		<br>
		
		<form method="POST" action="step1.php" name="myForm" id="myForm" margin>
			<input type="hidden" value="<?php echo $token; ?>" name="token"/>
			<table style="height:80%; width:80%; border-radius: 18px; box-shadow: 0 9px 9px rgba(0,0,0,.3);" align="center" bgcolor="#e0dddb">
				<th colspan="2">
					<h1 style = "text-align:left; margin-left:2.7%; margin-top:5%; margin-bottom:5%;">
						<font  color="#383a3d" style="font-size:30px">
							<?php echo convert_RS('COURSE FEATURES'); ?>
						</font>
					</h1>
				</th>
				
				<tr>
					<td>
						<p><h4 style = "margin-bottom:10%; text-align:left; margin-left:5%;"><?php echo convert_RS('Course Name'); ?>:</h4></p>
					</td>
					<td>
						<center>
							<div class="controls" id="div_cn">
								<input type="text" class="input-large" name="cn" id="cn_id" placeholder="<?php echo convert_RS('Insert course name'); ?>" style="font-size:16px; border-radius: 6px;">
									<p id="alert"/>
								</input>
							</div>
						</center>
					</td>
				</tr>
				<tr>
					<td align="center">	
						<center>
							<h4  style = "margin-bottom:10%; text-align:left; margin-left:5%;"><?php echo convert_RS('Knowledge Area'); ?>:
								<span class="gestOverlayer">
									<img class="fakeLink" src="themes/img/question.png" style="width:16px;height:16px"/>
									<span class="overlayer">
										<?php echo convert_RS('Courses\' Macrocategories');?>
									</span>
								</span>
							</h4>
						</center>
					</td>
					<td>
						<div class="controls" >
							<center>
								<select name="ka" style="font-size:16px">
								<?php
									$connection = GetMyConnection();
									if ($connection < 0)
									{
										print errorDB($connection);
										die();
									}
									
									$result = mysqli_query($connection, "SELECT name FROM mdl_course_categories" );
									while ($row = mysqli_fetch_row($result)){
										$c = convert_RS($row[0]);
										echo '<option value="'.$row[0].'">'.$c.'</option>';
									}
									
								?>		
								</select>
							</center>
						</div>
					</td>
				</tr>
				<tr>
					<td align="center">		
						<center>
							<h4  style = "margin-bottom:10%; text-align:left; margin-left:5%;"><?php echo convert_RS('Number of Sections'); ?>:
								<span class="gestOverlayer">
									<img class="fakeLink" src="themes/img/question.png" style="width:16px;height:16px"/>
									<span class="overlayer">
										<?php echo convert_RS('Number of sections the course will contain');?>
									</span>
								</span>
							</h4>
						</center>
					</td>
					<td>
						<center>
							<div  >
								<select class="input-small" name="nop" style="font-size:16px">
									<?php 
									for($i=1; $i<10; $i++){
										if ($i == 4)
											echo "<option value='".$i."' selected=\"selected\">".$i."</option>";
										else
											echo "<option value='".$i."'>".$i."</option>";
									}
									?>
									
								</select>
							</div>
						</center>
					</td>
					<!-- aggiunta in menu principale che secondario, testing
					
				-->
				
				<tr>
							<td align="center">
							<center>
							<h4 style = "margin-bottom:10%; text-align:left; margin-left:5%;">
								<?php echo convert_RS('Threshold'); ?>:
								<span class="gestOverlayer">
									<img class="fakeLink" src="themes/img/question.png" style="width:14px;height:14px"/>
									<span class="overlayer">
										<?php echo convert_RS('Threshold info');?>
									</span>
								</span>
								</h4>
								</center>
							</td>
							<td>
							<center>
								<div class="controls">
									<select class="input-small" name="thresh">
										<?php
											$i = 20;
											while ($i<=100){
												if($i==40)
													echo '<option value="'.$i.'" selected="selected">'.$i.'%</option>';
												else
													echo '<option value="'.$i.'">'.$i.'%</option>';
												$i+=20;
											}
										?>		
									</select>
								</div>
								</center>
							</td>
						</tr>
				</tr>
				<tr>
					<td align="center" colspan="2">
					<!--.........................................................................................................-->
					</td>
				</tr>
			</table>

			<!--p>
				<center>
					<h1>
						<font color="#383a3d"><php echo convert_RS('Recommender System Parameters'); ?></font>
					</h1>
				</center>
			</p-->
	
	
			<div class="item-title"><img src="./themes/img/pignon.png" style="height:30px;"> <a class="item-link"><?php echo convert_RS('Show advanced RS options'); ?></a></div>
			   <div class="item-content">
					<table align="center">
						<tr>
						<!--
							<td align="center">
								<?php echo convert_RS('Threshold'); ?>:
								<span class="gestOverlayer">
									<img class="fakeLink" src="themes/img/question.png" style="width:14px;height:14px"/>
									<span class="overlayer">
										<?php echo convert_RS('Threshold info');?>
									</span>
								</span>
							</td>
							<td>
								<div class="controls">
									<select class="input-small" name="thresh">
										<?php
											$i = 20;
											while ($i<=100){
												if($i==40)
													echo '<option value="'.$i.'" selected="selected">'.$i.'%</option>';
												else
													echo '<option value="'.$i.'">'.$i.'%</option>';
												$i+=20;
											}
										?>		
									</select>
								</div>
							</td>
							-->
						</tr>
						<tr><td></td></tr>
						<tr>
							<td align="center">
								<?php echo convert_RS('Rules Support'); ?>:
								<span class="gestOverlayer">
									<img class="fakeLink" src="themes/img/question.png" style="width:14px;height:14px"/>
									<span class="overlayer">
										<?php echo convert_RS('This threshold defines incidence of rules');?>
									</span>
								</span>
							</td>
							<td>
								<div class="controls">
									<select class="input-small" name="rsup">
										<?php
											$j = 10;
											while ($j<=100){
												if($j==20)
													echo '<option value="'.$j.'" selected="selected">'.$j.'%</option>';
												else
													echo '<option value="'.$j.'">'.$j.'%</option>';
												$j+=10;
											}
										?>		
									</select>
								</div>
							</td>
						</tr>
						<tr><td></td></tr>
						<tr>
							<td align="center">
								<?php echo convert_RS('Keywords'); ?>:
								<span class="gestOverlayer">
									<img class="fakeLink" src="themes/img/question.png" style="width:14px;height:14px"/>
									<span class="overlayer">
										<?php echo convert_RS('Minimum number of keywords that will be displayed in the "Keywords" step.');?>
									</span>
								</span>
							</td>
							<td>
								<div>
									<select class="input-small" name="kmin">
										<option value="2" selected="selected">2</option>
										<option value="3">3</option>
										<option value="4">4</option>
										<option value="5">5</option>
									</select>
								</div>
							</td>
						</tr>
						<tr><td></td></tr>
					
						<select class="input-small" name="kmax" style='display:none;'>
							<option value="5">5</option>
							<option value="7">7</option>
							<option value="9">9</option>
							<option value="12" selected="selected" >12</option>
						</select>
							
						<td align="center">
							<?php echo convert_RS('Rules per Page'); ?>:
							<span class="gestOverlayer">
								<img class="fakeLink" src="themes/img/question.png" style="width:14px;height:14px"/>
								<span class="overlayer">
									<?php echo convert_RS('Maximum number of rules that will be displayed in the "Rules" step.');?>
								</span>
							</span>
						</td>
						<td>
							<div>
								<select class="input-small" name="ruleXpage">
									<option value="5" selected="selected">5</option>
									<option value="10">10</option>
									<option value="15">15</option>
									<option value="20" >20</option>
								</select>
							</div>
						</td>
					</table>
				</div>
			
			<center style = "margin: 8px">
				<input type="reset" id="reset" value="<?php echo convert_RS('Reset Values'); ?>" class="btn btn-large btn-primary" style="height:40px; width:auto; padding: 5px 5px 5px 5px; font-weight:bold; border-radius: 9px;">
				<input type="submit" id="continue" value="<?php echo convert_RS('Continue'); ?>" class="btn btn-large btn-primary" style="font-size: 20; height:40px; width:auto; padding: 5px 5px 5px 5px; font-weight:bold; border-radius: 9px;" onclick="return check();">
				
				<p align="center">
					<img id='loading' src='themes/spinner/ajax-loader.gif' style='visibility:hidden;'>
				</p>
			</center>
		</form>

		<!-- Alertify JS -->
		<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
		<script src="themes/alertify/lib/alertify.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				// Form di avviso
				$("#myForm").submit(function(event)
				{
					event.preventDefault(); // cancel submit
					alertify.confirm("<font color='#5392B3' style='font-weight:bold; font-size:16px;'><?php echo convert_RS('For the proper functioning of the service SHOULD NOT be used commands/buttons "Next" and "Back" of the browser.'); echo "<br />"; echo convert_RS('Remember to use navigation functions that the system offers.');  ?></font>", function (e) {
					
						if (e)
							$("#myForm")[0].submit(); // submit form skipping jQuery bound handler
						else
						{
							var element = document.getElementById("continue");
							element.setAttribute('style','visibility:visible;');
							var element2 = document.getElementById("reset");
							element2.setAttribute('style','visibility:visible;');
							var img = document.getElementById("loading");
							img.setAttribute('style','visibility:hidden;');
						}
					});
					
					document.getElementById('alertify-cancel').innerHTML = "<?php echo convert_RS('Cancel');  ?>";
					document.getElementById('alertify-ok').innerHTML = "<?php echo convert_RS('CONTINUE');  ?>";
				});	
				
				$(".item-title").click(function(){
					$(".item-content").toggle();
					console.log("toggle");
				});
				
				document.getElementById('block-region-side-post').style.display = "none";
				document.getElementById('alertify-cancel').innerHTML = "Home";
			});
			
			//function check() onclick
			function check()
			{
				if (!myForm.cn.value)
				{
					alertify.alert("<font color='#5392B3' style='font-weight:bold; font-size:16px;'><?php echo convert_RS('PLEASE INSERT COURSE NAME'); ?></font>");
					var red = document.getElementById("cn_id");
					red.setAttribute('style',"font-size:16px; border:2px solid red; border-radius: 6px;");
					document.getElementById("alert").innerHTML = "<?php echo convert_RS('Required Field!'); ?>";

					return (false);
				}
				var element = document.getElementById("continue");
				element.setAttribute('style','visibility:hidden;');
				var element2 = document.getElementById("reset");
				element2.setAttribute('style','visibility:hidden;');
				var img = document.getElementById("loading");
				img.setAttribute('style','visibility:visible;');
				return (true);
			}
		</script>
	</body>
</html>

<?php } ?>

<?php	
	echo $OUTPUT->footer();
?>
