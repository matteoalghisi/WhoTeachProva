<?php
require_once('../config.php');
require_once('../metadata/metadata_page/aux_functions.php');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$HTTP_STR=$_POST;
}else{
	$HTTP_STR=$_GET;
}
$course=$HTTP_STR["id_course"];


require_login();
$PAGE->set_context(context_course::instance($course));

//require('php/knowledge_base.php');

$PAGE->set_pagetype('site-index');
$PAGE->set_docs_path('');
//$PAGE->set_pagelayout('frontpage');
//$editing = $PAGE->user_is_editing();
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading($SITE->fullname);
$urlparams = array('id_course' => $course);
$PAGE->set_url('/search_engine', $urlparams); // Defined here to avoid notices on errors etc
//$courserenderer = $PAGE->get_renderer('core', 'course');
echo $OUTPUT->header();

//	if ($REQUEST_METHOD=="POST") { // 20170615

?>

<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">

  <link rel="stylesheet" type="text/css" href="./css/multi-select.css">
  <link rel="stylesheet" type="text/css" href="./css/scrollable-wizard.css">
  <link rel="stylesheet" type="text/css" href="./css/jquery.multiselect.css">
  <link rel="stylesheet" type="text/css" href="./css/jquery-ui.css">



  <!--Avoid button double click--> 

  <script type="text/javascript">
	 function changeButton() {
	     document.getElementById("button").innerText = 'Please wait...';
	     document.getElementById("button").style.background='#FFCC66';
	 }
  </script>

  <script type="text/javascript">
	  $(document).ready(function(){
		  $(".close").click(function(){
			  if($.data(this, 'clicked')){
	  			  return false;
			
			  } else {
	  			  $.data(this, 'clicked', true);
	  			  return true;
			  }	
		  });
	  });
  </script>

 
 <!-- script for multiselection component -->
<script type='text/javascript'>//<![CDATA[ 
$( document ).ready(function(){
var s = $('#my-select');
s.multiSelect({
    selectableHeader: "<div class='custom-header' ><?php echo translate_engine('Modules');?></div>",
    selectionHeader: "<div class='custom-header'><?php echo translate_engine('Selected');?></div>"
	});


//s.append($('<option />').text("new").val("value"));
//s.multiSelect('refresh');
});//]]>  
</script> 
 
<script type="text/javascript">

function fire_ajax(array_of_values,key_value){
	var course=document.getElementById('id_course').value;
	var params=array_of_values+"&key_value="+key_value+"&course="+course;
	$.post('php/ajax_filter_sections.php', params, function(data) {
	$('#my-select').html(data);
	var s = $('#my-select');
	s.multiSelect('refresh');
	});
};

function keyword_ajax(){


var array_of_checked_values = new Object();
var keyword=document.getElementById("key");

		for (var i=1;i<=10;i++)
				{ 
				array_of_checked_values[i] = $("#select"+i).multiselect("getChecked").map(function(){
					return this.value; }).get();
					}
			var array_of_values='';
			for (var i=1;i<=10;i++)
				{   if (i>1) array_of_values=array_of_values+"&";
					array_of_values=array_of_values+"select"+i+"="+array_of_checked_values[i];
					}

			fire_ajax(array_of_values,keyword.value);
};


function fire_ajax_category(array_of_values_category){
    var course=<?php echo $course; ?>;
	var params=array_of_values_category+"&course="+course;

	$.post('php/ajax_filter_req_skill.php', params, function(data) {
	$('#select3').html(data);
	$("#select3").multiselect('refresh');
	});
	$.post('php/ajax_filter_acq_skill.php', params, function(data) {
	$('#select4').html(data);
	$("#select4").multiselect('refresh');
	});
};

$(function(){

var array_of_checked_values = new Object();
var keyword=document.getElementById("key");

for (var i=1;i<=10;i++)
				{ 

	$("#select"+i).multiselect({
		selectedList: 4,
		header: false,
		click: function(e, ui){
		var skill=false;
	
		if ( e.target.id != 'select2' )
			{
			skill=true;
			}
		else {
			skill= false;
			}
			console.log(skill);
			console.log(e);
			console.log(e.target.id);

			for (var i=1;i<=10;i++)
				{ 
				array_of_checked_values[i] = $("#select"+i).multiselect("getChecked").map(function(){
					return this.value; }).get();
					}
			var array_of_values='';
			for (var i=1;i<=10;i++)
				{   if (i>1) array_of_values=array_of_values+"&";
					array_of_values=array_of_values+"select"+i+"="+array_of_checked_values[i];
					}
		
			array_of_checked_values_category = $("#select2").multiselect("getChecked").map(function(){
					return this.value; }).get();
			var array_of_values_category='';
			array_of_values_category=array_of_values_category+"category="+array_of_checked_values_category;
			fire_ajax(array_of_values,keyword.value);
			if (skill==false)
				{
				fire_ajax_category(array_of_values_category);
				}
			}
			
	});
	}

	
});


