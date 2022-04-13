<?php

function cleanString($string){
$string = str_replace("à", "a", $string);
$string = str_replace("á", "a", $string);
$string = str_replace("â", "a", $string);
$string = str_replace("ä", "a", $string);
 
$string = str_replace("è", "e", $string);
$string = str_replace("é", "e", $string);
$string = str_replace("ê", "e", $string);
$string = str_replace("ë", "e", $string);
 
$string = str_replace("ì", "i", $string);
$string = str_replace("í", "i", $string);
$string = str_replace("î", "i", $string);
$string = str_replace("ï", "i", $string);
 
$string = str_replace("ò", "o", $string);
$string = str_replace("ó", "o", $string);
$string = str_replace("ô", "o", $string);
$string = str_replace("ö", "o", $string);
 
$string = str_replace("ù", "u", $string);
$string = str_replace("ú", "u", $string);
$string = str_replace("û", "u", $string);
$string = str_replace("ü", "u", $string);
 
$string = preg_match("[^A-Za-z0-9: \-]", "", $string );
return $string;
}

// Require both the backup and restore libs
require_once('../config.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_plan_builder.class.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->dirroot . '/backup/util/ui/import_extensions.php');

require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->libdir . '/filelib.php');
require_once('../lib/filelib.php');
global $DB;

//if ($REQUEST_METHOD=="POST") {
if ($_SERVER["REQUEST_METHOD"] == "POST") {
$HTTP_STR=$_POST;
}else{
$HTTP_STR=$_GET;
}
$sectionDest=$HTTP_STR["id_section"];
$modid=array();
$modid=$HTTP_STR["my-select"];
$numModules = count($modid);

$sel_resources=array();
$res_types=array();

$sel_resources= explode('-', $HTTP_STR["my-select"])[1];
$res_types= explode('-', $HTTP_STR["my-select"])[0];
$numResources = count($sel_resources);


