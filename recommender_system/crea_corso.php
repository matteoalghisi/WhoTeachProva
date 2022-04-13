<?php

function cleanString($string)
{
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

	$string = preg_match("[^A-Za-z0-9: \-]", "", $string);
	return $string;
}

/**
 * Crea una risorsa di tipo link nel database
 */
function createExternalResourceLink($platform, $cmid, $courseDest, $id_dest_sez)
{
	global $DB;

	$resource = $DB->get_record_sql("SELECT id, link, title, description FROM mdl_" . $platform . "_data WHERE id = " . $cmid . "");

	$maxResourceId = $DB->insert_record('url', array('course' => $courseDest, 'name' => $resource->title, 'intro' => $resource->description, 'externalurl' => $resource->link));

	$maxId = $DB->insert_record('course_modules', array('course' => $courseDest, 'module' => 20, 'instance' => $maxResourceId, 'section' => $id_dest_sez));

	$DB->execute("INSERT INTO mdl_" . $platform . "_id(id, externalid) VALUES (" . $maxId . "," . $cmid . ")");		// Crea il mapping tra id di course_modules e l'id della tabella della risorsa esterna (es. mdl_merlot_data)

	$DB->execute("UPDATE mdl_course_sections SET sequence = CONCAT(sequence,'," . $maxId . "') WHERE mdl_course_sections.id = $id_dest_sez");
}

// Require both the backup and restore libs
require_once('../config.php');
require_once('../search_engine/php/mysql_conn.php');
require_once('../course/lib.php');

require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_plan_builder.class.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->dirroot . '/backup/util/ui/import_extensions.php');

require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->libdir . '/filelib.php');
require_once('../lib/filelib.php');

require_once('../metadata/metadata_page/aux_functions.php');

global $DB;


$PAGE->set_pagetype('site-index');
$PAGE->set_docs_path('');
$PAGE->set_pagelayout('frontpage');
//$editing = $PAGE->user_is_editing();
require_login();
$PAGE->set_context(context_system::instance());
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_url($CFG->wwwroot."/recommender_system/create_course");
$courserenderer = $PAGE->get_renderer('core', 'course');

$PAGE->set_context(context_system::instance());



