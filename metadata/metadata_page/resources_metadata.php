<?php
require_once('../../config.php');
require_once('aux_functions.php');

//DETERMINA IL FULLNAME DEL CORSO
$sql="SELECT fullname FROM mdl_course WHERE id = '".$_GET['id_course']."'";
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
    //STAMPA IL TASTO PER TORNARE AL CORSO
    $server_name = $_SERVER['SERVER_NAME'];
    print '<form action=http://'.$server_name.'/lms/course/view.php target="_top">';
    echo "<input type='hidden' name='id' value='".$_GET['id_course']."'/>";
    print "<input type='submit' align='center' value='".handle_translation('Return to course', 'Ritorna al corso', 'Derse geri dönün', 'Върни се към курса')."'>";
    print '</form>';
    ?>

    <div align="center">
	
	<h1><br/>Resources: Metadata<br/><br/></h1>

	<?php

	//DETERMINA L'ID DELLA SEZIONE
	$sql="SELECT id, name FROM mdl_course_sections WHERE course = '".$_GET['id_course']."' AND section = '".$_GET['id_section']."'";
	$fields = $DB->get_records_sql($sql);
	foreach($fields as $field) {
		$id_section = $field->id; 
	}

	print '<table>';
	print '<tr>';
	print '<td>';

	//DETERMINA TUTTE LE RISORSE ASSOCIATE ALLA SEZIONE
	$sql="SELECT DISTINCT id_resource FROM mdl_metadata WHERE id_course_sections = '".$id_section."' ORDER BY id_resource ASC";
	$fields = $DB->get_records_sql($sql);
	foreach($fields as $field) {
		$r_id = $field->id_resource;

		//STAMPA IL LOGO DELLA RISORSA CORRENTE
		$sql="SELECT module, instance FROM mdl_course_modules WHERE id = '".$r_id."'";
		$fields = $DB->get_records_sql($sql);

		foreach($fields as $field) {
			$r_type = $field->module;
			$instance = $field->instance;
			$file_name = find_image($r_type);
			
			//DETERMINA IL NOME DELLA RISORSA CORRENTE
			$sql="SELECT name FROM mdl_$file_name WHERE id = '".$instance."'";
			$fields = $DB->get_records_sql($sql);
			foreach($fields as $field) {
				$r_name = $field->name;
			}
			
			echo '<img src="images/'.$file_name.'.svg"/>'." ".$r_name.'<br/><br/>';

			//STAMPA I METADATI ASSOCIATI ALLA RISORSA CORRENTE
			$sql="SELECT id_metadata, property, value FROM mdl_metadata WHERE id_resource = '".$r_id."'";
			$fields = $DB->get_records_sql($sql);
			print '<table border=1 bordercolor=#dddddd CELLPADDING="0">';
            $a = 0;
            $b = 10;

            foreach($fields as $field) {
                $a++;
                if($a>$b){
                    $a= 0;
                    print '<table border=1 bordercolor=#dddddd CELLPADDING="0">';
                    print '<tr>';
                    continue;
                }else{
     			$property = convert_metadata($field->property);
                	$value = $field->value;
			echo '<td valign = "100">'.'<b>'.$property.'</b>'.'</br>'.$value.'</td>';

   }
}

			print '</tr>';
			print '</table>';
			print '<br/><br/>';
		}
	}

	print '</td>';
	print '</tr>';
        print '</table>';

        /**sql="SELECT id_metadata, property, value FROM mdl_metadata WHERE id_resource = '".$r_id."'";
			$fields = $DB->get_records_sql($sql);
			print '<table border=1 bordercolor=#dddddd>';
            $a = 0;
            $b = 10;
            //do{
                foreach($fields as $field) {
                echo $a;
                 $a++;
                if($a>$b){
                    //echo "ciao";
                    $a= 0;
                    print '<table border=1 bordercolor=#dddddd>';
                    continue;
                }else{
				$property = convert_metadata($field->property);
				echo '<th>'.$property.'</th>';

			}
}*/
        ?>
    </div>
  </body>
</html>

<?php
echo $OUTPUT->footer();
?>




