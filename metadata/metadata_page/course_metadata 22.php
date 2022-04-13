<?php
require_once('../../config.php');
require_once('aux_functions.php');

// Determina il fullname del corso
$sql = "SELECT fullname
 	FROM mdl_course 
 	WHERE id = '".$_GET['id_course']."'";
 		
$fields = $DB->get_records_sql($sql);

foreach($fields as $field) {
	$course_name = $field->fullname;
}

$PAGE->set_pagetype('site-index');
$PAGE->set_docs_path('');
//$PAGE->set_pagelayout('frontpage');
//$editing = $PAGE->user_is_editing();
require_login();
$PAGE->set_context(context_system::instance());
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_url($CFG->wwwroot."/metadata/metadata_page/course_metadata.php?id_course=".$_GET['id_course']."");//s_t_mod Hui
$courserenderer = $PAGE->get_renderer('core', 'course');
echo $OUTPUT->header();
?>

<!DOCTYPE HTML>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
  </head>
  <body>

    <?php
    // Stampa il tasto per tornare al corso
    $server_name = $_SERVER['SERVER_NAME'];
    print '<form action=http://'.$server_name.'/lms/course/view.php target="_top">';
    echo "<input type='hidden' name='id' value='".$_GET['id_course']."'/>";
    print "<input type='submit' align='center' value='".handle_translation('Return to course', 'Ritorna al corso', 'Derse geri dönün', 'Върни се към курса')."'>";
    print '</form>';
    ?>

    <div align="center">

	<?php
	echo '<br/><h1>'.$course_name.': Metadata<br/></h1><br/>';
	
	print '<table border=1 bordercolor=#dddddd id="metadata_table">';
	//print '<td>';
	
	// Keywords, minimal age, maximal age, category
	$current_property = array('keywords', 'min_age', 'max_age', 'category');

	//s_t_mod Hui
	// Scorre tutti i tipi di metadato
	for($i = 0; $i < count($current_property); $i++) {	
		$j=0;
		// Stampa i metadati associati al tipo di metadato corrente
		$sql = "SELECT value 
			FROM mdl_metadata 
			WHERE property = '".$current_property[$i]."' AND id_course = '".$_GET['id_course']."' AND id_course IS NOT NULL AND id_course_sections IS NULL AND id_resource IS NULL";
				
		$fields = $DB->get_records_sql($sql);
		
		if($fields != NULL) {
			//print '<table>';
				
				// convert_metadata in nett/mnett/course/aux_functions.php 
				echo '<tr><td><strong>'.convert_metadata($current_property[$i]).': </strong></td><td>';
				foreach($fields as $field) {
					if ($j>0 && $j<count($fields))
						print ", ";
					$value = translate_category($field->value);
					echo $value;
					$j++;
				}
			//print '</td></table>';
			print '</td></tr>';
		}
	}
	
	//---------------------------------------------------------------------------------------
	
	// $gradesRequested contiene i gradi (levels) delle Required Skills per il corso specificato
	$gradesRequested = array();
	$sql = "SELECT grade
		FROM mdl_metadata
		WHERE grade IS NOT NULL AND id_course = '".$_GET['id_course']."' AND id_course IS NOT NULL AND id_course_sections IS NULL AND id_resource IS NULL AND property = 's_req_skill'";
			
	$fields = $DB->get_recordset_sql($sql);	

	foreach($fields as $field) {
		$value = $field->grade;
		$gradesRequested[] = $value;
	}
	
	$sql = "SELECT value
		FROM mdl_metadata
		WHERE property = 's_req_skill' AND id_course = '".$_GET['id_course']."' AND id_course IS NOT NULL AND id_course_sections IS NULL AND id_resource IS NULL";
		
	//s_t_mod Hui
	$fields = $DB->get_records_sql($sql);
		if($fields != NULL) {
			//print '<table>';
				echo '<tr><td><strong>'.convert_metadata('s_req_skill').': </strong></td><td>';
				
				// $posArray1 determina l'indice della posizione corrente dell'array contente i gradi di Requested Skills   
				$posArray1 = 0;
				$j=0;
				foreach($fields as $field) {
					$value = $field->value;
					
					if ($j>0 && $j<count($fields))
						echo ", ";
					// Required Skill x (level: num)
					echo translate_skill($value).' ('.handle_translation("coverage level: ", "livello di copertura: ", "not: ", "oценки: ").$gradesRequested[$posArray1].')';
					$posArray1++;
					$j++;
				}
			print '</td></tr>';
		}
	//---------------------------------------------------------------------------------------------------------
	
	// $gradesAcquired contiene i gradi (levels) delle Acquired Skills per il corso specificato
	$gradesAcquired = array();
	$sql = "SELECT grade
		FROM mdl_metadata
		WHERE grade IS NOT NULL AND id_course = '".$_GET['id_course']."' AND id_course IS NOT NULL AND property = 's_acq_skill' AND id_course_sections IS NULL AND id_resource IS NULL";
			
	$fields = $DB->get_recordset_sql($sql);	
	
	foreach($fields as $field) {
		$value2 = $field->grade;
		$gradesAcquired[] = $value2;
	}
	
	$sql = "SELECT value
		FROM mdl_metadata 
		WHERE property = 's_acq_skill' AND id_course = '".$_GET['id_course']."' AND id_course IS NOT NULL AND id_course_sections IS NULL AND id_resource IS NULL";
			
	$fields = $DB->get_records_sql($sql);
	//s_t_mod Hui
		if($fields != NULL) {
			//print '<table>';
				echo '<tr><td><strong>'.convert_metadata('s_acq_skill').': </strong></td><td>';
				
				// $posArray2 determina l'indice della posizione corrente dell'array contente i gradi di Acquired Skills   
				$posArray2 = 0;
				$j=0;
				foreach($fields as $field) {
					$value2 = $field->value;
					
					if ($j>0 && $j<count($fields))
						echo ", ";
					// Required Skill x (level: num)
					echo translate_skill($value2).' ('.handle_translation("coverage level: ", "livello di copertura: ", "not: ", "oценки: "). $gradesAcquired[$posArray2].')';
					$posArray2++;
					$j++;
				}
			print '</td></tr>';
		}
	//------------------------------------------------------------------------------------------------------------
	$current_property2 = array('d_req_skills', 'd_acq_skills', 'language', 'format', 'resourcetype', 'time');

	//SCORRI TUTTI I TIPI DI METADATO
	for($i = 0; $i < count($current_property2); $i++) {

		//STAMPA I METADATI ASSOCIATI AL TIPO DI METADATO CORRENTE
		$sql = "SELECT Id_metadata, value 
			FROM mdl_metadata 
			WHERE property = '".$current_property2[$i]."' AND id_course = '".$_GET['id_course']."'";
				
		$fields = $DB->get_records_sql($sql);
	//s_t_mod Hui
		$j=0;
		if($fields != NULL) {
			//print '<table>';
				echo '<tr><td><strong>'.convert_metadata($current_property2[$i]).': </strong></td><td>';
				foreach($fields as $field) {
					$value = translate_language(translate_format(translate_type(translate_time($field->value))));
					
					if($value!=''){
						if ($j>0 && $j<count($fields))
							echo ", ";
						echo $value;
					}
					
					$j++;
				}
			print '</td></tr>';
		}
	}	
	//------------------------------------------------------------------------------------
	//print '</td>';
	print '</table>';
	
	?>
    </div>
  </body>
</html>

<?php
echo $OUTPUT->footer();
?>

<!-------------s_t_mod Hui----------------------->
<style>
#metadata_table {
  font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

#metadata_table td, #metadata_table th {
  border: 1px solid #ddd;
  padding: 8px;
}

#metadata_table tr:nth-child(even){background-color: #f2f2f2;}

#metadata_table tr:hover {background-color: #ddd;}

#metadata_table th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #4CAF50;
  color: white;
}
</style>
