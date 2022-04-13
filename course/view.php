<?php

//  Display the course home page.

    require_once('../config.php');
    require_once('lib.php');
    require_once($CFG->libdir.'/completionlib.php');
	require_once('../metadata/metadata_page/aux_functions.php'); // _s_t_mod_


    $id          = optional_param('id', 0, PARAM_INT);
    $name        = optional_param('name', '', PARAM_TEXT);
    $edit        = optional_param('edit', -1, PARAM_BOOL);
    $hide        = optional_param('hide', 0, PARAM_INT);
    $show        = optional_param('show', 0, PARAM_INT);
    $idnumber    = optional_param('idnumber', '', PARAM_RAW);
    $sectionid   = optional_param('sectionid', 0, PARAM_INT);
    $section     = optional_param('section', 0, PARAM_INT);
    $move        = optional_param('move', 0, PARAM_INT);
    $marker      = optional_param('marker',-1 , PARAM_INT);
    $switchrole  = optional_param('switchrole',-1, PARAM_INT); // Deprecated, use course/switchrole.php instead.
    $return      = optional_param('return', 0, PARAM_LOCALURL);

    $params = array();
    if (!empty($name)) {
        $params = array('shortname' => $name);
    } else if (!empty($idnumber)) {
        $params = array('idnumber' => $idnumber);
    } else if (!empty($id)) {
        $params = array('id' => $id);
    }else {
        print_error('unspecifycourseid', 'error');
    }

    $course = $DB->get_record('course', $params, '*', MUST_EXIST);

    $urlparams = array('id' => $course->id);

    // Sectionid should get priority over section number
    if ($sectionid) {
        $section = $DB->get_field('course_sections', 'section', array('id' => $sectionid, 'course' => $course->id), MUST_EXIST);
    }
    if ($section) {
        $urlparams['section'] = $section;
    }

    $PAGE->set_url('/course/view.php', $urlparams); // Defined here to avoid notices on errors etc

    // Prevent caching of this page to stop confusion when changing page after making AJAX changes
    $PAGE->set_cacheable(false);

    context_helper::preload_course($course->id);
    $context = context_course::instance($course->id, MUST_EXIST);

    // Remove any switched roles before checking login
    if ($switchrole == 0 && confirm_sesskey()) {
        role_switch($switchrole, $context);
    }

    require_login($course);

    // Switchrole - sanity check in cost-order...
    $reset_user_allowed_editing = false;
    if ($switchrole > 0 && confirm_sesskey() &&
        has_capability('moodle/role:switchroles', $context)) {
        // is this role assignable in this context?
        // inquiring minds want to know...
        $aroles = get_switchable_roles($context);
        if (is_array($aroles) && isset($aroles[$switchrole])) {
            role_switch($switchrole, $context);
            // Double check that this role is allowed here
            require_login($course);
        }
        // reset course page state - this prevents some weird problems ;-)
        $USER->activitycopy = false;
        $USER->activitycopycourse = NULL;
        unset($USER->activitycopyname);
        unset($SESSION->modform);
        $USER->editing = 0;
        $reset_user_allowed_editing = true;
    }

    //If course is hosted on an external server, redirect to corresponding
    //url with appropriate authentication attached as parameter
    if (file_exists($CFG->dirroot .'/course/externservercourse.php')) {
        include $CFG->dirroot .'/course/externservercourse.php';
        if (function_exists('extern_server_course')) {
            if ($extern_url = extern_server_course($course)) {
                redirect($extern_url);
            }
        }
    }


    require_once($CFG->dirroot.'/calendar/lib.php');    /// This is after login because it needs $USER

    // Must set layout before gettting section info. See MDL-47555.
    $PAGE->set_pagelayout('course');

    if ($section and $section > 0) {

        // Get section details and check it exists.
        $modinfo = get_fast_modinfo($course);
        $coursesections = $modinfo->get_section_info($section, MUST_EXIST);

        // Check user is allowed to see it.
        if (!$coursesections->uservisible) {
            // Check if coursesection has conditions affecting availability and if
            // so, output availability info.
            if ($coursesections->visible && $coursesections->availableinfo) {
                $sectionname     = get_section_name($course, $coursesections);
                $message = get_string('notavailablecourse', '', $sectionname);
                redirect(course_get_url($course), $message, null, \core\output\notification::NOTIFY_ERROR);
            } else {
                // Note: We actually already know they don't have this capability
                // or uservisible would have been true; this is just to get the
                // correct error message shown.
                require_capability('moodle/course:viewhiddensections', $context);
            }
        }
    }

    // Fix course format if it is no longer installed
    $course->format = course_get_format($course)->get_format();

    $PAGE->set_pagetype('course-view-' . $course->format);
    $PAGE->set_other_editing_capability('moodle/course:update');
    $PAGE->set_other_editing_capability('moodle/course:manageactivities');
    $PAGE->set_other_editing_capability('moodle/course:activityvisibility');
    if (course_format_uses_sections($course->format)) {
        $PAGE->set_other_editing_capability('moodle/course:sectionvisibility');
        $PAGE->set_other_editing_capability('moodle/course:movesections');
    }

    // Preload course format renderer before output starts.
    // This is a little hacky but necessary since
    // format.php is not included until after output starts
    if (file_exists($CFG->dirroot.'/course/format/'.$course->format.'/renderer.php')) {
        require_once($CFG->dirroot.'/course/format/'.$course->format.'/renderer.php');
        if (class_exists('format_'.$course->format.'_renderer')) {
            // call get_renderer only if renderer is defined in format plugin
            // otherwise an exception would be thrown
            $PAGE->get_renderer('format_'. $course->format);
        }
    }

    if ($reset_user_allowed_editing) {
        // ugly hack
        unset($PAGE->_user_allowed_editing);
    }

    if (!isset($USER->editing)) {
        $USER->editing = 0;
    }
    if ($PAGE->user_allowed_editing()) {
        if (($edit == 1) and confirm_sesskey()) {
            $USER->editing = 1;
            // Redirect to site root if Editing is toggled on frontpage
            if ($course->id == SITEID) {
                redirect($CFG->wwwroot .'/?redirect=0');
            } else if (!empty($return)) {
                redirect($CFG->wwwroot . $return);
            } else {
                $url = new moodle_url($PAGE->url, array('notifyeditingon' => 1));
                redirect($url);
            }
        } else if (($edit == 0) and confirm_sesskey()) {
            $USER->editing = 0;
            if(!empty($USER->activitycopy) && $USER->activitycopycourse == $course->id) {
                $USER->activitycopy       = false;
                $USER->activitycopycourse = NULL;
            }
            // Redirect to site root if Editing is toggled on frontpage
            if ($course->id == SITEID) {
                redirect($CFG->wwwroot .'/?redirect=0');
            } else if (!empty($return)) {
                redirect($CFG->wwwroot . $return);
            } else {
                redirect($PAGE->url);
            }
        }

        if (has_capability('moodle/course:sectionvisibility', $context)) {
            if ($hide && confirm_sesskey()) {
                set_section_visible($course->id, $hide, '0');
                redirect($PAGE->url);
            }

            if ($show && confirm_sesskey()) {
                set_section_visible($course->id, $show, '1');
                redirect($PAGE->url);
            }
        }

        if (!empty($section) && !empty($move) &&
                has_capability('moodle/course:movesections', $context) && confirm_sesskey()) {
            $destsection = $section + $move;
            if (move_section_to($course, $section, $destsection)) {
                if ($course->id == SITEID) {
                    redirect($CFG->wwwroot . '/?redirect=0');
                } else {
                    redirect(course_get_url($course));
                }
            } else {
                echo $OUTPUT->notification('An error occurred while moving a section');
            }
        }
    } else {
        $USER->editing = 0;
    }

    $SESSION->fromdiscussion = $PAGE->url->out(false);


    if ($course->id == SITEID) {
        // This course is not a real course.
        redirect($CFG->wwwroot .'/');
    }

    $completion = new completion_info($course);
    if ($completion->is_enabled()) {
        $PAGE->requires->string_for_js('completion-alt-manual-y', 'completion');
        $PAGE->requires->string_for_js('completion-alt-manual-n', 'completion');

        $PAGE->requires->js_init_call('M.core_completion.init');
    }

    // We are currently keeping the button here from 1.x to help new teachers figure out
    // what to do, even though the link also appears in the course admin block.  It also
    // means you can back out of a situation where you removed the admin block. :)
    if ($PAGE->user_allowed_editing()) {
        $buttons = $OUTPUT->edit_button($PAGE->url);
        $PAGE->set_button($buttons);
    }

    // If viewing a section, make the title more specific
    if ($section and $section > 0 and course_format_uses_sections($course->format)) {
        $sectionname = get_string('sectionname', "format_$course->format");
        $sectiontitle = get_section_name($course, $section);
        $PAGE->set_title(get_string('coursesectiontitle', 'moodle', array('course' => $course->fullname, 'sectiontitle' => $sectiontitle, 'sectionname' => $sectionname)));
    } else {
        $PAGE->set_title(get_string('coursetitle', 'moodle', array('course' => $course->fullname)));
    }

    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();

    if ($USER->editing == 1) {

        // MDL-65321 The backup libraries are quite heavy, only require the bare minimum.
        require_once($CFG->dirroot . '/backup/util/helper/async_helper.class.php');

        if (async_helper::is_async_pending($id, 'course', 'backup')) {
            echo $OUTPUT->notification(get_string('pendingasyncedit', 'backup'), 'warning');
        }
    }

    if ($completion->is_enabled()) {
        // This value tracks whether there has been a dynamic change to the page.
        // It is used so that if a user does this - (a) set some tickmarks, (b)
        // go to another page, (c) clicks Back button - the page will
        // automatically reload. Otherwise it would start with the wrong tick
        // values.
        echo html_writer::start_tag('form', array('action'=>'.', 'method'=>'get'));
        echo html_writer::start_tag('div');
        echo html_writer::empty_tag('input', array('type'=>'hidden', 'id'=>'completion_dynamic_change', 'name'=>'completion_dynamic_change', 'value'=>'0'));
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('form');
    }

    // Course wrapper start.
    echo html_writer::start_tag('div', array('class'=>'course-content'));

    // make sure that section 0 exists (this function will create one if it is missing)
    course_create_sections_if_missing($course, 0);
	
	
	/*********************************************************/
	// START OF _s_t_mod_ ADDING
	
	$course_id = $course->id;
	$server_name = $_SERVER['SERVER_NAME'];
        print '<table border=0 bordercolor=#ddddd>';
	print '<tr style = "border: white">';
	print '<td><form action=https://'.$server_name.'/lms/metadata/metadata_page/course_metadata.php target="_top">';
	echo "<input type='hidden' name='id_course' value='".$course_id."'/>";
	
	// Controllo lingua sul bottone per visualizzazione metadati
	print "<input type='submit' align='center' value='".handle_translation('Course metadata', 'Metadati del corso', 'Metadatasini gör', '?????? ??????????? ?? ?????')."'>";
	print '</form></td>';

	/*if ($PAGE->user_allowed_editing()) {
		print '<td><form action=https://'.$server_name.'/lms/search_engine target="_top">';
		echo "<input type='hidden' name='id_course' value='".$course_id."'/>";
		print "<input type='submit' align='center' value='".handle_translation('Add modules through metadata', 'Aggiungi moduli da metadati', 'Modülleri metadata üzerinden ekle', '?????? ???? ???? ?????????')."'>";
		print '</form></td>';
	}*/
	print '</tr>';
	print '</table>';

	//Alert provvisorio per l'inserimento dei metadati dei moduli
	if ($PAGE->user_allowed_editing()) {	
	//	echo "<br><div align='center'><h4><font color='red'>".handle_translation('REMEMBER TO INSERT MODULES METADATA AND TO CHANGE THE SETTINGS OF THE DEFAULT FORM!', 'RICORDATI DI INSERIRE I METADATI DEI MODULI E DI MODIFICARE LE IMPOSTAZIONI DEL MODULO PREDEFINITO!', 'MODÜLLER METADATA INSERT VE STANDART FORMU AYARLARINI DEGISTIRMEK IÇIN UNUTMAYIN!', '?? ?????????? ?? ????????? ?????? ?? ????????? ? ?? ????????? ??????????? ?? ??????? ?? ????????????')."</font></h4></div>";
	}
	
	
	//######### Share Button ####################### 

	//OLD
	/*echo '<!-- AddThis Button BEGIN -->
	<p><a class="addthis_button" href="http://www.addthis.com/bookmark.php?v=300&amp;pubid=ra-530a20cc6de2bcdd"><img src="https://s7.addthis.com/static/btn/v2/lg-share-en.gif" alt="Bookmark and Share" style="border: 0;" height="16" width="125" /></a></p>
	<script type="text/javascript">// <![CDATA[
	var addthis_config = {"data_track_addressbar":true};
	// ]]></script>
	<script src="https://s7.addthis.com/js/300/addthis_widget.js#pubid=ra-530a20cc6de2bcdd" type="text/javascript"></script>
	<!-- AddThis Button END -->'; */
	
	echo '<!-- Go to www.addthis.com/dashboard to customize your tools -->
		  <div style="margin:0 0 10px 5px" class="addthis_inline_share_toolbox"></div>
		  <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5d47fdc9b7c7c6ee"></script>';

	//######### Share Button #######################
 
	// legenda vecchia che su sfondo bianco non va bene
	//Legenda
	echo "<br>";
