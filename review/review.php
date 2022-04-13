<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
        <link href="style.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
         
<?php
require_once("../config.php");
require_once($CFG->dirroot. '/course/lib.php');
require_once($CFG->libdir. '/coursecatlib.php');
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
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading($SITE->fullname);
$courserenderer = $PAGE->get_renderer('core', 'course');
echo $OUTPUT->header();
 
GLOBAL $DB, $USER;
$userid = $USER->id;
$rev = $_GET["rev"];
$id = $_GET["id"];
$table = $_GET["table"];
/*
//Admin table
if(is_siteadmin($userid)){
    echo "admin";
}
 
 
 
////////////////////////////END OF ADMIN TABLE//////////////////////////////////////////////
 
 
 
 
else{*/
     
 
?>
    <font size=3>
    <div align="left">
        <a href="javascript:history.go(-1)"><?php echo translate_review_element('Go back');?></a>
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
        echo '<br/><h1>'.'Topic '.$_GET['id_section'].'Metadata'.'</h1><br/>';
    else
        echo '<br/><h1>'.$section_name.translate_review_element(' Metadata').'</h1><br/>';
 
    print '<table border=1 bordercolor=#dddddd>';
    print '<td>';
 
    $current_property = array('category', 'min_age', 'max_age', 'keywords', 'difficulty', 'd_req_skill', 'd_acq_skill', 'language', 'format', 'resourcetype', 'time');
 
    //SCORRI TUTTI I TIPI DI METADATO
    for($i = 0; $i < count($current_property); $i++) {
 
        //STAMPA I METADATI ASSOCIATI AL TIPO DI METADATO CORRENTE
        $sql="SELECT value FROM mdl_metadata WHERE property = '".$current_property[$i]."' AND id_course_sections = '".$id_section."' AND id_course IS NOT NULL AND id_resource IS NULL";
        $fields = $DB->get_records_sql($sql);
        if($fields != NULL) {
            print '<table>';
                echo '<td><strong>'.convert_metadata($current_property[$i]).': </strong></td>';
                foreach($fields as $field) {
                    $value = $field->value;
                    echo '<td>'.translate_review_element($value).'</td>';
                }
            print '</table>';
        }
    }
    print '</td>';
    print '</table>';
    ?>
    </div>
       
    <div align="center">
     
    <h1><br/><?php echo translate_review_element('Resources: Metadata');?><br/><br/></h1>
 
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
                echo '<td>'.translate_review_element($value).'</td>';
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
     
         
 
    <div align="center">
            <form name="comments" action="process.php" method="post">
                <h2>
                    <?php echo translate_review_element('Your Review');?>
                </h2>
        <?php
        if($rev == "m"){
            $sql = "SELECT id_course_sections, comments, review_status
                    FROM sssecm_review_master
                    WHERE id=" . $id;
        }
        elseif($rev == "e"){
            $sql = "SELECT comments, review_status
                    FROM sssecm_review
                    WHERE id_review_master=" . $id . " AND expert=" . $userid;
        }
        $fields = $DB->get_records_sql($sql);
        foreach ($fields as $field) {
            $comments = $field->comments;
            $status = $field->review_status;
            $module = $field->id_course_sections;
        }
        if($rev == "m"){
            $sql = "SELECT Value FROM mdl_metadata WHERE Property='status' AND Id_course_sections=" . $module;
            $results = $DB->get_records_sql($sql);
            foreach ($results as $result){
                $value = $result->value;
            }
        }
        //echo "<input type=\"text\" name=\"comment\" value=\"" . $comments . "\">";
        echo "<textarea name=\"comment\" rows=\"6\" cols=\"60\">" . $comments . "</textarea>";
        echo "<input type=\"hidden\" name=\"id\" value=\"" . $id . "\">";
        echo "<input type=\"hidden\" name=\"rev\" value=\"" . $rev . "\">";
        ?>
                 
                <br />
                <table border="0">
                    <tr>
        <?php
        if($status == 1){
            if($rev == "m"){
                if($value == "gold"){
                    echo "<td><input disabled='disabled' type='submit' name='action' value='Gold'></td>";
                    echo "<td><input type='submit' name='action' value='Accept'></td>";
                }
                else{
                    echo "<td><input type='submit' name='action' value='Gold'></td>";
                    echo "<td><input disabled='disabled' type='submit' name='action' value='Accept'></td>";
                }
                echo "<td><input type='submit' name='action' value='Minor revision'></td>";
                echo "<td><input type='submit' name='action' value='Major revision'></td>";
                echo "<td><input type='submit' name='action' value='Reject'></td>";
                echo "<td><input type='submit' name='action' value='Black'></td>";
            }
            else{
                echo "<td><input disabled='disabled' type='submit' name='action' value='Accept'></td>";
                echo "<td><input type='submit' name='action' value='Minor revision'></td>";
                echo "<td><input type='submit' name='action' value='Major revision'></td>";
                echo "<td><input type='submit' name='action' value='Reject'></td>";
            }
        }
        elseif($status == 2){
            if($rev == "m"){
                echo "<td><input type='submit' name='action' value='Gold'></td>";
            }
            echo "<td><input type='submit' name='action' value='Accept'></td>";
            echo "<td><input disabled='disabled' type='submit' name='action' value='Minor revision'></td>";
            echo "<td><input type='submit' name='action' value='Major revision'></td>";
            echo "<td><input type='submit' name='action' value='Reject'></td>";
            if($rev == "m"){
                echo "<td><input type='submit' name='action' value='Black'></td>";
            }
        }
        elseif($status == 3){
            if($rev == "m"){
                echo "<td><input type='submit' name='action' value='Gold'></td>";
            }
            echo "<td><input type='submit' name='action' value='Accept'></td>";
            echo "<td><input type='submit' name='action' value='Minor revision'></td>";
            echo "<td><input disabled='disabled' type='submit' name='action' value='Major revision'></td>";
            echo "<td><input type='submit' name='action' value='Reject'></td>";
            if($rev == "m"){
                echo "<td><input type='submit' name='action' value='Black'></td>";
            }
        }
        elseif($status == 4){
            if($rev == "m"){
                echo "<td><input type='submit' name='action' value='Gold'></td>";
                echo "<td><input type='submit' name='action' value='Accept'></td>";
                echo "<td><input type='submit' name='action' value='Minor revision'></td>";
                echo "<td><input type='submit' name='action' value='Major revision'></td>";
                if($value == "black"){
                    echo "<td><input type='submit' name='action' value='Reject'></td>";
                    echo "<td><input disabled='disabled' type='submit' name='action' value='Black'></td>";
                }
                else{
                    echo "<td><input disabled='disabled' type='submit' name='action' value='Reject'></td>";
                    echo "<td><input type='submit' name='action' value='Black'></td>";
                }
            }
            else{
                echo "<td><input type='submit' name='action' value='Accept'></td>";
                echo "<td><input type='submit' name='action' value='Minor revision'></td>";
                echo "<td><input type='submit' name='action' value='Major revision'></td>";
                echo "<td><input disabled='disabled' type='submit' name='action' value='Reject'></td>";
            }
        }
        else{
            if($rev == "m"){
                echo "<td><input type='submit' name='action' value='Gold'></td>";
            }
            echo "<td><input type='submit' name='action' value='Accept'></td>";
            echo "<td><input type='submit' name='action' value='Minor revision'></td>";
            echo "<td><input type='submit' name='action' value='Major revision'></td>";
            echo "<td><input type='submit' name='action' value='Reject'></td>";
            if($rev == "m"){
                echo "<td><input type='submit' name='action' value='Black'></td>";
            }
        }
        ?>
                    </tr>
                </table>
            </form>
        </div>
 
 
        <?php
 
//}
 
echo $OUTPUT->footer();
?>
</body>
</html>
