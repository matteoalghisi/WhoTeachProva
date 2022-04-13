<?php
require_once("../config.php");
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
echo $OUTPUT->header();
?>
  
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
</head>
<body>
      
    <font size=3>
    <div align="left">
        <a href="javascript:history.go(-1)">Go back</a>
    </div>
    </font>
    <div align="center">
  
    <?php
  
    //DETERMINA L'ID DELLA SEZIONE
    $sql="SELECT id, name FROM mdl_course_sections WHERE course = '".$_GET['id_course']."' AND section = '".$_GET['id_section']."'";
    $fields = $DB->get_records_sql($sql);
    foreach($fields as $field) {
        $section_name = $field->name;
        $id_section = $field->id; 
    }
  
    if ($section_name == NULL)
        echo '<br/><h1>'.'Topic '.$_GET['id_section'].' Metadata'.'</h1><br/>';
    else
        echo '<br/><h1>'.$section_name.' Metadata'.'</h1><br/>';
  
    print '<table border=1 bordercolor=#dddddd>';
    print '<td>';
  
    $current_property = array('category', 'min_age', 'max_age', 'keywords', 'difficulty', 'd_req_skill', 'd_acq_skill', 'language', 'format', 'resourcetype', 'time');
  
    //SCORRI TUTTI I TIPI DI METADATO
    for($i = 0; $i < count($current_property); $i++) {
  
        //STAMPA I METADATI ASSOCIATI AL TIPO DI METADATO CORRENTE
        $sql="SELECT value FROM mdl_metadata WHERE property = '".$current_property[$i]."' AND id_course_sections = '".$id_section."' AND id_course IS NULL";
        $fields = $DB->get_records_sql($sql);
        if($fields != NULL) {
            print '<table>';
                echo '<td><strong>'.convert_metadata($current_property[$i]).': </strong></td>';
                foreach($fields as $field) {
                    $value = $field->value;
                    echo '<td>'.$value.'</td>';
                }
            print '</table>';
        }
    }
    print '</td>';
    print '</table>';
    ?>
    </div>
        
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
              
            //ricerca del link della risorsa
            $cm->id = $r_id;
            $sql="SELECT module FROM mdl_course_modules WHERE id = " . $r_id;
            $fields = $DB->get_records_sql($sql);
            foreach ($fields as $field) {
                $tipoRisorsa = $field->module;
            }       
            $sql="SELECT name FROM mdl_modules WHERE id = '".$tipoRisorsa."'";
            $fields = $DB->get_records_sql($sql);
            foreach ($fields as $field) {
                $nomeTipoRisorsa = $field->name;
            }   
            $url = $CFG->wwwroot.'/mod/'.$nomeTipoRisorsa.'/view.php?id='.$cm->id;    
            echo '<img src="images/'.$file_name.'.svg"/>'." ". '<a href=' . $url . '>' . $r_name . ''  .'<br/><br/>';
  
            //STAMPA I METADATI ASSOCIATI ALLA RISORSA CORRENTE
            $sql="SELECT id_metadata, property, value FROM mdl_metadata WHERE id_resource = '".$r_id."'";
            $fields = $DB->get_records_sql($sql);
            print '<table border=1 bordercolor=#dddddd>';
            foreach($fields as $field) {
                $property = convert_metadata($field->property);  
                echo '<th>'.$property.'</th>';
            }
            print '<tr>';
            foreach($fields as $field) { 
                $value = $field->value;
                echo '<td>'.$value.'</td>';
            }
            print '</tr>';
            print '</table>';
            print '<br/><br/>';
        }
    }
  
    print '</td>';
    print '</tr>';
        print '</table>'; 
        ?>
    </div>
  </body>
</html>
  
<?php
echo $OUTPUT->footer();
?>
