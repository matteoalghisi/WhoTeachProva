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
require_once('../../config.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_plan_builder.class.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->dirroot . '/backup/util/ui/import_extensions.php');

require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->libdir . '/filelib.php');
require_once('../../lib/filelib.php');
require_once('../../course/lib.php');

global $DB, $cm;
//check if usrer is logged in and has capability to create resource
require_once('../../config.php');
global $CFG;
require_once($CFG->libdir.'/moodlelib.php');
require_login($course);
$PAGE->set_context(context_system::instance());
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_url($CFG->wwwroot."/mod/recommend/import_mod.php");
if (!has_capability('moodle/course:managefiles', context_system::instance()))
	die();

$data = json_decode(file_get_contents('php://input'), true);
//print_r(var_dump($data));

//id section
$id_dest_sez=$data["id_section"];
//id course
$courseDest=$data["id_course"];

//$modid=array();
//$modid=$HTTP_STR["my-select"];
//$numModules = count($modid);

//id resources and types
$sel_resources=array();
$res_types=array();

//$sel_resources= explode('-', $data["listaCampi"]["my-select"])[1];
$sel_resources= $data["listaCampi"];
//$res_types= $data["listaCampi"]["my-select"];
$numResources = count($sel_resources);



// * - cmid: The Course module id (id of resource)
// * - typeModule: The Course module type (type of resource)	
	