</script>


 </head>

 			<?php
			require 'php/mysql_conn.php';
			
			/*
			//STAMPA IL TASTO PER TORNARE AL CORSO
			$server_name = $_SERVER['SERVER_NAME'];
			print '<form action=http://'.$server_name.'/lms_rs/course/view.php target="_top">';
			echo "<input type='hidden' name='id' value='".$_GET['id_course']."'/>";
			print '<input type="submit" align="center" value="Return to course">';
			print '</form>';
			*/

			echo '<br/><div align="center"><h1>'.translate_engine("Add modules through metadata").'</h1></div><br/>';

			$i=0;
			$result = mysqli_query(GetMyConnection(), "SELECT Property,Value FROM mdl_metadata where Id_course=".$course);
			$stringa_where='';
			/*
			while ($row = mysql_fetch_row($result)){
				//echo $row[0] . " ". $row[1] . "<br />";
				$stringa_where=$stringa_where."AND (exists (select * from mdl_metadata z where mdl_course_sections.id = z.Id_course_sections and (z.Property='".$row[0]."' and z.Value IN ".$row[1]."))) ";
				//echo $row[1];
				if (!$array_metadati[$row[0]])
						{
						$array_metadati[$row[0]]= array(0 => $row[1]);
						}
						else
						{
						$array_metadati[$row[0]][]= $row[1];
						}
			$i=$i+1;
			}*/
	//	echo 'ciao'.$stringa_where;
	//		print_r ($array_metadati);
			?>

<body>
 <!-- the form -->
<?php 
$server_name = $_SERVER['SERVER_NAME'];
print '<form id="filterform" action="https://'.$server_name.'/lms/backup/import_mod.php" target="_top">'; 
?>