/*
//recupero valutazione del corso di destinazione
	$sql = "SELECT ROUND(AVG(mdl_block_rate_course.rating*2)) as grade
			FROM mdl_block_rate_course
			WHERE mdl_block_rate_course.course = ".$courseDest."
			";
	if($DB->get_record_sql($sql)->grade != null)
		$courseGrade = $DB->get_record_sql($sql)->grade;
	else 
		$courseGrade = 0;
//voto corso recuperato...
*/
/*
for($i=0; $i < $numModules;$i++){

	$id_sezione = $modid[$i];
	
	// Seleziono il corso di appartenenza della sezione
	$sql="select id, course from mdl_course_sections WHERE id = '".$id_sezione."'";
	$fields = $DB->get_records_sql($sql);
	foreach ($fields as $field)
		$courseid = $field->course;
	
	// Seleziono il valore di value	
	$sql = "SELECT id, value from mdl_course_format_options WHERE courseid = '".$courseDest."' AND name = '".numsections."'";
	$fields = $DB->get_records_sql($sql);
	foreach ($fields as $field)
		$value_course  = $field->value;
	$value = $value_course + 1;

		// test
	
	$sql="select max(section) as section from mdl_course_sections WHERE course = '".$courseDest."'";
	$fields = $DB->get_records_sql($sql);
	foreach ($fields as $field){
		$value  = $field->section + 1;	
	}
	
	// Aggiorno il campo value
	$sql = "UPDATE mdl_course_format_options SET value='".$value."' WHERE courseid = '".$courseDest."' AND name = '".numsections."'";
	$DB->execute($sql);		
		
	// Query che seleziona il campo "sequence"
	$sql="select id, sequence, name, summary from mdl_course_sections WHERE id = '".$id_sezione."'";
	$fields = $DB->get_records_sql($sql);
	foreach ($fields as $field){
		$sequenceIniziale  = $field->sequence;
		$nameSection  = $field->name;
		$summary  = $field->summary;	
	}
	
	$array = array_map('intval', explode(",", $sequenceIniziale));
	
	$numeroFile = count($array);
	$data = $array[0];

			
	/*******************************************************************************/
	/* Inserisco la nuova sezione vuota! Successivamente la riempiremo con i file  */
	/*******************************************************************************/

	/*
	$nameSection = stripslashes($nameSection);
	$summary = stripslashes($summary);
	
	$nameSection = strip_tags($nameSection);
	$summary = strip_tags($summary);
	
	/*
	$nameSection = mysql_real_escape_string($nameSection);
	$summary = mysql_real_escape_string($summary);	*/
	/*
	$nameSection = cleanString($nameSection);
	$summary = cleanString($summary);
		

	
	$sql="INSERT INTO mdl_course_sections(course, section, name, summary, summaryformat, visible) VALUES ('".$courseDest."', '".$value."', '".$nameSection."', '".$summary."', 1, 1)";
	$DB->execute($sql);
	
	// Seleziono l'id della sezione appena inserita
	$sql="select max(id) AS idmax, sequence from mdl_course_sections";
	$fields = $DB->get_records_sql($sql);
	foreach ($fields as $field)
		$id_dest_sez = $field->idmax;
		
	//inserimento dei metadati associati al modulo	
	$sql="SELECT property, value FROM mdl_metadata WHERE id_course_sections = '".$id_sezione."' AND id_course IS NULL AND id_resource IS NULL";
	$fields = $DB->get_records_sql($sql);
	foreach ($fields as $field) {
		$property  = $field->property;
		$value  = $field->value;
		
		$sql="INSERT INTO mdl_metadata(id_course_sections, property, value, courseGrade) VALUES ('".$id_dest_sez."', '".$property."', '".$value."', $courseGrade)";
		$fields = $DB->execute($sql);
		
	}

	//inserimento dei metadati associati al modulo propagati sul corso	
	$sql="SELECT property, value FROM mdl_metadata WHERE id_course_sections = '".$id_sezione."' AND id_course IS NOT NULL AND id_resource IS NULL";
	$fields = $DB->get_records_sql($sql);
	foreach ($fields as $field) {
		$property  = $field->property;
		$value  = $field->value;
		
		$sql="INSERT INTO mdl_metadata(id_course, id_course_sections, property, value, courseGrade) VALUES ('".$courseDest."', '".$id_dest_sez."', '".$property."', '".$value."', $courseGrade)";
		$fields = $DB->execute($sql);
		
	}

	$arrayModuli = array();
	$arrayTipo = array();
	
	if($data != "") {
			$sql="select id, module from mdl_course_modules WHERE section = '".$id_sezione."'";
			$fields = $DB->get_records_sql($sql);
			$x = 0;
			foreach ($fields as $field) {
				$arrayModuli[$x]  = $field->id;
				
				$arrayTipo[$x] = $field->module;
				$x++;
			}


			//inserimento dei metadati associati alle risorse e propagati sul modulo e sul corso (parte 1)
			$array_resources = array();
			$k = 0;
			$sql="SELECT DISTINCT id_resource FROM mdl_metadata WHERE id_course_sections = '".$id_sezione."' AND id_course IS NOT NULL AND id_resource IS NOT NULL";
			$fields = $DB->get_records_sql($sql);
			foreach ($fields as $field) {
				$array_resources[$k] = $field->id_resource;
				$k++;
			}
			
			*/
		

// * - cmid: The Course module id (id of resource)
// * - typeModule: The Course module type (type of resource)	
	
