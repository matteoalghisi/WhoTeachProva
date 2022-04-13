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
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading($SITE->fullname);
$courserenderer = $PAGE->get_renderer('core', 'course');
require_login();
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

	// Determina l'ID della sezione
	$sql = "SELECT id, name FROM mdl_course_sections WHERE course = '".$_GET['id_course']."' AND section = '".$_GET['id_section']."'";
	$fields = $DB->get_records_sql($sql);
	foreach($fields as $field) {
		$section_name = $field->name;
		$id_section = $field->id; 
	}

	if ($section_name == NULL)
        	echo '<br/><h1>'.'Topic '.$_GET['id_section'].' Metadata'.'</h1><br/>';
	else
		echo '<br/><h1>'.$section_name.': Metadata'.'</h1><br/>';
	
	print '<table border=1 bordercolor=#dddddd>';
	print '<td>';

	$current_property = array('min_age', 'max_age', 'category', 'keywords', 'difficulty');

	// Scorre tutti i tipi di metadato
	for($i = 0; $i < count($current_property); $i++) {

		// Stampa i metadati associati al tipo di metadato corrente
		$sql="SELECT value FROM mdl_metadata WHERE property = '".$current_property[$i]."' AND id_course IS NOT NULL AND id_course_sections = '".$id_section."' AND id_resource IS NULL";
		$fields = $DB->get_records_sql($sql);
		if($fields != NULL) {
			print '<table>';
				echo '<td><strong>'.convert_metadata($current_property[$i]).': </strong></td>';
				foreach($fields as $field) {
					$value = translate_category(translate_difficulty($field->value));
					echo '<td>'.$value.'</td>';
				}
			print '</table>';
		}
	}
	
	$gradesRequested = array();
	$sql = "SELECT grade
		FROM mdl_metadata
		WHERE grade IS NOT NULL AND property = 'd_req_skill' AND id_course IS NOT NULL AND id_course_sections = '".$id_section."' AND id_resource IS NULL";
			
	$fields = $DB->get_recordset_sql($sql);	

	foreach($fields as $field) {
		$value = $field->grade;
		$gradesRequested[] = $value;
	}
	
	$sql = "SELECT value
		FROM mdl_metadata
		WHERE property = 'd_req_skill' AND id_course IS NOT NULL AND id_course_sections = '".$id_section."'";
		
	$fields = $DB->get_records_sql($sql);
		if($fields != NULL) {
			print '<table>';
				echo '<td><strong>'.convert_metadata('d_req_skill').': </strong></td>';
				$posArray1 = 0;
				foreach($fields as $field) {
					$value = $field->value;
					echo '<td>'.translate_skill($value).' ('.handle_translation("coverage level: ", "livello di copertura: ", "not: ", "oценки: ").$gradesRequested[$posArray1].')</td>';
					$posArray1++;
				}
			print '</table>';
		}
		
	$gradesAcquired = array();
	$sql = "SELECT grade
		FROM mdl_metadata
		WHERE property = 'd_acq_skill' AND id_course IS NOT NULL AND id_course_sections = '".$id_section."' AND id_resource IS NULL AND grade IS NOT NULL";
			
	$fields = $DB->get_recordset_sql($sql);	

	foreach($fields as $field) {
		$value = $field->grade;
		$gradesAcquired[] = $value;
	}
	
	$sql = "SELECT value
		FROM mdl_metadata
		WHERE property = 'd_acq_skill' AND id_course IS NOT NULL AND id_course_sections = '".$id_section."' AND id_resource IS NULL";
		
	$fields = $DB->get_records_sql($sql);
		if($fields != NULL) {
			print '<table>';
				echo '<td><strong>'.convert_metadata('d_acq_skill').': </strong></td>';
				$posArray1 = 0;
				foreach($fields as $field) {
					$value = $field->value;
					echo '<td>'.translate_skill($value).' ('.handle_translation("coverage level: ", "livello di copertura: ", "not: ", "oценки: ").$gradesAcquired[$posArray1].')</td>';
					$posArray1++;
				}
			print '</table>';
		}	
		
	$current_property2 = array('language', 'format', 'resourcetype', 'time');

	// Scorre tutti i tipi di metadato
	for($i = 0; $i < count($current_property2); $i++) {

		// Stampa i metadati associati al tipo di metadato corrente
		$sql="SELECT value FROM mdl_metadata WHERE property = '".$current_property2[$i]."' AND id_course_sections = '".$id_section."'";
		$fields = $DB->get_records_sql($sql);
		if($fields != NULL) {
			print '<table>';
				echo '<td><strong>'.convert_metadata($current_property2[$i]).': </strong></td>';
				foreach($fields as $field) {
					$value = translate_language(translate_format(translate_type(translate_time($field->value))));
					echo '<td>'.$value.'</td>';
				}
			print '</table>';
		}
	}	
	
	print '</td>';
	print '</table>';
	?>
    </div>
  </body>
</html>

<?php
echo $OUTPUT->footer();
?>