<!-- scrollable root element -->
  <div id="wizard">
 
    <!-- status bar -->
    <ul id="status">
      <li class="active"><strong><?php echo translate_engine('NETT parameters - 1/3');?></strong></li> <!-- s_t_mod Chiara: prima era translate_engine('Parametri - 1/3') -->
      <li><strong><?php echo translate_engine('NETT parameters - 2/3');?></strong></li> <!-- s_t_mod Chiara: prima era translate_engine('Parametri - 2/3') -->
      <li><strong><?php echo translate_engine('NETT parameters - 3/3');?></strong></li> <!-- s_t_mod Chiara: prima era translate_engine('Parametri - 3/3') -->
    </ul>
 
    <!-- scrollable items -->
    <div class="items">
      <!-- pages 1 -->
      <div class="page">

			<ul>
				<!-- <li >
					<label>
						<p><strong>1.</strong>
						Keywords</p>
						<input type="text" class="text" name="keywords">
					</label>
				</li>-->
				<li>
					<label>
					<p><strong>1.</strong><?php echo translate_engine('Language');?></p>
						<select multiple="multiple" id="select1" name="language[]"  style="width:250px">
						<?php
						$result = mysqli_query( GetMyConnection(), "SELECT property_value FROM mdl_metadata_descr where property_name='language'" );
						while ($row = mysqli_fetch_row($result)){
						/*s_t_mod Hui: array_metadati non definito e non usato
						if (in_array($row[0], $array_metadati['language'])) {
						echo '<option value="'.$row[0].'" selected="selected">'.translate_language($row[0]).'</option>';
						}
						else */
							echo '<option value="'.$row[0].'">'.translate_language($row[0]).'</option>';
							
						}
								?>

						</select>
					</label>
				</li>
				<li>
					<label>
						<p><strong>2.</strong><?php echo translate_engine('Category');?></p>
						<select multiple="multiple" id="select2" name="category[]" style="width:250px" >
							<?php
							//s_t_mod Hui
							//$result = mysqli_query(GetMyConnection(), "SELECT property_value FROM mdl_metadata_descr where property_name='category'" );
							$result = mysqli_query(GetMyConnection(), "SELECT id,name FROM mdl_course_categories" );
							$lista_category="";
							while ($row = mysqli_fetch_row($result)){
								
							//s_t_mod Hui: array_metadati non definito e non usato
							/*if (in_array($row[1], $array_metadati['category'])) {
							echo '<option value="'.$row[0].'" selected="selected">'.$row[1].'</option>';
							$lista_category=$lista_category."'".$row[1]."',";
									}
							else */
								echo '<option value="'.$row[0].'">'.translate_category($row[1]).'</option>';
							}
							//----------------------------------------------------------------------------------------------------------------
							?>
						</select>
					</label>
				</li>
				<div id="content">
				<div id="left">
				<li>
					<label>
						<p><strong>3.</strong><?php echo translate_engine('Required skills');?> </p>
						<select multiple="multiple" id="select3" name="d_req_skill[]" style="width:250px">
						<?php
							/*s_t_mod Hui: questa parte non viene usata
							$lista_category=substr($lista_category, 0, strlen($lista_category)-1);s_t_mod Hui
							$stringa_query="SELECT property_value FROM mdl_metadata_descr where property_name='d_req_skill' and category in (".$lista_category.")";

							$result = mysqli_query( GetMyConnection(), $stringa_query );
							while ($row = mysqli_fetch_row($result)){
							if (in_array($row[0], $array_metadati['d_req_skill'])) {
							echo '<option value="'.$row[0].'" selected="selected">'.$row[0].'</option>';
						}
							else 
								echo '<option value="'.$row[0].'">'.$row[0].'</option>';
							}*/
							?>
						</select>
					</label>
				</li>
			 </div>
			 <div id="right">
				<li>
					<label>
						<p><strong>4.</strong><?php echo translate_engine('Acquired skills');?> </p>
						<select multiple="multiple" id="select4" name="d_acq_skill[]" style="width:250px">
						
						<?php
							/*s_t_mod Hui: questa parte non viene usata
							$stringa_query="SELECT property_value FROM mdl_metadata_descr where property_name='d_acq_skill' and category in (".$lista_category.")";
								
							$result = mysqli_query(GetMyConnection(), $stringa_query );
							
							while ($row = mysqli_fetch_row($result)){
							if (in_array($row[0], $array_metadati['d_acq_skill'])) {
							echo '<option value="'.$row[0].'" selected="selected">'.$row[0].'</option>';
						}
							else 
								echo '<option value="'.$row[0].'">'.$row[0].'</option>';
							}*/
							?>
						</select>
					</label>
				</li>
			 </div>
			</div>
			

				<li class="clearfix">
					<button type="button" class="next right">
					<?php echo translate_engine('Proceed >>');?>
					</button>
				</li>
				<br clear="all">
			</ul>
  	  </div>
	  
	  <!-- pages 2 -->
      <div class="page">
	  	  	<ul>
				<li>
					<label>
						<p><strong>5.</strong><?php echo translate_engine('Learning Resource Type');?></p>
					<select  multiple="multiple" id="select5"  name="resourcetype[]"  style="width:250px">
						<?php
						$result = mysqli_query(GetMyConnection() , "SELECT property_value FROM mdl_metadata_descr where property_name='resourcetype'");
						while ($row = mysqli_fetch_row($result)){
							
						//s_t_mod Hui: array_metadati non definito e non usato
						/*if (in_array($row[0], $array_metadati['resourcetype'])) {
						echo '<option value="'.$row[0].'" selected="selected">'.$row[0].'</option>';
						}
						else */
							echo '<option value="'.$row[0].'">'.translate_type($row[0]).'</option>';
							
						}
						?>
					</select>
					</label>
				</li>
				<li>
					<label>
						<p><strong>6.</strong><?php echo translate_engine('Difficulty');?></p>
						<select multiple="multiple" id="select6" name="difficulty[]" style="width:250px">
							<?php
							$result = mysqli_query(GetMyConnection(), "SELECT property_value FROM mdl_metadata_descr where property_name='difficulty'");
							while ($row = mysqli_fetch_row($result)){
								
							//s_t_mod Hui: array_metadati non definito e non usato
							/*if (in_array($row[0], $array_metadati['difficulty'])) {
							echo '<option value="'.$row[0].'" selected="selected">'.$row[0].'</option>';
							}
							else */
								echo '<option value="'.$row[0].'">'.translate_difficulty($row[0]).'</option>';
								
							}
							?>
						</select>
					</label>
				</li>
				
				<li>
					<label>
						<p><strong>7.</strong><?php echo translate_engine('Format');?></p>
						<select multiple="multiple" id="select9" name="format[]" style="width:250px">
							<?php
							$result = mysqli_query(GetMyConnection(), "SELECT property_value FROM mdl_metadata_descr where property_name='format'" );
							while ($row = mysqli_fetch_row($result)){
								
							//s_t_mod Hui: array_metadati non definito e non usato
							/*if (in_array($row[0], $array_metadati['format'])) {
							echo '<option value="'.$row[0].'" selected="selected">'.$row[0].'</option>';
							}
							else */
								echo '<option value="'.$row[0].'">'.translate_format($row[0]).'</option>';
								
							}
							?>
						</select>
					</label>
				</li>
			<!--
				/////////////////////AGE DISABILITATO
				/*
				<div id="left">
					<li>
						<label>
							<p><strong>7.</strong><?php //echo translate_engine('Minimal Age');?></p>
							<select multiple="multiple" id="select7" name="min_age[]" style="width:250px">
								<?php
								/*
								$result = mysqli_query(GetMyConnection(), "SELECT property_value FROM mdl_metadata_descr where property_name='min_age'");
								while ($row = mysqli_fetch_row($result)){
								if (in_array($row[0], $array_metadati['min_age'])) {
								echo '<option value="'.$row[0].'" selected="selected">'.$row[0].'</option>';
								}
								else 
									echo '<option value="'.$row[0].'">'.$row[0].'</option>';
								
								}
								*/
								?>
							</select>
						</label>
					</li>
				</div>
				<div id="right">
					<li>
						<label>
							<p><strong>8.</strong><?php //echo translate_engine('Maximal Age');?></p>
							<select multiple="multiple" id="select8" name="max_age[]" style="width:250px">
								<?php
								/*
								$result = mysqli_query(GetMyConnection(), "SELECT property_value FROM mdl_metadata_descr where property_name='max_age'" );
								while ($row = mysqli_fetch_row($result)){
								if (in_array($row[0], $array_metadati['max_age'])) {
								echo '<option value="'.$row[0].'" selected="selected">'.$row[0].'</option>';
								}
								else 
									echo '<option value="'.$row[0].'">'.$row[0].'</option>';
								
								}
								*/
								?>
							</select>
						</label>
					</li>
				</div>
				*/
			-->
				
				<li class="clearfix">
					<button type="button" class="prev disabled" style="float:left">
					<?php echo translate_engine('<< Back');?>
					</button>
					<button type="button" class="next right">
					<?php echo translate_engine('Proceed >>');?>
					</button>
				</li>
				<br clear="all">
		</ul>
	  </div>
	  
	  <!-- pages 3 -->
      <div class="page">
	  
	  <ul>

				<li>
					<label>
						<p><strong>8.</strong><?php echo translate_engine('Typical Learning Time');?></p>
						<select multiple="multiple" id="select10" name="time[]" style="width:250px">
							<?php
							$result = mysqli_query(GetMyConnection(), "SELECT property_value FROM mdl_metadata_descr where property_name='time'" );
							while ($row = mysqli_fetch_row($result)){
							
							//s_t_mod Hui: array_metadati non definito e non usato
							/*if (in_array($row[0], $array_metadati['time'])) {
							echo '<option value="'.$row[0].'" selected="selected">'.$row[0].'</option>';
							}
							else */
								echo '<option value="'.$row[0].'">'.translate_time($row[0]).'</option>';
								
							}
							?>
						</select>
					</label>
				</li>
			<li >
					<label>
						<p><strong>9.</strong><?php echo translate_engine('Keywords (separator ", ")');?></p>
						<input id="key" type="text" class="text" name="keywords" size=44 onchange="keyword_ajax()" >
		
					</label>
				</li>
				<li class="clearfix">
					<button type="button" class="prev disabled" style="float:left">
					<?php echo translate_engine('<< Back');?>
					</button>
					<br><br>
										
			<!-- 		<button type="button" onclick='javascript:  $(function() { $("#wizard").scrollable().begin(0);  });'>
					First page
					</button> -->
					

				</li>
				<br clear="all">
		</ul>
	
	
	  </div>
	  
	 </div>

	
  </div>		