for($j = 0; $j < $numResources; $j++){		
	$cmid = $sel_resources[$j];
	if($cmid != ''){
	$typeModule = $res_types[$j];

	echo ($cmid);
	echo ($typeModule);
	/*******************************************************/
	/* PARTE PHP CHE SI OCCUPA DI DUPLICARE LA RISORSA     */
	/*******************************************************/
	$sectionreturn  = 0;
	$course     = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
	$cm         = get_coursemodule_from_id('', $cmid, $course->id, true, MUST_EXIST);

	
	
	$cmcontext  = context_module::instance($cm->id);
	$context    = context_course::instance($courseid);
	$section    = $DB->get_record('course_sections', array('id' => $cm->section, 'course' => $cm->course));

	//require_capability('moodle/course:manageactivities', $context);
	// Require both target import caps to be able to duplicate, see course_get_cm_edit_actions()
	//require_capability('moodle/backup:backuptargetimport', $context);
	//require_capability('moodle/restore:restoretargetimport', $context);

	$a = new stdClass();
	$a->modtype = get_string('modulename', $cm->modname);
	$a->modname = format_string($cm->name);

	if (!plugin_supports('mod', $cm->modname, FEATURE_BACKUP_MOODLE2)) {
		$url = course_get_url($course, $cm->sectionnum, array('sr' => $sectionreturn));
		print_error('duplicatenosupport', 'error', $url, $a);
	}

	// backup the activity

	$bc = new backup_controller(backup::TYPE_1ACTIVITY, $cm->id, backup::FORMAT_MOODLE,
		backup::INTERACTIVE_NO, backup::MODE_IMPORT, $USER->id);

	$backupid       = $bc->get_backupid();
	$backupbasepath = $bc->get_plan()->get_basepath();

	$bc->execute_plan();

	$bc->destroy();

	// restore the backup immediately

	$rc = new restore_controller($backupid, $courseid,
		backup::INTERACTIVE_NO, backup::MODE_IMPORT, $USER->id, backup::TARGET_CURRENT_ADDING);

	if (!$rc->execute_precheck()) {
		$precheckresults = $rc->get_precheck_results();
		if (is_array($precheckresults) && !empty($precheckresults['errors'])) {
			if (empty($CFG->keeptempdirectoriesonbackup)) {
				fulldelete($backupbasepath);
			}

		$url = course_get_url($course, $cm->sectionnum, array('sr' => $sectionreturn));
		die();
		}
	}

	$rc->execute_plan();

	// now a bit hacky part follows - we try to get the cmid of the newly
	// restored copy of the module
	$newcmid = null;
	$tasks = $rc->get_plan()->get_tasks();
	foreach ($tasks as $task) {
		if (is_subclass_of($task, 'restore_activity_task')) {
			if ($task->get_old_contextid() == $cmcontext->id) {
				$newcmid = $task->get_moduleid();
				break;
			}
		}
	}

	// if we know the cmid of the new course module, let us move it
	// right below the original one. otherwise it will stay at the
	// end of the section
	if ($newcmid) {
		$newcm = get_coursemodule_from_id('', $newcmid, $course->id, true, MUST_EXIST);
		moveto_module($newcm, $section, $cm);
		moveto_module($cm, $section, $newcm);
	}

	$rc->destroy();

	/*******************************************************/
	/* 						FINE	                       */
	/*******************************************************/

	// Verifico il tipo della risorsa
	
	switch($typeModule){
		case 1: $tabella_file = 'mdl_assign';
		break;
		case 3: $tabella_file = 'mdl_book';	
		break;
		case 4: $tabella_file = 'mdl_chat';
		break;
		case 5: $tabella_file = 'mdl_choice';
		break;
		case 6: $tabella_file = 'mdl_data';
		break;
		case 8: $tabella_file = 'mdl_folder';
		break;
		case 9: $tabella_file = 'mdl_forum';
		break;
		case 10: $tabella_file = 'mdl_glossary';
		break;
		case 11: $tabella_file = 'mdl_imscp';
		break;
		case 12: $tabella_file = 'mdl_label';
		break;
		case 13: $tabella_file = 'mdl_lesson';
		break;
		case 14: $tabella_file = 'mdl_lti';
		break;
		case 15: $tabella_file = 'mdl_page';
		break;
		case 16: $tabella_file = 'mdl_quiz';
		break;
		case 17: $tabella_file = 'mdl_resource';
		break;
		case 18: $tabella_file = 'mdl_scorm'; 
		break;
		case 19: $tabella_file = 'mdl_survey';	
		break;
		case 20: $tabella_file = 'mdl_url';
		break;
		case 21: $tabella_file = 'mdl_wiki';
		break;
		case 22: $tabella_file = 'mdl_workshop';
		break;
	}

	$sql="SELECT MAX(id) AS idmax, course FROM ".$tabella_file."";	
	$fields = $DB->get_records_sql($sql);
	foreach ($fields as $field)
		$idMaxRisorsa  = $field->idmax;	

	$sql="UPDATE ".$tabella_file." SET course = '".$courseDest."' WHERE id = '".$idMaxRisorsa."'";
	$DB->execute($sql);
	
	// Query che seleziona il campo "sequence"

	$sql="select id, sequence from mdl_course_sections WHERE id = '".$id_sezione."'";
	$fields = $DB->get_records_sql($sql);
	foreach ($fields as $field)
	$sequenceFinale  = $field->sequence;	

	$array = explode(",", $sequenceFinale);

	$sequenceDelFile = max($array);

	$sql="CALL update_database('".$id_sezione."', '".$sequenceDelFile."', '".$sequenceIniziale."', '".$courseDest."', '".$id_dest_sez."', '".$typeModule."')";
	$DB->execute($sql);
	
	//inserimento dei metadati associati alle risorse e propagati sul modulo e sul corso (parte 2)
	$sql="SELECT DISTINCT m.id AS id_risorsa FROM mdl_course_modules m, ".$tabella_file." f WHERE m.instance = ".$idMaxRisorsa." AND m.module = ".$typeModule."";
	$fields = $DB->get_records_sql($sql);
	foreach ($fields as $field)
		$id_risorsa_modulo = $field->id_risorsa;
		
	$sql="SELECT property, value FROM mdl_metadata WHERE id_course_sections = '".$id_sezione."' AND id_course IS NOT NULL AND id_resource = '".$array_resources[$j]."'";
	$fields = $DB->get_records_sql($sql);
	foreach ($fields as $field) {
		$property  = $field->property;
		$value  = $field->value;

		$sql="INSERT INTO mdl_metadata(id_course, id_course_sections, id_resource, property, value, courseGrade) VALUES ('".$courseDest."', '".$id_dest_sez."', '".$id_risorsa_modulo."', '".$property."', '".$value."', $courseGrade)";
		$fields = $DB->execute($sql);
		}
	}
}
}	
	/*****************************************************Parte "duplicates"*************************************************/
		/* 	Ogni volta che si procede alla duplicazione di una sezione s, si inserisce una tupla 		  						*/
		/*	in questa tabella applicando il seguente algoritmo:											  						*/
		/*	If s has never been duplicated (i.e. Table Duplicates does not contain a tuple in which s is the destination) 		*/
		/*	then the tuple (s,s,d,0) in inserted in the table Duplicates, where d is the identifier of the module duplicated.	*/
		/*	else (table Duplicates contains a tuple (s\92,s\92\92,s,f)), the tuple (s\92,s,d,0) is inserted in the table Duplicates.	*/
		/*	This algorithm guarantees that each duplication is always associated with the original module 						*/
		/*	(i.e. the first module starting from which duplications have been realized) and the module from which 				*/
		/*	it has been duplicated. 																							*/
		/*	This means that if s3 is the duplication of s2 and s2 is the duplication of s1 which is in turn the duplication 	*/
		/*	of s0,Table Duplicates contains the tuples (s0,s0,s1,0), (s0,s1,s2,0), (s0,s2,s3,0).								*/												
		/************************************************************************************************************************/

		$sql="select count(*) from sssecm_duplicates WHERE id_sec_dest = '".$id_sezione."'";
		$n_mod_dup = $DB->count_records_sql($sql);
		
		if($n_mod_dup > 0){
			$sql="select id_sec_source, id_sec_origin from sssecm_duplicates WHERE id_sec_dest = '".$id_sezione."'";
			$fields = $DB->get_records_sql($sql);
			foreach ($fields as $field)
				$id_sec_source = $field->id_sec_source;
				$id_sec_origin = $field->id_sec_origin;
			$sql="INSERT INTO sssecm_duplicates(id_sec_source, id_sec_dest, id_sec_origin, flag) VALUES ('".$id_sezione."', '".$id_dest_sez."', '".$id_sec_origin."', 0)";
			$DB->execute($sql);
		}else{
			$sql="INSERT INTO sssecm_duplicates(id_sec_source, id_sec_dest, id_sec_origin, flag) VALUES ('".$id_sezione."', '".$id_dest_sez."', '".$id_sezione."', 0)";
			$DB->execute($sql);
		}
	
	}	
		
	// Aggiorno la cache del corso
	//$sql="UPDATE mdl_course SET cacherev = '1' WHERE id = '".$courseDest."'";
	//$DB->execute($sql);
	
	rebuild_course_cache($courseDest);

	$server_name = $_SERVER['SERVER_NAME'];
	header("Location: http://".$server_name."/".$CFG->dirroot."/course/view.php?id=" . $courseDest); /* Redirect browser */
	//header("Location: http://".$server_name); /* Redirect browser */
?>
