<html>
<body>
<?php
require_once("../config.php");
require_once($CFG->dirroot. '/course/lib.php');
require_once($CFG->libdir. '/coursecatlib.php');
require_once('aux_functions.php');
 
GLOBAL $DB, $USER;
$userid=$USER->id;
$review = $_POST["id"];
$id_section = $_POST["id_section"];
$id_course = $_POST["id_course"];
 
if($review){
    //review assignment to an expert
    if($_POST["action"] == translate_review_element("Assign") || $_POST["action"] == translate_review_element("Remove")){
        $expert_id = $_POST["expert_id"];
        if($_POST["action"] == translate_review_element("Assign")){
            if($expert_id != null){
                for($i=0; $i<count($expert_id); $i++){
                    $sql="INSERT INTO sssecm_review (id_review_master, expert, assignmentDate)
                        VALUES ('" . $review . "', '" . $expert_id[$i] . "', CURDATE())";
                    $DB->execute($sql);
                }
            }
        }
        elseif($_POST["action"] == translate_review_element("Remove")){
            if($expert_id != null){
                for($i=0; $i<count($expert_id); $i++){
                    $sql = "SELECT sssecm_review_master.id_course_sections FROM sssecm_review_master WHERE id=" . $review;
                    $fields = $DB->get_records_sql($sql);
                    foreach ($fields as $field) {
                        $id_course_sections= $field->id_course_sections;
                    }
                     
                    $sql = "UPDATE mdl_metadata SET Value='yellow' WHERE Id_course_sections=" . $id_course_sections . " AND Property='status'";
                    $DB->execute($sql);
                     
                    $sql="DELETE FROM sssecm_review WHERE expert =" . $expert_id[$i];
                    $DB->execute($sql);
                }
            }
        }
    }
     
    //expert's review
    elseif($_POST["action"] == "Gold" || $_POST["action"] == "Accept" || $_POST["action"] == "Minor revision" || $_POST["action"] == "Major revision" || $_POST["action"] == "Reject" || $_POST["action"] == "Black"){
        //insert admin's review into sssecm_review
        /*if(is_siteadmin($userid)){
            $sql="INSERT INTO sssecm_review (id_review_master, expert, assignmentDate)
                VALUES ('" . $review . "', '" . $userid . "', CURDATE())";
            $DB->execute($sql);
        }*/
        $comment = $_POST["comment"];
        $rev = $_POST["rev"];
        if($_POST["action"] == "Accept")
            $status = 1;
        elseif($_POST["action"] == "Minor revision")
            $status = 2;
        elseif($_POST["action"] == "Major revision")
            $status = 3;
        elseif($_POST["action"] == "Reject")
            $status = 4;
        elseif($_POST["action"] == "Gold")
            $status = 1;
        elseif($_POST["action"] == "Black")
            $status = 4;
         
        if($comment != null){
            if($rev == "m"){
                if($status == 1){
                    $sql = "SELECT sssecm_review_master.id_course_sections FROM sssecm_review_master WHERE id=" . $review;
                    $fields = $DB->get_records_sql($sql);
                    foreach ($fields as $field) {
                        $id_course_sections= $field->id_course_sections;
                    }
                    if($_POST["action"] == "Accept")
                        $sql = "UPDATE mdl_metadata SET Value='green' WHERE Id_course_sections=" . $id_course_sections . " AND Property='status'";
                    elseif($_POST["action"] == "Gold")
                        $sql = "UPDATE mdl_metadata SET Value='gold' WHERE Id_course_sections=" . $id_course_sections . " AND Property='status'";
                    $DB->execute($sql);
                }
                elseif($status == 2 || $status == 3){
                    $sql = "SELECT sssecm_review_master.id_course_sections FROM sssecm_review_master WHERE id=" . $review;
                    $fields = $DB->get_records_sql($sql);
                    foreach ($fields as $field) {
                        $id_course_sections= $field->id_course_sections;
                    }
                    $sql = "UPDATE mdl_metadata SET Value='yellow' WHERE Id_course_sections=" . $id_course_sections . " AND Property='status'";
                    $DB->execute($sql);
                }
                elseif($status == 4){
                    $sql = "SELECT sssecm_review_master.id_course_sections FROM sssecm_review_master WHERE id=" . $review;
                    $fields = $DB->get_records_sql($sql);
                    foreach ($fields as $field) {
                        $id_course_sections= $field->id_course_sections;
                    }
                    if($_POST["action"] == "Reject")
                        $sql = "UPDATE mdl_metadata SET Value='red' WHERE Id_course_sections=" . $id_course_sections . " AND Property='status'";
                    elseif($_POST["action"] == "Black")
                        $sql = "UPDATE mdl_metadata SET Value='black' WHERE Id_course_sections=" . $id_course_sections . " AND Property='status'";
                    $DB->execute($sql);
                }
                $sql="UPDATE sssecm_review_master
                    SET completitionDate=CURDATE(), comments=\"" . $comment . "\", review_status=" . $status . "
                    WHERE id=" . $review;
            }
            elseif($rev == "e"){
                $sql="UPDATE sssecm_review
                    SET completitionDate=CURDATE(), comments=\"" . $comment . "\", review_status=" . $status . "
                    WHERE id_review_master=" . $review;
            }
            $DB->execute($sql);
            redirect($CFG->wwwroot . '/review/index.php');
        }
         
    }
    redirect($CFG->wwwroot . '/review/view.php?id=' . $review . '&id_section=' . $id_section . '&id_course=' . $id_course);
}
else{
    print_error('No permissions');
}
?>
</body>
</html>