for($j = 0; $j < $numResources; $j++){		

	$cmid = explode('-', $sel_resources[$j]["my-select"])[1]; //in cmid c'è l'id della risorsa (id vecchio)
	if($cmid != ''){

		$typeModule = explode('-', $sel_resources[$j]["my-select"])[0]; //typeModule è il tipo di risorsa (quale tra le 22 tabelle)

		/////////////

		//$typeModule = $fields['module']; 
		$result = $DB->get_record_sql("SELECT DISTINCT id, course FROM mdl_course_modules WHERE id = ".$cmid."");
		$courseid = $result->course; //id del corso al quale appartiene la risorsa in cmid (vecchia)


		//fin qui ho recuperato 'id della risorsa'($cmid), 'tipo di risorsa'($typeModule) e 'corso di appartenenza'($courseid) della risorsa

		//prima di procedere con la duplicazione recupero il campo "sequence" della sezione alla quale appartiene la risorsa originaria
		//$result = mysqli_query($connection, "SELECT DISTINCT id_course, id_course_sections FROM mdl_metadata WHERE id_resource = '".$cmid."'" );
		//$fields = mysqli_fetch_assoc($result);
		$result = $DB->get_record_sql("SELECT DISTINCT course, section FROM mdl_course_modules WHERE id = ".$cmid."");
		$sect = $result->section; //in $sect ho l'id della sezione alla quale appartiene la risorsa (vecchia)
		//$result = mysqli_query($connection, "SELECT id, course, name, summary, sequence FROM mdl_course_sections WHERE id = '".$sect."'" );
		//$fields = mysqli_fetch_assoc($result);
		$result = $DB->get_record_sql("SELECT id, course, name, summary, sequence FROM mdl_course_sections WHERE id = '".$sect."'");
		$sequenceIniziale = $result->sequence; //in $sequenceIniziale ho il campo sequence della sezione alla quale appartiene la risorsa (il tutto prima della duplicazione)

		/*******************************************************/
		/* PARTE PHP CHE SI OCCUPA DI DUPLICARE LA RISORSA     */
		/*******************************************************/

		$sectionreturn  = 0;

		$course     = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
		$cm         = get_coursemodule_from_id('', $cmid, $course->id, true, MUST_EXIST);
		$cmcontext  = context_module::instance($cm->id);
		$context    = context_course::instance($courseid);
		$section    = $DB->get_record('course_sections', array('id' => $cm->section, 'course' => $cm->course));

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

		//////////qui sopra non cacella i metadati
		
		
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

		///////
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
		
		
		//nell if qua sotto per qualche motivo vengono eliminati i metadati nelle funzioni moveto_module
		//
		//
		//
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
			
		//$tabella_file = 'mdl_'.$typeModule; 
			

	/*switch($typeModule){
				case 'assign': 
					$tabella_file = 'mdl_assign';
					$typeModule = 1;
					break;
				case 'book': 
					$tabella_file = 'mdl_book';	
					$typeModule = 3;
					break;
				case 'chat': 
					$tabella_file = 'mdl_chat';
					$typeModule = 4;
					break;
				case 'choice': 
					$tabella_file = 'mdl_choice';
					$typeModule = 5;
					break;
				case 'data': 
					$tabella_file = 'mdl_data';
					$typeModule = 6;
					break;
				case 'folder': 
					$tabella_file = 'mdl_folder';
					$typeModule = 8;
					break;
				case 'forum': 
					$tabella_file = 'mdl_forum';
					$typeModule = 9;
					break;
				case 'glossary': 
					$tabella_file = 'mdl_glossary';
					$typeModule = 10;
					break;
				case 'imscp': 
					$tabella_file = 'mdl_imscp';
					$typeModule = 11;
					break;
				case 'label': 
					$tabella_file = 'mdl_label';
					$typeModule = 12;
					break;
				case 'lesson': 
					$tabella_file = 'mdl_lesson';
					$typeModule = 13;
					break;
				case 'lti': 
					$tabella_file = 'mdl_lti';
					$typeModule = 14;
					break;
				case 'page': 
					$tabella_file = 'mdl_page';
					$typeModule = 15;
					break;
				case 'quiz': 
					$tabella_file = 'mdl_quiz';
					$typeModule = 16;
					break;
				case 'resource': 
					$tabella_file = 'mdl_resource';
					$typeModule = 17;
					break;
				case 'scorm': 
					$tabella_file = 'mdl_scorm';
					$typeModule = 18;
					break;
				case 'survey': 
					$tabella_file = 'mdl_survey';	
					$typeModule = 19;
					break;
				case 'url': 
					$tabella_file = 'mdl_url';
					$typeModule = 20;
					break;
				case 'wiki': 
					$tabella_file = 'mdl_wiki';
					$typeModule = 21;
					break;
				case 'workshop': 
					$tabella_file = 'mdl_workshop';
					$typeModule = 22;
					break;
		}*/
		
		//sostituito switch statico con nome tabelle preso direttamente da DB
			$tabella_file = $DB->get_record('modules', array('name'=>$typeModule));
			$typeModule = $tabella_file->id;
			$tabella_file = $tabella_file->name;
			$tabella_file = "mdl_".$tabella_file;



		//Recupero l'id della risorsa duplicata all'interno della tabella corrispondente ('mdl_page' oppure 'mdl_url' ecc...)
		$sql="SELECT MAX(id) AS idmax, course FROM ".$tabella_file."";	
		$fields = $DB->get_records_sql($sql);
		foreach ($fields as $field)
			$idMaxRisorsa  = $field->idmax;	

		//Cambio l'id del corso relativo alla risorsa duplicata mettendogli l'id del corso destinazione, cioè il corso appena creato	
		$sql="UPDATE ".$tabella_file." SET course = '".$courseDest."' WHERE id = '".$idMaxRisorsa."'";
		$DB->execute($sql);

		//Recupero l'id della sezione di partenza della risorsa
		//$result = mysqli_query($connection, "SELECT DISTINCT id_course, id_course_sections FROM mdl_metadata WHERE id_resource = '".$cmid."'" );
		//$fields = mysqli_fetch_assoc($result);
		$result = $DB->get_record_sql("SELECT DISTINCT course, section FROM mdl_course_modules WHERE id = ".$cmid."");
		$id_sezione = $result->section; //salvo l'id della sezione di partenza in $id_sezione
		
		//echo "chmid: ".$cmid." /\n";
		
		// Query che seleziona il campo "sequence" della sezione all'interno della quale la risorsa è stata duplicata
		$sql="select id, sequence from mdl_course_sections WHERE id = '".$id_sezione."'";
		$fields = $DB->get_records_sql($sql);
		foreach ($fields as $field)
			$sequenceFinale  = $field->sequence;	

		$array = explode(",", $sequenceFinale);

		$sequenceDelFile = max($array);	


		$sql="CALL update_database('".$id_sezione."', '".$sequenceDelFile."', '".$sequenceIniziale."', '".$courseDest."', '".$id_dest_sez."', '".$typeModule."')";
		//print_r($sql);
		$DB->execute($sql);

		//inserimento dei metadati associati alle risorse e propagati sul modulo e sul corso (parte 2)
		$sql="SELECT DISTINCT m.id AS id_risorsa FROM mdl_course_modules m, ".$tabella_file." f WHERE m.instance = ".$idMaxRisorsa." AND m.module = ".$typeModule."";
		$fields = $DB->get_records_sql($sql);
		foreach ($fields as $field)
			$id_risorsa_modulo = $field->id_risorsa;

		$sql="SELECT Id_metadata, property, value FROM mdl_metadata WHERE id_resource = '".$cmid."' AND id_course IS NOT NULL AND id_course_sections IS NOT NULL";
		$fields = $DB->get_records_sql($sql);
		foreach ($fields as $field) {
			$property  = $field->property;
			$value  = $field->value;

			$sql="INSERT INTO mdl_metadata(id_course, id_course_sections, id_resource, property, value, courseGrade) VALUES ('".$courseDest."', '".$id_dest_sez."', '".$id_risorsa_modulo."', '".$property."', '".$value."', 0)";
			$fields = $DB->execute($sql);
		
		}


	}
}

//setting timestamp of section and course editing 
//needed in order to show the new resources in the course
//removed for section becouse timemodified not aviable for old moodle versions
//$param['timemodified'] = time();
//$param['id'] = $id_dest_sez;
///echo("destsez ".$id_dest_sez);
//$DB->update_record_raw('course_sections', $param);
//$param = new stdClass();
//$DB->execute('UPDATE mdl_course_sections SET timemodified = '.$param['timemodified'].' WHERE id='.$param['id'].'');
$param = new stdClass();
$param->id = $courseDest;
$param->timemodified = time();
echo("courseDest ".$courseDest);
//update_course($param);
$DB->execute('UPDATE mdl_course SET timemodified = '.$param->timemodified.',cacherev  = '.$param->timemodified.' WHERE id='.$param->id.'');


// Unset completo di tutta la variabile $_SESSION, ad eccezione del login (USER)
$_SESSION = array_intersect_key($_SESSION, array_flip(array('USER')));

echo("done!");

?>
