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
 
//expert response
if($review){
    if($_POST["action"] == translate_review_element("Reject")){
        $sql="UPDATE sssecm_review
            SET acceptanceDate = null
            WHERE id_review_master=" . $review . " AND expert=" . $userid;
        $DB->execute($sql);
    }
    elseif($_POST["action"] == translate_review_element("Accept")){
        $sql="UPDATE sssecm_review
            SET acceptanceDate = CURDATE()
            WHERE id_review_master=" . $review . " AND expert=" . $userid;
        $DB->execute($sql);
    }
    redirect($CFG->wwwroot . '/review/index.php');
}
else{
    print_error('No permissions');
}
?>
</body>
</html>
