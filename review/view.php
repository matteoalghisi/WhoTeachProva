<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
        <link href="style.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
         
<?php
require_once("../config.php");
require_once('aux_functions.php');
require_once($CFG->dirroot. '/course/lib.php');
require_once($CFG->libdir. '/coursecatlib.php');
require_once('aux_functions.php');
$PAGE->set_pagetype('site-index');
$PAGE->set_docs_path('');
//$PAGE->set_pagelayout('frontpage');
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading($SITE->fullname);
$courserenderer = $PAGE->get_renderer('core', 'course');
echo $OUTPUT->header();
 
GLOBAL $DB, $USER;
$userid = $USER->id;
$id = $_GET["id"];
$tab = $_GET["tab"];
 
     
echo '<div align="left"><font size=3><a href="index.php">'.translate_review_element("Go back").'</a></font></div>';
echo '<div align="center">';
 
 
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
print '</div>';
 
 
 
if(is_siteadmin($userid)){
    //Admin table
    $sql = "SELECT mdl_context.id
            FROM sssecm_review_master, mdl_course_sections, mdl_context, mdl_course
            WHERE sssecm_review_master.id= " . $id . " AND sssecm_review_master.id_course_sections=mdl_course_sections.id AND mdl_course_sections.course=mdl_course.id
            AND mdl_course.category = mdl_context.instanceid AND mdl_context.contextlevel =40";
    $fields = $DB->get_records_sql($sql);
    foreach ($fields as $field) {
        $contextid= $field->id;
    }
}
else{
    //Master table
    //getting module context
    $sql = "SELECT mdl_context.id
            FROM sssecm_review_master, mdl_course_sections, mdl_course, mdl_context
            WHERE sssecm_review_master.id =" . $id . " AND sssecm_review_master.id_course_sections = mdl_course_sections.id AND mdl_course_sections.course = mdl_course.id AND mdl_course.category = mdl_context.instanceid AND mdl_context.contextlevel=40";
    $fields = $DB->get_records_sql($sql);
    foreach ($fields as $field) {
        $contextid_module = $field->id;
    }
     
    $sql="SELECT  mdl_user.id
            FROM mdl_role, mdl_role_assignments, mdl_user
            WHERE mdl_role_assignments.roleid = mdl_role.id AND mdl_role.shortname=\"master\" AND mdl_user.id=mdl_role_assignments.userid AND mdl_role_assignments.contextid=" . $contextid_module;
    $fields = $DB->get_records_sql($sql);
 
    foreach ($fields as $field) {
        if($userid == $field->id){
            $contextid = $contextid_module;
        }
    }
}
if($contextid){
    ?>
         
    <table align="center" border="0">
        <tr>
            <td>
                <form name="assign" action="process.php" method="post">
                    <table class="assigntable">
                        <tr>
                            <th><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Experts Available');?></font></th>
                        </tr>
 
    <?php
    //select of the experts
    $sql="SELECT mdl_user.username, mdl_user.id
        FROM mdl_role, mdl_role_assignments, mdl_user
        WHERE mdl_user.id = mdl_role_assignments.userid AND mdl_role_assignments.roleid = mdl_role.id AND
            mdl_role.shortname=\"expert\" AND mdl_role_assignments.contextid =" . $contextid;
    $results = $DB->get_records_sql($sql);
    if($results == null){
        ?>
 
                        <tr>
                            <td>
                                <select name="expert_id" size="5" multiple="multiple" style="width:200px">
                                    <option>
                                        No experts
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input disabled="disabled" type="submit" name="action" value="Assign">
                            </td>
                        </tr>
        <?php
    }else{
        ?>
 
                        <tr>
                            <td>
                                <select name="expert_id[]" size="5" multiple="multiple" style="width:200px">
        <?php
        $count = 0;
        foreach ($results as $result) {
            $expert_username= $result->username;
            $expert_id= $result->id;
            $sql="SELECT sssecm_review.expert
                    FROM sssecm_review
                    WHERE expert =" . $expert_id . " AND id_review_master=" . $id;
            $results2 = $DB->get_records_sql($sql);
            if(!$results2){
                $count++;
 
                echo "<option value=\"" . $expert_id . "\">" . $expert_username . " </option>";
            }
        }
        if($count==0){
            ?>
                                    <option>
                                            <?php echo translate_review_element('No experts to be assigned');?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            <?php 
                                echo '<input disabled="disabled" type="submit" name="action" value="'.translate_review_element("Assign").'">'; 
                            ?>
                            </td>
                        </tr>
            <?php
        }else{
            ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
            <?php
            echo "<input type=\"hidden\" name=\"id\" value=\"" . $id . "\">";
            echo "<input type=\"hidden\" name=\"id_section\" value=\"" . $_GET['id_section'] . "\">";
            echo "<input type=\"hidden\" name=\"id_course\" value=\"" . $_GET['id_course'] . "\">";
            
                                echo '<input type="submit" name="action" value="'.translate_review_element("Assign").'">';
                                ?>
                            </td>
                        </tr>
            <?php   
        }
    }
    ?>
                    </table>
                </form>
            </td>
     
            <td>
                <form name="remove" action="process.php" method="post">
                    <table class="assigntable">
                        <tr>
                            <th><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Experts assigned');?></font></th>
                        </tr>
 
    <?php
    //select of the experts
    $sql="SELECT sssecm_review.expert
            FROM sssecm_review
            WHERE id_review_master=" . $id;
    $results = $DB->get_records_sql($sql);
    if($results == null){
        ?>
 
                        <tr>
                            <td>
                                <select name="expert_id" size="5" multiple="multiple" style="width:200px">
                                    <option>
                                        <?php echo translate_review_element('No experts');?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            	<?php
                                echo '<input disabled="disabled" type="submit" name="action" value="'.translate_review_element("Remove").'">';
                                ?>
                            </td>
                        </tr>
 
        <?php
    }else{
        ?>
                        <tr>
                            <td>
                                <select name="expert_id[]" size="5" multiple="multiple" style="width:200px">
        <?php
        foreach ($results as $result) {
            $expert_id= $result->expert;
            $sql = "SELECT mdl_user.username
                FROM mdl_user
                WHERE mdl_user.id = " . $expert_id;
            $results2 = $DB->get_records_sql($sql);
            foreach ($results2 as $res) {
                $expert_username= $res->username;
 
                echo "<option value=\"" . $expert_id . "\">" . $expert_username . " </option>";
            }
        }
    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
    <?php
    echo "<input type=\"hidden\" name=\"id\" value=\"" . $id . "\">";
    echo "<input type=\"hidden\" name=\"id_section\" value=\"" . $_GET['id_section'] . "\">";
    echo "<input type=\"hidden\" name=\"id_course\" value=\"" . $_GET['id_course'] . "\">";
    
    echo '<input type="submit" name="action" value='.translate_review_element("Remove").'>';
    ?>
                            </td>
                        </tr>
    <?php
    }
    ?>
                    </table>
                </form>
            </td>
        </tr>
    </table>
    <br />
    <br />
    <br />
    <br />
    <?php
    $sql="SELECT expert, assignmentDate, acceptanceDate, completitionDate, comments, review_status
            FROM sssecm_review
            WHERE id_review_master =" . $id;
    $count = 0;
    $results = $DB->get_records_sql($sql);
    if($results != null){
        ?>
    <div align="center">
         
        <?php
        foreach ($results as $result) {
            $expert_id= $result->expert;
            $assignment= $result->assignmentdate;
            $acceptance= $result->acceptancedate;
            $completition= $result->completitiondate;
            $comments= $result->comments;
            $review_status= $result->review_status;
             
             
            $sql="SELECT username
                    FROM mdl_user
                    WHERE id =" . $expert_id;
            $query = $DB->get_records_sql($sql);
            foreach ($query as $q){
                $username= $q->username;
            }
             
            echo "<h3>".translate_review_element('Review of ') . $username . "</h3>";
            ?>
 
        <table class="hovertable">
            <tr>
                <th>
                    <?php echo translate_review_element('Invitation Accepted');?>
                </th>
                <th>
                    <?php echo translate_review_element('Review Completed');?>
                </th>
                <th>
                    <?php echo translate_review_element('Decision');?>
                </th>
            </tr>
            <tr>
                <td>
            <?php
            echo $acceptance;
            ?>
                </td>            
                <td>
            <?php
            echo $completition;
            ?>
                </td>
                <td>
            <?php
            if($review_status == 0)
                echo translate_review_element("No decision");
            elseif($review_status == 1)
                echo "Accepted";
            elseif($review_status == 2)
                echo "Minor revision";
            elseif($review_status == 3)
                echo "Major revision";
            elseif($review_status == 4)
                echo "Rejected";
            ?>
                </td>        
            </tr>
        </table>
            <?php
            echo "<textarea rows='5' cols='60' readonly>" . $comments . "</textarea>";
            echo "<br /><br /><br /><br /><br />";
        }
        ?>
    </div>
        <?php
    }
}
else{
        print_error('No permissions');
}
 
echo $OUTPUT->footer();
?>
</body>
</html>