if (!has_capability('moodle/course:managefiles', context_system::instance()))
	die();



	//INFORMAZIONI IN INPUT
	$nomeCorso=$_POST['courseName'];
    $categoriaCorso=$_POST['courseCategory'];
    $sottocategoriaCorso=$_POST['courseSubcategory'];
    $sezioniCorso=$_POST['courseSections'];
    $risorseCorso=$_POST['idResources'];
	$titoloRisorse=$_POST['titleResources'];
	$moduliRisorse=$_POST['moduleResources'];

	//debug
	//echo json_encode($moduliRisorse)."\n";

	//debug
	//echo "sottocategoria: ".$sottocategoriaCorso."\n";

	//debug
	//echo "nome corso: ".cleanString($nomeCorso)."\n";


	//debug
	//echo "sequenza risorse: ".$sequenzaRisorse;


	//debug
	//echo "nomi sezioni: ".json_encode($nomiSezioni)."\n";



	//Assegnamento id categoria
	if($sottocategoriaCorso!=""){
		$category = $sottocategoriaCorso;
	}else{
		$category=$categoriaCorso;
	}
	
	switch ($category) 
	{
		case 'Abilità informatiche':
			$categoryId = 1;
			break;
		case 'Entrepreneurial Vision':
			$categoryId = 2;
			break;
		case 'Personal Development':
			$categoryId = 3;
			break;
		case 'Communication Skills':
			$categoryId = 4;
			break;
		case 'Economic Skills':
			$categoryId = 5;
			break;
		case 'Technical Skills':
			$categoryId = 6;
			break;
		case 'Competenze in Economia':
			$categoryId = 14;
			break;
		case 'Abilità Comunicative':
			$categoryId = 15;
			break;
		case 'Sviluppo Personale':
			$categoryId = 16;
			break;
		case 'Visione imprenditoriale':
			$categoryId = 20;
			break;
		case 'SIAM':
			$categoryId = 22;
			break;
		case 'nuovaCategoria':
			$categoryId = 23;
			break;
		case 'nuovaCategoria2':
			$categoryId = 24;
			break;
		
	}

	

	
	//debug
	//echo "categoryId:  ".$categoryId."\n";

	//riempo l'object $data con i dati che mi servono per creare il corso
	$data=new stdClass();
	$data->category = $categoryId;
	$data->fullname = $nomeCorso;
	$data->summary = $nomeCorso;


	
	//debug
	//echo "data category -> ".$data->category.", data fullname e summary -> ".$data->fullname."";
	
	//CREO IL CORSO
	$corso = create_course($data);


	// Inserisco la referenza del corso con l'utente --- Get the context of the newly created course
	$context = context_course::instance($corso->id, MUST_EXIST);

	if (!empty($CFG->creatornewroleid) and !is_viewing($context, NULL, 'moodle/role:assign') and !is_enrolled($context, NULL, 'moodle/role:assign')) {
		// deal with course creators - enrol them internally with default role
		enrol_try_internal_enrol($corso->id, $USER->id, $CFG->creatornewroleid);
	}

	$id_utente = $USER->id;



	// Seleziono l'id del corso appena inserito
	$connection = GetMyConnection();
	if ($connection < 0)
	{
		print errorDB($connection);
		die();
	}
	$result = mysqli_query($connection, "SELECT max(id) as idmax FROM mdl_course" );
	$courseDest = mysqli_fetch_assoc($result);
	$courseDest = $courseDest['idmax'];

	//debug
	//echo "id corso: ".$courseDest."\n";
	
	//Inserisco nella tabella mdl_metadata la categoria del nuovo corso: Modificato da Hui(03/09)
	//echo "INSERT INTO mdl_metadata(Id_course, property,value) VALUES(".$courseDest.",'CATEGORY',".$category."  )" ;
	$result = mysqli_query($connection, "INSERT INTO mdl_metadata(Id_course, property,value,courseGrade) VALUES(".$courseDest.",'CATEGORY', '".$category."' , 0)" );

	
	
	// Seleziono il valore di value	(value è il numero di sezioni -Moduli- di un determinato corso)
	$result = mysqli_query($connection, "SELECT id, value from mdl_course_format_options WHERE courseid = '".$courseDest."' AND name = 'numsections'" );
	$value = mysqli_fetch_assoc($result);
	$value = $value['value'];




	$connection = GetMyConnection();
	if ($connection < 0)
	{
		print errorDB($connection);
		die();
	}



	$sezioniRisorse=array();
	

	for($i=1; $i<=count($moduliRisorse); $i++)
	{

		$index=$i-1;
		$numeroSezione=$i;
		$nameSection = $moduliRisorse[$index]['modulo'];

		$risorseModulo=$moduliRisorse[$index]['idrisorse'];
		$numeroRisorse=0;
		$sequenzaRisorse='';

		foreach($risorseModulo as $risorsa){
			$numeroRisorse=$numeroRisorse+1;
			$sequenzaRisorse.=$risorsa.",";
		}

		$sequenzaRisorse=substr($sequenzaRisorse,0,-1);
		

		//debug
		//echo "sequenza risorse: ".$sequenzaRisorse."\n";

		//debug
		//echo "sezione: ".$nameSection."\n";

		//debug
		//echo "nome sezione: ".$nameSection."\n";

		

		if($numeroRisorse>0){

			$result = mysqli_query($connection, "INSERT INTO mdl_course_sections(course, section, name, summary, sequence, summaryformat, visible) VALUES (".$courseDest.", ".$numeroSezione.", '".$nameSection."', '', '".$sequenzaRisorse."', 1, 1)" );
			//$result = mysqli_query($connection, "INSERT INTO mdl_course_sections(course, section, name, summary, summaryformat, visible, availablefrom, availableuntil, showavailability, groupingid) VALUES ('".$courseDest."', '".$value."', '".$nameSection."', '".$nameSection."', 1, 1, 0, 0, 0, 0)" );
			$result = mysqli_query($connection, "SELECT max(id) as id FROM mdl_course_sections WHERE name = '".$nameSection."'");
			$idSectionResult = mysqli_fetch_assoc($result);
			$idSection = $idSectionResult['id'];
			array_push($sezioniRisorse,$idSection);

		}else{

			$result = mysqli_query($connection, "INSERT INTO mdl_course_sections(course, section, name, summary, sequence, summaryformat, visible) VALUES (".$courseDest.", ".$numeroSezione.", '".$nameSection."', '', '".null."', 1, 1)" );
			//$result = mysqli_query($connection, "INSERT INTO mdl_course_sections(course, section, name, summary, summaryformat, visible, availablefrom, availableuntil, showavailability, groupingid) VALUES ('".$courseDest."', '".$value."', '".$nameSection."', '".$nameSection."', 1, 1, 0, 0, 0, 0)" );
			$result = mysqli_query($connection, "SELECT max(id) as id FROM mdl_course_sections WHERE name = '".$nameSection."'");
			$idSectionResult = mysqli_fetch_assoc($result);
			$idSection = $idSectionResult['id'];
			array_push($sezioniRisorse,$idSection);
		}



		//Inserisco lo status (yellow) del nuovo modulo in mdl_metadata
		$result = mysqli_query($connection, "INSERT INTO mdl_metadata(Id_course, Id_course_sections, property, value) VALUES (".$courseDest.", ".$idSection.", 'status', 'yellow')" );
		$result = mysqli_query($connection, "INSERT INTO nett_developedby(user_id, module_id, date, fromrs) VALUES (".$id_utente.", ".$idSection.", '".date('Y-m-d')."', 1)" );
		
			


		//debug
		//echo "idsection: ".$idSection."\n";
		
		
	}


	

	//debug
	//echo "id corso: ".$courseDest."\n";

	//debug
	//echo "modid: ".json_encode($modid)."\n";
	

	$connection = GetMyConnection();
	if ($connection < 0)
	{
		print errorDB($connection);
		die();
	}
	// Aggiorno il campo value (cioè aumento il numero di moduli all'interno del corso da modificare)
	$result = mysqli_query($connection, "UPDATE mdl_course_format_options SET value=".count($moduliRisorse)." WHERE courseid = ".$courseDest." AND name = 'numsections'" );

	//inserisco il record del corso con automaticenddate
	$result = mysqli_query($connection, "INSERT INTO mdl_course_format_options(courseid, format, sectionid, name, value) VALUES (".$courseDest.", 'weeks', 0, 'automaticenddate', 1)" );

	