<div id="sections">
		<p align=center ><br/><font size="3"><?php echo translate_engine('Select the modules that compose the course');?> </font></p>  
			<select multiple="multiple" id="my-select" name="my-select[]">

			<?php

			$stringa_where=substr($stringa_where, 0, strlen($stringa_where)-4);	
			if ($stringa_where=='') 
				{$sql_sections="SELECT distinct x.section,x.name,x.summary, x.id, y.fullname FROM mdl_course y,mdl_course_sections x where x.course = y.id and x.name is not null AND x.visible = 1 AND x.id > 2 AND sequence <> ''";
				}
				else {
					$sql_sections="SELECT distinct x.section,x.name,x.summary, x.id, y.fullname FROM mdl_course y,mdl_course_sections x,mdl_metadata where x.course = y.id and x.name is not null  ".$stringa_where."))) AND x.visible = 1 AND x.id > 2 AND sequence <> ''";
				}
			
			$sql_sections=$sql_sections." and x.id  not in (select id_sec_dest from sssecm_duplicates WHERE flag = 0) AND x.id  not in (SELECT id FROM mdl_course_sections WHERE course = '".$course."') ";
			//$stringa_key=clean_phrase_input($stringa_key);
			//$string_key_full=" and x.id in (SELECT id_section from full_text_section where  MATCH (name,summary,keywords) AGAINST('".$stringa_key."' IN BOOLEAN MODE));";

			//if ($string_key!='*') $sql_sections=$sql_sections.$string_key_full;
			
			echo $sql_sections;
			$result = mysqli_query( GetMyConnection(), $sql_sections);
			$i=1;
			while ($row = mysqli_fetch_row($result)){
			$val1= strip_tags($row[1]);
			$val2= strip_tags($row[2]);
			$val3= strip_tags($row[3]);
			$val4= strip_tags($row[4]);
			echo ($val1);
			
			//colorazione del nome
			/*$sql = "SELECT mdl_metadata.Id_metadata, mdl_metadata.Value value
				FROM mdl_metadata 
				WHERE id_course_sections=" . $val3 . " AND Property='status'";
			$fields = $DB->get_records_sql($sql);
			foreach ($fields as $field) {
				$color = $field->value;
			}
			if($color == "green"){
				//$name = "background-color: #00ff00";
				$name = "background-color: #ffffff; color:#000000"; //s_t_mod Chiara
			}
			elseif($color == "yellow"){
				//$name = "background-color: #ffff00";
				$name = "background-color: #ffffff; color:#000000"; //s_t_mod Chiara
			}
			elseif($color == "red"){
				//$name = "background-color: #ff0000";
				$name = "background-color: #ffffff; color:#000000"; //s_t_mod Chiara
			}
			elseif($color == "white"){
				//$name = "background-color: #ffffff";
				$name = "background-color: #ffffff; color:#000000"; //s_t_mod Chiara
			}
			elseif($color == "gold"){
				//$name = "background-color: #ffd700";
				$name = "background-color: #ffffff; color:#000000"; //s_t_mod Chiara
			}
			elseif ($color == "black") {
				//$name = "background-color: #000000; color: #ffffff";
				$name = "background-color: #ffffff; color:#000000"; //s_t_mod Chiara
			}*/
			//fatto escape del single quote '
			if (!ctype_space($val1) && $val1!=null && $val1!='')
				//echo "<option style=\"" .str_replace('\'', '&#39;',$name). "\" value=\"".str_replace('\'', '&#39;',$val3)."\" title='Module Description = ".str_replace('\'', '&#39;',$val2)." \nCourse Title = ".str_replace('\'', '&#39;',$val4)."'>".str_replace('\'', '&#39;',$val1)."</option>";
				echo "<option style='background-color:#FFFFFF' value=\"".str_replace('\'', '&#39;',$val3)."\" title='Module Description = ".str_replace('\'', '&#39;',$val2)." \nCourse Title = ".str_replace('\'', '&#39;',$val4)."'>".str_replace('\'', '&#39;',$val1)."</option>";
			$i=$i+1;
			}

			?>
			</select>
		 <br>
		 <p align=center> 
		 
		<?php
		echo "<input type='hidden' name='id_course' id='id_course' value='".$course."'/>";
		?>
		<button id="button" type="submit" onclick="changeButton()" class="next right" style="height: 30px; width: 110px">
		<?php echo translate_engine('Submit');?>
		</button>
		
		<!--
		<button type="cancel" class="close">
		Discard
		</button>
		-->
		</p>
	</div>
	
</form>

<?PHP
echo $OUTPUT->footer();
?>

  <script type="text/javascript" src="./Javascript/jquery-1.9.1.js"></script>
  <script type="text/javascript" src="./Javascript/jquery.tools.min.js"></script>
  <script type="text/javascript" src="./Javascript/jquery.multi-select.js" ></script>
  <script type="text/javascript" src="./Javascript/jquery-ui.min.js"></script> 
  <script type="text/javascript" src="./Javascript/jquery.multiselect.js" ></script>
	
<script>
  $(function() {
      var root = $("#wizard").scrollable();
  
      // some variables that we need
    var api = root.scrollable();

    api.onBeforeSeek(function(event, i) {
	                 // update status bar
	                 $("#status li").removeClass("active").eq(i).addClass("active");
					 
                      });
                         
   // if tab is pressed on the next button seek to next page
   
   root.find("button.next").keydown(function(e) {

		if (e.keyCode == 9) {

    // seeks to next tab by executing our validation routine
    api.next();
    e.preventDefault();
    }
    });
                           });

						   
</script>

</body>
</html>

