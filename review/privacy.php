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
require_once('aux_functions.php');
$PAGE->set_pagetype('site-index');
$PAGE->set_docs_path('');
//$PAGE->set_pagelayout('frontpage');
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading($SITE->fullname);
$courserenderer = $PAGE->get_renderer('core', 'course');
echo $OUTPUT->header();
 
GLOBAL $DB, $USER;
$userid=$USER->id;
$table = $_GET["table"];
$tab = $_GET["tab"];
 
//Admin table
if(is_siteadmin($userid)){
        ?>
         
    <div align="center">
            <table border=0>
                <tr>
                    <td>
                       <h2>Privacy</h2>
                    </td>

</tr>
<tr><td>
<p>
We value your privacy and will take all reasonable steps to protect your personal information. We do not share or distribute your personal information (including email address) to any third party, but it may be accessible to those volunteers and staff who administer the site and infrastructure. We may be required to share stored personal information by law.<br/>

All information that you disclose in your public profile, in forum posts, comments, tracker, or other public portions of the whoteach.eu sites becomes public information. All content is made available under the GNU General Public License, unless otherwise stated.<br/>

IP addresses, URLs visited, and related information is recorded for all site visitors for the purpose of site traffic analytics and captured as part of normal operation in our server logs. Cookies are used to track logins, sessions, and collect anonymous traffic data.<br/>
       </td>
                </tr>
            </table>

         
        <br />
        <br />
        <form name="review_master" action="review.php" method="get">
            <table class="hovertable">
        <?php
        if($tab == translate_review_element("Submitted")){
            ?>
                <tr>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Module');?></font></th>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Course');?></font></th>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Assign the module to an expert');?></font></th>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Publish the module');?></font></th>
                </tr>
 
            <?php
 
            //select of the modules to be reviewed
            $sql="SELECT sssecm_review_master.id, sssecm_review_master.id_course_sections, mdl_course_sections.name, mdl_course.id AS id_course, mdl_course_sections.section, mdl_course.fullname
                    FROM sssecm_review_master, mdl_course_sections, mdl_course, mdl_context
                    WHERE sssecm_review_master.id_course_sections=mdl_course_sections.id AND mdl_course_sections.course=mdl_course.id AND
                        sssecm_review_master.completitionDate IS NULL AND sssecm_review_master.submissionDate IS NOT NULL AND
                        sssecm_review_master.id NOT IN (SELECT id_review_master FROM sssecm_review)
                    GROUP BY sssecm_review_master.id
                    ORDER BY mdl_course.fullname";
             
            $fields = $DB->get_records_sql($sql);
            if($fields == null){
            ?>
 
                <tr>
                    <td>
                        <?php echo translate_review_element('No modules');?>
                    </td>
                    <td />
                    <td />
                    <td />
                </tr>
 
            <?php
            }
            else{
                foreach ($fields as $field) {
                    $id = $field->id;
                    $id_module = $field->id_course_sections;
                    $id_course = $field->id_course;
                    $name = $field->name;
                    $section = $field->section;
                    $fullname = $field->fullname;
                    ?>
 
                <tr>
                    <td>
                    <?php 
                    echo "<a href=\"page.php?id_section=" . $section . "&id_course=" . $id_course . "\">" . $name . "</a>";
                    ?>
                    </td>
                    <td>
                    <?php 
                    echo $fullname;
                    ?>
                    </td>
                    <td>
                    <?php
                    echo "<a href=\"view.php?id=" . $id . "&id_section=" . $section . "&id_course=" . $id_course . "\">".translate_review_element('Assign')."</a>";
                    ?>
                    </td>
                    <td>
                    <?php
                    echo "<a href=\"review.php?id=" . $id . "&id_section=" . $section . "&id_course=" . $id_course . "&rev=m\">".translate_review_element('Publish')."</a>";
                    ?>
                    </td>
                </tr>
 
                    <?php
                }
            }
        }
        elseif($tab == translate_review_element("Assigned")){
            ?>
                <tr>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Module');?></font></th>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Course');?></font></th>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('View expert reviews');?></font></th>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Publish the module');?></font></th>
                </tr>
 
            <?php
 
            //select of the modules to be reviewed
            $sql="SELECT sssecm_review_master.id, sssecm_review_master.id_course_sections, mdl_course_sections.name, mdl_course.id AS id_course, mdl_course_sections.section, mdl_course.fullname
                    FROM sssecm_review_master, mdl_course_sections, mdl_course, mdl_context, sssecm_review
                    WHERE sssecm_review_master.id_course_sections=mdl_course_sections.id AND mdl_course_sections.course=mdl_course.id AND
                    sssecm_review_master.id=sssecm_review.id_review_master AND sssecm_review_master.completitionDate IS NULL";
            $fields = $DB->get_records_sql($sql);
            if($fields == null){
                ?>
 
                <tr>
                    <td>
                        <?php echo translate_review_element('No modules');?>
                    </td>
                    <td />
                    <td />
                    <td />
                </tr>
 
                <?php
            }
            else{
                foreach ($fields as $field) {
                    $id = $field->id;
                    $id_module = $field->id_course_sections;
                    $id_course = $field->id_course;
                    $name = $field->name;
                    $section = $field->section;
                    $fullname = $field->fullname;
                    ?>
 
                <tr>
                    <td>
                    <?php 
                    echo "<a href=\"page.php?id_section=" . $section . "&id_course=" . $id_course . "\">" . $name . "</a>";
                    ?>
                    </td>
                    <td>
                    <?php 
                    echo $fullname;
                    ?>
                    </td>
                    <td>
                    <?php
                    echo "<a href=\"view.php?id=" . $id . "&id_section=" . $section . "&id_course=" . $id_course . "\">".translate_review_element('View')."</a>";
                    ?>
                    </td>
                    <td>
                    <?php
                    echo "<a href=\"review.php?id=" . $id . "&id_section=" . $section . "&id_course=" . $id_course . "&rev=m\">".translate_review_element('Publish')."</a>";
                    ?>
                    </td>
                </tr>
 
                    <?php
                }
            }
        }
        elseif($tab == translate_review_element("Published")){
            ?>
                <tr>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Module');?></font></th>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Course');?></font></th>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Completed');?></font></th>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Status');?></font></th>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('View expert reviews');?></font></th>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('View your review');?></font></th>
                </tr>
 
            <?php
 
            //select of the modules to be reviewed
            $sql="SELECT sssecm_review_master.id, sssecm_review_master.id_course_sections, mdl_course_sections.name, mdl_course.id AS id_course, mdl_course_sections.section, mdl_course.fullname
                    FROM sssecm_review_master, mdl_course_sections, mdl_course, mdl_context
                    WHERE sssecm_review_master.id_course_sections=mdl_course_sections.id AND mdl_course_sections.course=mdl_course.id AND
                    sssecm_review_master.completitionDate IS NOT NULL ";
            $fields = $DB->get_records_sql($sql);
            if($fields == null){
                ?>
 
                <tr>
                    <td>
                        No modules
                    </td>
                    <td />
                    <td />
                    <td />
                    <td />
                    <td />
                </tr>
 
                <?php
            }
            else{
                foreach ($fields as $field) {
                    $id = $field->id;
                    $id_module = $field->id_course_sections;
                    $id_course = $field->id_course;
                    $name = $field->name;
                    $section = $field->section;
                    $fullname = $field->fullname;
 
                    //select of the review status
                    $sql="SELECT sssecm_review_master.completitionDate, sssecm_review_master.review_status
                            FROM sssecm_review_master
                            WHERE id=" . $id;
                    $results = $DB->get_records_sql($sql);
                    if($results){
                        foreach ($results as $result) {
                            $completition = $result->completitiondate;
                            $review_status = $result->review_status;
                        }
                    }
                    //select of the color
                    $sql="SELECT mdl_metadata.Value FROM sssecm_review_master, mdl_metadata WHERE sssecm_review_master.id=" . $id . " AND sssecm_review_master.id_course_sections=mdl_metadata.Id_course_sections AND mdl_metadata.Property='status'";
                    $query = $DB->get_records_sql($sql);
                    foreach ($query as $q){
                        $value= $q->value;
                    }
                    ?>
 
                <tr>
                    <td>
                    <?php 
                    echo "<a href=\"page.php?id_section=" . $section . "&id_course=" . $id_course . "\">" . $name . "</a>";
                    ?>
                    </td>
                    <td>
                    <?php 
                    echo $fullname;
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
                        echo "No decision";
                    elseif($review_status == 1){
                        if($value == "gold")
                            echo "Approved (Gold)";
                        else
                            echo translate_review_element("Approved");
                    }
                    elseif($review_status == 2)
                        echo "Minor revision";
                    elseif($review_status == 3)
                        echo "Major revision";
                    elseif($review_status == 4){
                        if($value == "black")
                            echo "Approved (Black)";
                        else
                            echo "Rejected";
                    }
                    ?>
                        </td>
                    <td>
                    <?php
                    echo "<a href=\"view.php?id=" . $id . "&id_section=" . $section . "&id_course=" . $id_course . "\">".translate_review_element('View')."</a>";
                    ?>
                    </td>
                    <td>
                    <?php
                    echo "<a href=\"review.php?id=" . $id . "&id_section=" . $section . "&id_course=" . $id_course . "&rev=m\">".translate_review_element('View')."</a>";
                    ?>
                    </td>
                </tr>
 
                    <?php
                }
            }
        }
        ?>
 
            </table>
        </form>
    </div>
        <?php
}
 
 
 