/*
	print handle_translation('Modules legend: ', 'Legenda dei moduli: ');
	echo "<br>";
	echo "<span style='background-color: #eeee44'><font size=2 color='#000000;'> ".handle_translation('&nbsp &nbsp &nbsp &nbsp  &nbsp  Recommended &nbsp &nbsp &nbsp &nbsp  &nbsp ', '&nbsp &nbsp &nbsp &nbsp  &nbsp Consigliato &nbsp &nbsp &nbsp &nbsp')." </font></span>";
	echo "<span style='background-color: #00ff00'><font size=2 color='#000000;'> ".handle_translation('&nbsp &nbsp &nbsp  &nbsp Approved &nbsp &nbsp &nbsp  &nbsp', '&nbsp &nbsp &nbsp &nbsp Approvato &nbsp &nbsp &nbsp  &nbsp')." </font></span>";
	echo "<span style='background-color: #ffff00'><font size=2 color='#000000;'> ".handle_translation('Waiting to be reviewed', 'In attesa di revisione')." </font></span>";
	echo "<span style='background-color: #ff0000'><font size=2 color='#000000;'> ".handle_translation('&nbsp &nbsp &nbsp Need changes &nbsp &nbsp &nbsp', '&nbsp &nbsp &nbsp Da modificare &nbsp &nbsp &nbsp')." </font></span>";
	echo "<span style='background-color: #000000'><font size=2 color='#ffffff'>  ".handle_translation('&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp Rejected &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp', ' &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp Respinto &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp ')."</font></span>";
	echo "<br><br>";
*/
/*
	//Legenda nuova
	echo "<br>";

	print handle_translation('Modules legend: ', 'Legenda dei moduli: ');
	echo "<br>";
	echo "<font size=2 color='#eeee44;'>&#9679; ".handle_translation('Recommended', 'Consigliato')." </font>";
	echo "<font size=2 color='#00ff00;'> &#9679; ".handle_translation('Approved', 'Approvato')." </font>";
	echo "<font size=2 color='#ffff00;'> &#9679; ".handle_translation('Waiting to be reviewed', 'In attesa di revisione')." </font>";
	echo "<font size=2 color='#ff0000;'> &#9679; ".handle_translation('Need changes', 'Da modificare')." </font>";
	echo "<font size=2 color='#000000'>  &#9679; ".handle_translation('Rejected', 'Respinto')."</font>";
	echo "<br><br>";
*/


	/*********************************************************/
	// END OF _s_t_mod_ ADDING
	

    // get information about course modules and existing module types
    // format.php in course formats may rely on presence of these variables
    $modinfo = get_fast_modinfo($course);
    $modnames = get_module_types_names();
    $modnamesplural = get_module_types_names(true);
    $modnamesused = $modinfo->get_used_module_names();
    $mods = $modinfo->get_cms();
    $sections = $modinfo->get_section_info_all();

    // CAUTION, hacky fundamental variable defintion to follow!
    // Note that because of the way course fromats are constructed though
    // inclusion we pass parameters around this way..
    $displaysection = $section;

    // Include the actual course format.
    require($CFG->dirroot .'/course/format/'. $course->format .'/format.php');
    // Content wrapper end.

    echo html_writer::end_tag('div');

    // Trigger course viewed event.
    // We don't trust $context here. Course format inclusion above executes in the global space. We can't assume
    // anything after that point.
    course_view(context_course::instance($course->id), $section);

    // Include course AJAX
    include_course_ajax($course, $modnamesused);

    echo $OUTPUT->footer();
