<?php

    require_once(__DIR__ . '/../../config.php');
    require_once($CFG->dirroot . '/' . $CFG->admin . '/roles/lib.php');


    $contextid = required_param('contextid', PARAM_INT);
    $roleid    = optional_param('roleid', 0, PARAM_INT);
    $returnurl = optional_param('returnurl', null, PARAM_LOCALURL);

    list($context, $course, $cm) = get_context_info_array($contextid);

    $course = $DB->get_record('course', array('id'=>optional_param('courseid', SITEID, PARAM_INT)), '*', MUST_EXIST);
    $user = $DB->get_record('user', array('id'=>$context->instanceid), '*', MUST_EXIST);
    $url->param('courseid', $course->id);
    $url->param('userid', $user->id);

    // Security.
    require_login($course, false, $cm);
    require_capability('moodle/role:assign', $context);


    $contextname = $context->get_context_name();
    $courseid = $course->id;

    echo $user->id;

?>