////////////////////////////END OF ADMIN TABLE//////////////////////////////////////////////
 
 
 
 
else{
 
     
    //Master table
    $sql="SELECT DISTINCT mdl_role_assignments.contextid
            FROM mdl_role, mdl_role_assignments, mdl_user
            WHERE mdl_role_assignments.roleid = mdl_role.id AND mdl_role.shortname=\"master\" AND mdl_user.id=mdl_role_assignments.userid AND mdl_user.id=" . $userid;
    $objects = $DB->get_records_sql($sql);
    $i = 0;
    foreach ($objects as $object) {
        $contextid[$i] = $object->contextid;
        $i++;
    }
    if(isset($contextid)){
        ?>
     
    <div align="center">
        <br />
        <br />
        <form name="choose" action="index.php" method="get">
            <table border=0>
                <tr>
                    <td>
                        <?php echo translate_review_element('View modules:');?>
                    </td>
        <?php
        if($tab == translate_review_element("Submitted")){
            echo "<td><input disabled='disabled' type='submit' name='tab' value='".translate_review_element('Submitted')."'></td>";
            echo "<td><input type='submit' name='tab' value='".translate_review_element('Assigned')."'></td>";
            echo "<td><input type='submit' name='tab' value='".translate_review_element('Published')."'></td>";
        }
        elseif($tab == translate_review_element("Assigned")){
            echo "<td><input type='submit' name='tab' value='".translate_review_element('Submitted')."'></td>";
            echo "<td><input disabled='disabled' type='submit' name='tab' value='".translate_review_element('Assigned')."'></td>";
            echo "<td><input type='submit' name='tab' value='".translate_review_element('Published')."'></td>";
        }
        elseif($tab == translate_review_element("Published")){
            echo "<td><input type='submit' name='tab' value='".translate_review_element('Submitted')."'></td>";
            echo "<td><input type='submit' name='tab' value='".translate_review_element('Assigned')."'></td>";
            echo "<td><input disabled='disabled' type='submit' name='tab' value='".translate_review_element('Published')."'></td>";
        }
        else{
            echo "<td><input type='submit' name='tab' value='".translate_review_element('Submitted')."'></td>";
            echo "<td><input type='submit' name='tab' value='".translate_review_element('Assigned')."'></td>";
            echo "<td><input type='submit' name='tab' value='".translate_review_element('Published')."'></td>";
        }
        ?>
                </tr>
            </table>
        </form>
         
        <br />
        <br />
        <form name="review_master" action="review.php" method="get">
            <table class="hovertable">
        <?php
        if($tab == translate_review_element("Submitted")){
            ?>
                <tr>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Module');?></font></th>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Course');?></font></th>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Assign the module to an expert');?></font></th>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Publish the module');?></font></th>
                </tr>
 
            <?php
 
            //select of the modules to be reviewed
            if(count($contextid) == 1){
                $sql="SELECT sssecm_review_master.id, sssecm_review_master.id_course_sections, mdl_course_sections.name, mdl_course.id AS id_course, mdl_course_sections.section, mdl_course.fullname
                    FROM sssecm_review_master, mdl_course_sections, mdl_course, mdl_context
                    WHERE sssecm_review_master.id_course_sections=mdl_course_sections.id AND mdl_course_sections.course=mdl_course.id AND
                        mdl_course.category=mdl_context.instanceid AND mdl_context.id=" . $contextid[0] . " AND sssecm_review_master.completitionDate IS NULL AND sssecm_review_master.submissionDate IS NOT NULL AND sssecm_review_master.id NOT IN (SELECT id_review_master FROM sssecm_review)";
            }
            elseif(count($contextid) > 1){
                $i = 1;
                $sql ="SELECT sssecm_review_master.id, sssecm_review_master.id_course_sections, mdl_course_sections.name, mdl_course.id AS id_course, mdl_course_sections.section, mdl_course.fullname
                    FROM sssecm_review_master, mdl_course_sections, mdl_course, mdl_context
                    WHERE sssecm_review_master.id_course_sections=mdl_course_sections.id AND mdl_course_sections.course=mdl_course.id AND
                        mdl_course.category=mdl_context.instanceid AND mdl_context.id=" . $contextid[0] . " AND sssecm_review_master.completitionDate IS NULL AND sssecm_review_master.submissionDate IS NOT NULL AND sssecm_review_master.id NOT IN (SELECT id_review_master FROM sssecm_review)";
                do{
                    $sql = $sql . " UNION
                        SELECT sssecm_review_master.id, sssecm_review_master.id_course_sections, mdl_course_sections.name, mdl_course.id AS id_course, mdl_course_sections.section, mdl_course.fullname
                        FROM sssecm_review_master, mdl_course_sections, mdl_course, mdl_context
                        WHERE sssecm_review_master.id_course_sections=mdl_course_sections.id AND mdl_course_sections.course=mdl_course.id AND
                            mdl_course.category=mdl_context.instanceid AND mdl_context.id=" . $contextid[$i] . " AND sssecm_review_master.completitionDate IS NULL AND sssecm_review_master.submissionDate IS NOT NULL AND sssecm_review_master.id NOT IN (SELECT id_review_master FROM sssecm_review)";
                    $i++;
                } while($i < count($contextid));
            }
            $fields = $DB->get_records_sql($sql);
            if($fields == null){
            ?>
 
                <tr>
                    <td>
                        <?php echo translate_review_element('No modules');?>
                    </td>
                    <td />
                    <td />
                    <td />
                </tr>
 
            <?php
            }
            else{
                foreach ($fields as $field) {
                    $id = $field->id;
                    $id_module = $field->id_course_sections;
                    $id_course = $field->id_course;
                    $name = $field->name;
                    $section = $field->section;
                    $fullname=$field->fullname;
                    ?>
 
                <tr>
                    <td>
                    <?php 
                    echo "<a href=\"page.php?id_section=" . $section . "&id_course=" . $id_course . "\">" . $name . "</a>";
                    ?>
                    </td>
                    <td>
                    <?php 
                    echo $fullname;
                    ?>
                    </td>
                    <td>
                    <?php
                    echo "<a href=\"view.php?id=" . $id . "&id_section=" . $section . "&id_course=" . $id_course . "\">".translate_review_element('Assign')."</a>";
                    ?>
                    </td>
                    <td>
                    <?php
                    echo "<a href=\"review.php?id=" . $id . "&id_section=" . $section . "&id_course=" . $id_course . "&rev=m\">".translate_review_element('Publish')."</a>";
                    ?>
                    </td>
                </tr>
 
                    <?php
                }
            }
        }
        elseif($tab == translate_review_element("Assigned")){
            ?>
                <tr>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Module');?></font></th>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Course');?></font></th>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('View expert reviews');?></font></th>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Publish the module');?></font></th>
                </tr>
 
            <?php
 
            //select of the modules to be reviewed
            if(count($contextid) == 1){
                $sql="SELECT sssecm_review_master.id, sssecm_review_master.id_course_sections, mdl_course_sections.name, mdl_course.id AS id_course, mdl_course_sections.section, mdl_course.fullname
                    FROM sssecm_review_master, mdl_course_sections, mdl_course, mdl_context, sssecm_review
                    WHERE sssecm_review_master.id_course_sections=mdl_course_sections.id AND mdl_course_sections.course=mdl_course.id AND
                        mdl_course.category=mdl_context.instanceid AND mdl_context.id=" . $contextid[0] . " AND sssecm_review_master.id=sssecm_review.id_review_master AND sssecm_review_master.completitionDate IS NULL";
            }
            elseif(count($contextid) > 1){
                $i = 1;
                $sql ="SELECT sssecm_review_master.id, sssecm_review_master.id_course_sections, mdl_course_sections.name, mdl_course.id AS id_course, mdl_course_sections.section, mdl_course.fullname
                    FROM sssecm_review_master, mdl_course_sections, mdl_course, mdl_context, sssecm_review
                    WHERE sssecm_review_master.id_course_sections=mdl_course_sections.id AND mdl_course_sections.course=mdl_course.id AND
                        mdl_course.category=mdl_context.instanceid AND mdl_context.id=" . $contextid[0] . " AND sssecm_review_master.id=sssecm_review.id_review_master AND sssecm_review_master.completitionDate IS NULL";
                do{
                    $sql = $sql . " UNION
                        SELECT sssecm_review_master.id, sssecm_review_master.id_course_sections, mdl_course_sections.name, mdl_course.id AS id_course, mdl_course_sections.section, mdl_course.fullname
                        FROM sssecm_review_master, mdl_course_sections, mdl_course, mdl_context, sssecm_review
                        WHERE sssecm_review_master.id_course_sections=mdl_course_sections.id AND mdl_course_sections.course=mdl_course.id AND
                            mdl_course.category=mdl_context.instanceid AND mdl_context.id=" . $contextid[$i] . " AND sssecm_review_master.id=sssecm_review.id_review_master AND sssecm_review_master.completitionDate IS NULL";
                    $i++;
                } while($i < count($contextid));
            }
             
            $fields = $DB->get_records_sql($sql);
            if($fields == null){
                ?>
 
                <tr>
                    <td>
                        <?php echo translate_review_element('No modules');?>
                    </td>
                    <td />
                    <td />
                    <td />
                </tr>
 
                <?php
            }
            else{
                foreach ($fields as $field) {
                    $id = $field->id;
                    $id_module = $field->id_course_sections;
                    $id_course = $field->id_course;
                    $name = $field->name;
                    $section = $field->section;
                    $fullname=$field->fullname;
                    ?>
 
                <tr>
                    <td>
                    <?php 
                    echo "<a href=\"page.php?id_section=" . $section . "&id_course=" . $id_course . "\">" . $name . "</a>";
                    ?>
                    </td>
                    <td>
                    <?php 
                    echo $fullname;
                    ?>
                    </td>
                    <td>
                    <?php
                    echo "<a href=\"view.php?id=" . $id . "&id_section=" . $section . "&id_course=" . $id_course . "\">".translate_review_element('View')."</a>";
                    ?>
                    </td>
                    <td>
                    <?php
                    echo "<a href=\"review.php?id=" . $id . "&id_section=" . $section . "&id_course=" . $id_course . "&rev=m\">".translate_review_element('Publish')."</a>";
                    ?>
                    </td>
                </tr>
 
                    <?php
                }
            }
        }
        elseif($tab == translate_review_element("Published")){
            ?>
                <tr>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Module');?></font></th>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Course');?></font></th>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Completed');?></font></th>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Status');?></font></th>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('View expert reviews');?></font></th>
                    <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('View your review');?></font></th>
                </tr>
 
            <?php
 
            //select of the modules to be reviewed
            if(count($contextid) == 1){
                $sql="SELECT sssecm_review_master.id, sssecm_review_master.id_course_sections, mdl_course_sections.name, mdl_course.id AS id_course, mdl_course_sections.section, mdl_course.fullname
                    FROM sssecm_review_master, mdl_course_sections, mdl_course, mdl_context
                    WHERE sssecm_review_master.id_course_sections=mdl_course_sections.id AND mdl_course_sections.course=mdl_course.id AND
                        mdl_course.category=mdl_context.instanceid AND mdl_context.id=" . $contextid[0] . " AND sssecm_review_master.completitionDate IS NOT NULL ";
            }
            elseif(count($contextid) > 1){
                $i = 1;
                $sql ="SELECT sssecm_review_master.id, sssecm_review_master.id_course_sections, mdl_course_sections.name, mdl_course.id AS id_course, mdl_course_sections.section, mdl_course.fullname
                    FROM sssecm_review_master, mdl_course_sections, mdl_course, mdl_context
                    WHERE sssecm_review_master.id_course_sections=mdl_course_sections.id AND mdl_course_sections.course=mdl_course.id AND
                        mdl_course.category=mdl_context.instanceid AND mdl_context.id=" . $contextid[0] . " AND sssecm_review_master.completitionDate IS NOT NULL ";
                do{
                    $sql = $sql . " UNION
                        SELECT sssecm_review_master.id, sssecm_review_master.id_course_sections, mdl_course_sections.name, mdl_course.id AS id_course, mdl_course_sections.section, mdl_course.fullname
                        FROM sssecm_review_master, mdl_course_sections, mdl_course, mdl_context
                        WHERE sssecm_review_master.id_course_sections=mdl_course_sections.id AND mdl_course_sections.course=mdl_course.id AND
                            mdl_course.category=mdl_context.instanceid AND mdl_context.id=" . $contextid[$i] . " AND sssecm_review_master.completitionDate IS NOT NULL ";
                    $i++;
                } while($i < count($contextid));
            }
            $fields = $DB->get_records_sql($sql);
            if($fields == null){
                ?>
 
                <tr>
                    <td>
                        <?php echo translate_review_element('No modules');?>
                    </td>
                    <td />
                    <td />
                    <td />
                    <td />
                    <td />
                </tr>
 
                <?php
            }
            else{
                foreach ($fields as $field) {
                    $id = $field->id;
                    $id_module = $field->id_course_sections;
                    $id_course = $field->id_course;
                    $name = $field->name;
                    $section = $field->section;
                    $fullname=$field->fullname;
 
                    //select of the review status
                    $sql="SELECT sssecm_review_master.completitionDate, sssecm_review_master.review_status
                            FROM sssecm_review_master
                            WHERE id=" . $id;
                    $results = $DB->get_records_sql($sql);
                    if($results){
                        foreach ($results as $result) {
                            $completition = $result->completitiondate;
                            $review_status = $result->review_status;
                        }
                    }
                    //select of the color
                    $sql="SELECT mdl_metadata.Value FROM sssecm_review_master, mdl_metadata WHERE sssecm_review_master.id=" . $id . " AND sssecm_review_master.id_course_sections=mdl_metadata.Id_course_sections AND mdl_metadata.Property='status'";
                    $query = $DB->get_records_sql($sql);
                    foreach ($query as $q){
                        $value= $q->value;
                    }
                    ?>
 
                <tr>
                    <td>
                    <?php 
                    echo "<a href=\"page.php?id_section=" . $section . "&id_course=" . $id_course . "\">" . $name . "</a>";
                    ?>
                    </td>
                    <td>
                    <?php 
                    echo $fullname;
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
                        echo "No decision";
                    elseif($review_status == 1){
                        if($value == "gold")
                            echo "Approved (Gold)";
                        else
                            echo translate_review_element("Approved");
                    }
                    elseif($review_status == 2)
                        echo "Minor revision";
                    elseif($review_status == 3)
                        echo "Major revision";
                    elseif($review_status == 4){
                        if($value == "black")
                            echo "Approved (Black)";
                        else
                            echo "Rejected";
                    }
                    ?>
                        </td>
                    <td>
                    <?php
                    echo "<a href=\"view.php?id=" . $id . "&id_section=" . $section . "&id_course=" . $id_course . "\">".translate_review_element('View')."</a>";
                    ?>
                    </td>
                    <td>
                    <?php
                    echo "<a href=\"review.php?id=" . $id . "&id_section=" . $section . "&id_course=" . $id_course . "&rev=m\">".translate_review_element('View')."</a>";
                    ?>
                    </td>
                </tr>
 
                    <?php
                }
            }
        }
        ?>
 
            </table>
        </form>
    </div>
        <?php
    }
    else{
 
 
        //Experts table
        $sql="SELECT mdl_role_assignments.contextid
                FROM mdl_role, mdl_role_assignments, mdl_user
                WHERE mdl_role_assignments.roleid = mdl_role.id AND mdl_role.shortname=\"expert\" AND mdl_user.id=mdl_role_assignments.userid AND mdl_user.id=" . $userid;
        $fields = $DB->get_records_sql($sql);
        $i = 0;
        foreach ($fields as $field) {
            $contextid[$i]= $field->contextid;
        }
        if(isset($contextid)){
            ?>
 
    <div align="center">
        <br />
        <br />
        <form name="choose" action="index.php" method="get">
            <table border=0>
                <tr>
                    <td>
                        <?php echo translate_review_element('View modules:');?>
                    </td>
            <?php
            if($table == translate_review_element("Assigned")){
                echo "<td><input disabled='disabled' type='submit' name='table' value='".translate_review_element('Assigned')."'></td>";
                echo "<td><input type='submit' name='table' value='".translate_review_element('Accepted')."'></td>";
                echo "<td><input type='submit' name='table' value='".translate_review_element('Reviewed')."'></td>";
            }
            elseif($table == translate_review_element("Accepted")){
                echo "<td><input type='submit' name='table' value='".translate_review_element('Assigned')."'></td>";
                echo "<td><input disabled='disabled' type='submit' name='table' value='".translate_review_element('Accepted')."'></td>";
                echo "<td><input type='submit' name='table' value='".translate_review_element('Reviewed')."'></td>";
            }
            elseif($table == translate_review_element("Reviewed")){
                echo "<td><input type='submit' name='table' value='".translate_review_element('Assigned')."'></td>";
                echo "<td><input type='submit' name='table' value='".translate_review_element('Accepted')."'></td>";
                echo "<td><input disabled='disabled' type='submit' name='table' value='".translate_review_element('Reviewed')."'></td>";
            }
            else{
                echo "<td><input type='submit' name='table' value='".translate_review_element('Assigned')."'></td>";
                echo "<td><input type='submit' name='table' value='".translate_review_element('Accepted')."'></td>";
                echo "<td><input type='submit' name='table' value='".translate_review_element('Reviewed')."'></td>";
            }
            ?>
                </tr>
            </table>
        </form>
         
        <br />
        <br />
        <form name="review_expert" action="response.php" method="post">
            <table class="hovertable">
                 
            <?php
            if($table == translate_review_element("Assigned")){
                $sql="SELECT sssecm_review_master.id, sssecm_review_master.id_course_sections, mdl_course_sections.name, mdl_course_sections.course, mdl_course_sections.section, mdl_course.fullname
                    FROM sssecm_review_master, sssecm_review, mdl_course_sections, mdl_course
                    WHERE sssecm_review.expert = " . $userid . " AND sssecm_review.id_review_master = sssecm_review_master.id AND mdl_course_sections.course=mdl_course.id AND sssecm_review_master.id_course_sections = mdl_course_sections.id AND sssecm_review.acceptanceDate IS NULL";
                $results = $DB->get_records_sql($sql);
                ?>
                    <tr>
                        <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Module');?></font></th>
                        <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Course');?></font></th>
                        <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Accept');?></font></th>
                        <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Reject');?></font></th>
                    </tr>
                <?php
                if($results == null){
                ?>
 
                    <tr>
                        <td>
                            <?php echo translate_review_element('No modules');?>
                        </td>
                        <td />
                        <td />
                        <td />
                    </tr>
 
                <?php
                }
                else{
                    foreach ($results as $result) {
                        $id = $result->id;
                        $id_module = $result->id_course_sections;
                        $id_course = $result->course;
                        $name = $result->name;
                        $section = $result->section;
                        $fullname = $result->fullname;
                        ?>
                    <tr>
                        <td>
                        <?php 
                        echo "<a href=\"page.php?id_section=" . $section . "&id_course=" . $id_course . "\">" . $name . "</a>";
                        echo "<input type=\"hidden\" name=\"id\" value=\"" . $id . "\">";
                        ?>
                        </td>
                        <td>
                        <?php 
                        echo $fullname;
                        ?>
                        </td>
                        <td>
                        <?php
                        echo '<input type="submit" name="action" value="'.translate_review_element("Accept").'">';
                        ?>
                        </td>
                        <td>
                        <?php
                        echo '<input type="submit" name="action" value="'.translate_review_element("Reject").'">';
                        ?>
                        </td>
                    </tr>
 
                        <?php
                    }
                }
            }
            elseif($table == translate_review_element("Accepted")){
                $sql="SELECT sssecm_review_master.id, sssecm_review_master.id_course_sections, mdl_course_sections.name, mdl_course_sections.course, mdl_course_sections.section, mdl_course.fullname
                    FROM sssecm_review_master, sssecm_review, mdl_course_sections, mdl_course
                    WHERE sssecm_review.expert = " . $userid . " AND sssecm_review.id_review_master = sssecm_review_master.id AND mdl_course_sections.course=mdl_course.id AND sssecm_review_master.id_course_sections = mdl_course_sections.id AND sssecm_review.acceptanceDate IS NOT NULL AND sssecm_review.completitionDate IS NULL";
                $results = $DB->get_records_sql($sql);
                ?>
                    <tr>
                        <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Module');?></font></th>
                        <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Course');?></font></th>
                        <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Accepted');?></font></th>
                        <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Review');?></font></th>
                    </tr>
                <?php
                if($results == null){
                ?>
 
                    <tr>
                        <td>
                            <?php echo translate_review_element('No modules');?>
                        </td>
                        <td />
                        <td />
                        <td />
                    </tr>
 
                <?php
                }
                else{
                    foreach ($results as $result) {
                        $id = $result->id;
                        $id_module = $result->id_course_sections;
                        $id_course = $result->course;
                        $name = $result->name;
                        $section = $result->section;
                        $fullname = $result->fullname;
 
                        //select of the review status
                        $sql="SELECT sssecm_review.acceptanceDate
                                FROM sssecm_review
                                WHERE id_review_master=" . $id;
                        $objects = $DB->get_records_sql($sql);
                        if($objects){
                            foreach ($objects as $object) {
                                $acceptance = $object->acceptancedate;
                            }
                        }
                        ?>
 
                    <tr>
                        <td>
                        <?php 
                        echo "<a href=\"page.php?id_section=" . $section . "&id_course=" . $id_course . "\">" . $name . "</a>";
                        echo "<input type=\"hidden\" name=\"id\" value=\"" . $id . "\">";
                        ?>
                        </td>
                        <td>
                        <?php 
                        echo $fullname;
                        ?>
                        </td>
                        <td>
                        <?php
                        echo $acceptance;
                        ?>
                        </td>
                        <td>
                        <?php
                        echo "<a href=\"review.php?id=" . $id . "&id_section=" . $section . "&id_course=" . $id_course . "&rev=e\">".translate_review_element('Review')."</a>";
                        ?>
                        </td>
                    </tr>
 
                        <?php
                    }
                }
            }
            elseif($table == translate_review_element("Reviewed")){
                $sql="SELECT sssecm_review_master.id, sssecm_review_master.id_course_sections, mdl_course_sections.name, mdl_course_sections.course, mdl_course_sections.section, mdl_course.fullname
                    FROM sssecm_review_master, sssecm_review, mdl_course_sections, mdl_course
                    WHERE sssecm_review.expert = " . $userid . " AND sssecm_review.id_review_master = sssecm_review_master.id AND mdl_course_sections.course=mdl_course.id AND sssecm_review_master.id_course_sections = mdl_course_sections.id AND sssecm_review.acceptanceDate IS NOT NULL AND sssecm_review.completitionDate IS NOT NULL";
                $results = $DB->get_records_sql($sql);
                ?>
                    <tr>
                        <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Module');?></font></th>
                        <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Course');?></font></th>
                        <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Completed');?></font></th>
                        <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('Status');?></font></th>
                        <th align="center"><font face="Arial, Helvetica, sans-serif"><?php echo translate_review_element('View your review');?></font></th>
                    </tr>
                <?php
                if($results == null){
                ?>
 
                    <tr>
                        <td>
                            <?php echo translate_review_element('No modules');?>
                        </td>
                        <td />
                        <td />
                        <td />
                        <td />
                    </tr>
 
                <?php
                }
                else{
                    foreach ($results as $result) {
                        $id = $result->id;
                        $id_module = $result->id_course_sections;
                        $id_course = $result->course;
                        $name = $result->name;
                        $section = $result->section;
                        $fullname = $result->fullname;
 
                        //select of the review status
                        $sql="SELECT sssecm_review.completitionDate, sssecm_review.review_status
                                FROM sssecm_review
                                WHERE id_review_master=" . $id;
                        $objects = $DB->get_records_sql($sql);
                        if($objects){
                            foreach ($objects as $object) {
                                $completition = $object->completitiondate;
                                $review_status = $object->review_status;
                            }
                        }
                        ?>
 
                    <tr>
                        <td>
                        <?php 
                        echo "<a href=\"page.php?id_section=" . $section . "&id_course=" . $id_course . "\">" . $name . "</a>";
                        echo "<input type=\"hidden\" name=\"id\" value=\"" . $id . "\">";
                        ?>
                        </td>
                        <td>
                        <?php 
                        echo $fullname;
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
                            echo translate_review_element("Accepted");
                        elseif($review_status == 2)
                            echo translate_review_element("Minor revision");
                        elseif($review_status == 3)
                            echo translate_review_element("Major revision");
                        elseif($review_status == 4)
                            echo translate_review_element("Rejected");
                        ?>
                        </td>
                        <td>
                        <?php
                        echo "<a href=\"review.php?id=" . $id . "&id_section=" . $section . "&id_course=" . $id_course . "&rev=e\">".translate_review_element("View")."</a>";
                        ?>
                        </td>
                    </tr>
 
                        <?php
                    }
                }
            }
            ?>
            </table>
        </form>
    </div>
 
            <?php
        }
        else{
            echo ('<center>Sorry, you are not allowed to enter this section!</center>');
        }
    }
}
 
echo $OUTPUT->footer();
?>
</body>
</html>