// * - cmid: The Course module id (id of resource)
// * - typeModule: The Course module type (type of resource)	

for ($j = 0; $j < count($moduliRisorse); $j++) {

	$risorseModulo=$moduliRisorse[$j]['idrisorse'];

	foreach($risorseModulo as $risorsa){

		$cmid = $risorsa;		
		
	
		if ($cmid != '') {
	
	
			$result = $DB->get_record_sql("SELECT DISTINCT id, course FROM mdl_course_modules WHERE id = " . intval($cmid) . "");
			$courseid = $result->course; //id del corso al quale appartiene la risorsa in cmid (vecchia)
	
			
			//fin qui ho recuperato 'id della risorsa'($cmid), 'tipo di risorsa'($typeModule) e 'corso di appartenenza'($courseid) della risorsa
	
			/**********************************************************/
			/* PARTE PHP CHE SI OCCUPA DI CREARE LINK RISORSA ESTERNE */
			/**********************************************************/
			createExternalResourceLink('merlot', $cmid, $courseDest, $sezioniRisorse[$j]);
			
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



/*$param = new stdClass();
$param->id = $courseDest;
$param->timemodified = time();
echo ("courseDest " . $courseDest);
//update_course($param);
$DB->execute('UPDATE mdl_course SET timemodified = ' . $param->timemodified . ',cacherev  = ' . $param->timemodified . ' WHERE id=' . $param->id . '');




// Unset completo di tutta la variabile $_SESSION, ad eccezione del login (USER)
$_SESSION = array_intersect_key($_SESSION, array_flip(array('USER')));*/

$_SESSION["courseId"]=$courseDest;

echo $_SESSION["courseId"];

