<?php
	require_once('../config.php');
	require_once($CFG->libdir .'/mysql_conn.php');
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

	
	?>

		<center>
			<div class="controls" style="width: 25%;">
			<?php echo convert_RS('Course is building....'); ?>
				<img id='loading' src='themes/spinner/ajax-loader.gif'>
				<?php
					if (isset($_POST['parteFinita']))
					{
						// Vuol dire che sto tornando da conclude.php, il tasto back mi deve far tornare al modulo precedente
						$action = "step5.php";
						$value = $_POST['parteFinita'];
					}
					else
					{
						$action = "index.php";
						$value = "stop";
					}

				?>
				<form id="formBack" name="formBack" method="post" action="<?php print $action; ?>" style="float:left;">
					<input name="i_back" id="i_back" value="<?php print $value; ?>" style="display: none;"/>
				</form>
				<form id="formNext" name="formNext" method="post" action="step2.php" style="float:right;"/>					
			</div>
		</center>

	
	<?php
	
	$PAGE->set_context(context_system::instance());
	echo $OUTPUT->header();

if (has_capability('moodle/course:managefiles', context_system::instance()))

{

	$doFase = 5;

	//potrei mettere qui tutti gli unset di $_SESSION['valuesvar'] e $_SESSION['rules_risp'] e $_SESSION['modn'] e $_SESSION['resources']
	/*unset($_SESSION['modulesvar']);
	unset($_SESSION['ka']);
	unset($_SESSION['reg']);
	unset($_SESSION['rules']);
	unset($_SESSION['valuesvar']);
	unset($_SESSION['keywords_selezionate']);


	unset($_SESSION['data']);
	unset($_SESSION['course']);
	unset($_SESSION['fromMy']);

	unset($_SESSION['rules_risp']);
	unset($_SESSION['modn']);
	unset($_SESSION['resources']);*/


	//le prossime 5 istruzioni sono commentate per provare la creazione di un corso da un file txt di prova... alla fine andarnno scommentate
	$sessione = $_POST["session"];
	$sessione = base64_decode($sessione);
	$sessione = json_decode($sessione, true);

	$token = $sessione['data']['token'];

	$course = array();
	$course["course"] = $sessione["course"];
	$course["token"] = $token;


	//echo "sessione= ".print_r($sessione)." </br>";

	//Compongo il messaggio da inviare alla porta 20005. Messaggio di stop.
	$msg = array();
	$msg["token"] = $token;
	$msg["action"] = "stop";
	$msg["doFase"] = $doFase;
	$message = json_encode($msg);

	$message = $message . "\r\n";

	error_reporting(E_ALL);
	set_time_limit(0);
	ob_implicit_flush();

	$address = '::1';
	$port = 20005;

	if (($sock = socket_create(AF_INET6, SOCK_STREAM, 0)) === false) {
	   echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
	   exit(0);
	}
	
 	socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 30, 'usec' => 0));
	socket_set_option($sock, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 30, 'usec' => 0)); 

	if (socket_connect($sock, $address, $port) === false) {
		echo "socket_connect() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
		exit(0);
	}

	// invio primo messaggio alla socket 
	if (socket_write($sock, $message, strlen($message)) === false){
		echo "socket_write() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
		exit(0);
	}

	$response = socket_read($sock,2048,PHP_NORMAL_READ);
	socket_close($sock);
	



	// IN $course HO TUTTO IL CORSO CON ANCHE IL TOKEN

	//---------------------------------------------Inizio creazione corso-----------------------------------------------------------------
	//Questa funzione ripulisce i campi che poi andranno all'interno delle query
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

		$string = str_replace("[^A-Za-z0-9: \-]", "", $string );
		return $string;
	}


	//Assegnamento id categoria
	$category = $sessione['course']['ka'];
	
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
		case 'Probability and Statistics':
			$categoryId = 22;
			break;
		
	}
	
	//debug
	//echo "categoryId:  ".$categoryId." \n/n";

	//riempo l'object $data con i dati che mi servono per creare il corso
	$data->category = $categoryId;
	$data->fullname = cleanString($sessione['course']['name']);
	$data->summary = cleanString($sessione['course']['name']);
	
	//debug
	//echo "data category -> ".$data->category." data fullname e summary -> ".$data->fullname."";
	
	//CREO IL CORSO
	$corso = create_course($data);

	// Inserisco la referenza del corso con l'utente --- Get the context of the newly created course
	$context = context_course::instance($corso->id, MUST_EXIST);

	if (!empty($CFG->creatornewroleid) and !is_viewing($context, NULL, 'moodle/role:assign') and !is_enrolled($context, NULL, 'moodle/role:assign')) {
		// deal with course creators - enrol them internally with default role
		enrol_try_internal_enrol($corso->id, $USER->id, $CFG->creatornewroleid);
	}

	$id_utente = $USER->id;


//////////FIN QUI OK /////////////////////////////////////////////////////////////////////////////////

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
	
	//Inserisco nella tabella mdl_metadata la categoria del nuovo corso: Modificato da Hui(03/09)
	//echo "INSERT INTO mdl_metadata(Id_course, property,value) VALUES(".$courseDest.",'CATEGORY',".$category."  )" ;
	$result = mysqli_query($connection, "INSERT INTO mdl_metadata(Id_course, property,value,courseGrade) VALUES(".$courseDest.",'CATEGORY', '".$category."' , 0)" );
	
	

	// Seleziono il valore di value	(value è il numero di sezioni -Moduli- di un determinato corso)
	$result = mysqli_query($connection, "SELECT id, value from mdl_course_format_options WHERE courseid = '".$courseDest."' AND name = 'numsections'" );
	$value = mysqli_fetch_assoc($result);
	$value = $value['value'];

	// INSERISCO LE NUOVE SEZIONI SENZA IL CAMPO SEQUENCE (perche devo prima duplicare le risorse in modo tale che abbiano l'id giusto), incremento $value e salvo i loro id nell'array $modid
	$modid = array();
	$numParts = count($sessione['course']['parts']); //numero delle parti che compongono il corso
	$modNames = array();
	
	for($i=1; $i <= $numParts;$i++){
		$modNames[$i-1] = $sessione['course']['parts'][$i]['name']; //dentro $modNames ci sono tutti i nomi di tutte le parti del corso --- count($numParts) = count($modNames) --- Array ( [0] => Speaking [1] => Listening [2] => Decode Info )
		
		//debug
		//echo "course parts i : ".$sessione['course']['parts'][$i]['name']."</br>";
	}

	$modResources = array();
	
	for($i = 1; $i <= $numParts; $i++)
	{ //con questo for compongo il campo sequence per ogni parte del corso
		$idRes = array();
		$numResources = count($sessione['course']['parts'][$i]['resources']);
		
		for($j = 0; $j < $numResources; $j++)
			array_push($idRes, $sessione['course']['parts'][$i]['resources'][$j]['id']); //istruzione originale di inserimento id risorsa da recommender
		
		$sequenza = implode(",", $idRes);
		$modResources['part'.$i] = $sequenza; //$modResource è: Array ( [part1] => 268 [part2] => 318,319 [part3] => 355,356,357 ) --> contiene i valori del campo sequence prima della duplicazione
	}


	$connection = GetMyConnection();
	if ($connection < 0)
	{
		print errorDB($connection);
		die();
	}
	
	//debug
	//echo "modnames: ".print_r($modNames)."</br>";

	for($i=0; $i<count($modNames); $i++)
	{
		$parte = $i+1;
		$nameSection = $modNames[$i];
		$nameSection = cleanString($nameSection);
		
		//debug
		/*
		echo "courseDest: ".$courseDest."</br>";
		echo "value: ".$value."</br>";
		echo "value: ".$nameSection."</br>";
		*/
		
		$result = mysqli_query($connection, "INSERT INTO mdl_course_sections(course, section, name, summary, summaryformat, visible) VALUES ('".$courseDest."', '".$parte."', '".$nameSection."', '".$nameSection."', 1, 1)" );
		//$result = mysqli_query($connection, "INSERT INTO mdl_course_sections(course, section, name, summary, summaryformat, visible, availablefrom, availableuntil, showavailability, groupingid) VALUES ('".$courseDest."', '".$value."', '".$nameSection."', '".$nameSection."', 1, 1, 0, 0, 0, 0)" );
		$result = mysqli_query($connection, "SELECT max(id) as id FROM mdl_course_sections WHERE name = '".$nameSection."'");
		$idSectionResult = mysqli_fetch_assoc($result);
		$idSection = $idSectionResult['id'];

		//Inserisco lo status (yellow) del nuovo modulo in mdl_metadata
		$result = mysqli_query($connection, "INSERT INTO mdl_metadata(Id_course, Id_course_sections, property, value) VALUES ('".$courseDest."', '".$idSection."', 'status', 'yellow')" );
		$result = mysqli_query($connection, "INSERT INTO nett_developedby(user_id, module_id, date, fromrs) VALUES (".$id_utente.", ".$idSection.", '".date('Y-m-d')."', 1)" );

		$modid[$i] = $idSection; //dentro $modid salvo l'id delle sezioni nuove appena inserite
		
		//echo "idsection: ".$idSection."</br>";
		
		
		$value = $value + 1;
	}

	$connection = GetMyConnection();
	if ($connection < 0)
	{
		print errorDB($connection);
		die();
	}
	// Aggiorno il campo value (cioè aumento il numero di moduli all'interno del corso da modificare)
	$result = mysqli_query($connection, "UPDATE mdl_course_format_options SET value='".$value."' WHERE courseid = '".$courseDest."' AND name = 'numsections'" );


////////////////fin qua non cancella ma controllare errore sotto (compare solo alcune volte)

/*
Debug info: SELECT * FROM {course_categories} WHERE id IS NULL
[array (
)] 
Error code: invalidrecord
×Stack trace:
line 1533 of \lib\dml\moodle_database.php: dml_missing_record_exception thrown
line 1509 of \lib\dml\moodle_database.php: call to moodle_database->get_record_select()
line 2445 of \course\lib.php: call to moodle_database->get_record()
line 168 of \recommender_system\create_course.php: call to create_course()
*/

//////////////////////////////77


	//Scorro tutti i moduli (sezioni) che sono stati appena inseriti (si trovano nell'array $modid)
	$numModules = count($modid);
	for($i=0; $i < $numModules;$i++)
	{

		$id_dest_sez = $modid[$i];  //di volta in volta ogni modulo sarà salvato in $id_dest_sez --- modulo destinazione
		//echo "modid: ".print_r ($modid)."</br>";
		$p = $i+1;
		$id_res_before = $modResources['part'.$p]; //campo sequence della parte 1, 2 ,3 ecc... che si trovano in $modResource

		//Salvo in $sequenceValues tutti i valori (risorse) contenuti in $modResource. Sarà circa così: Array ( [0] => 537 [1] => 554 [2] => 549 )
		$sequenceValues = array_map('intval', explode(",", $id_res_before));

		$numeroFile = count($sequenceValues); //numero di valori del campo sequence della sezione in questione


		//echo "numero file: ".$numeroFile."</br>";
		//Duplicazione risorsa
		for($j = 0; $j < $numeroFile; $j++)
		{
			$cmid = $sequenceValues[$j]; //in cmid c'è l'id della risorsa (id vecchio) associata al modulo nuovo (part1, 2, 3...)
			$result = mysqli_query($connection, "SELECT id, module FROM mdl_course_modules WHERE id = '".$cmid."'" );
			$fields = mysqli_fetch_assoc($result);
			$typeModule = $fields['module']; //typeModule è il tipo di risorsa (quale tra le 22 tabelle)
			$result = mysqli_query($connection, "SELECT DISTINCT id_course, id_resource FROM mdl_metadata WHERE id_resource = '".$cmid."'");
			$fields = mysqli_fetch_assoc($result);
			$courseid = $fields['id_course']; //id del corso al quale appartiene la risorsa in cmid

			//fin qui ho recuperato 'id della risorsa'($cmid), 'tipo di risorsa'($typeModule) e 'corso di appartenenza'($courseid) della risorsa

			//prima di procedere con la duplicazione recupero il campo "sequence" della sezione alla quale appartiene la risorsa originaria
			$result = mysqli_query($connection, "SELECT DISTINCT id_course, id_course_sections FROM mdl_metadata WHERE id_resource = '".$cmid."'" );
			$fields = mysqli_fetch_assoc($result);
			$sect = $fields['id_course_sections']; //in $sect ho l'id della sezione alla quale appartiene la risorsa
			$result = mysqli_query($connection, "SELECT id, course, name, summary, sequence FROM mdl_course_sections WHERE id = '".$sect."'" );
			$fields = mysqli_fetch_assoc($result);
			$sequenceIniziale = $fields['sequence']; //in $sequenceIniziale ho il campo sequence della sezione alla quale appartiene la risorsa (il tutto prima della duplicazione)



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
//

			/*
			switch($typeModule)
			{
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
			*/
			
			//sostituito switch statico con nome tabelle preso direttamente da DB
			$tabella_file = $DB->get_record('modules', array('id'=>$typeModule));
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
			$connection = GetMyConnection();
			if ($connection < 0)
			{
				print errorDB($connection);
				die();
			}
			
			//MODIFICO: non recupero piu` sezione da metadati ma da tabella contente le risorse che e` piu` affidabile
			//
			//
			//$result = mysqli_query($connection, "SELECT DISTINCT id_course, id_course_sections FROM mdl_metadata WHERE id_resource = '".$cmid."'" );
			//$fields = mysqli_fetch_assoc($result);
			//$id_sezione = $fields['id_course_sections']; //salvo l'id della sezione di partenza in $id_sezione
			$result = mysqli_query($connection, "SELECT DISTINCT course, section FROM mdl_course_modules WHERE id = '".$cmid."'" );
			$fields = mysqli_fetch_assoc($result);
			$id_sezione = $fields['section']; //salvo l'id della sezione di partenza in $id_sezione
			
			//debug
			//echo "chmid: ".$cmid." /\n";
			//echo "id section: ".$id_sezione." /\n";
			
			// Query che seleziona il campo "sequence" della sezione all'interno della quale la risorsa è stata duplicata
			$sql="select id, sequence from mdl_course_sections WHERE id = '".$id_sezione."'";
			$fields = $DB->get_records_sql($sql);
			foreach ($fields as $field)
			$sequenceFinale  = $field->sequence;	

			$array = explode(",", $sequenceFinale);

			$sequenceDelFile = max($array);

			//Query che seleziona il campo "sequence" della sezione di destinazione della risorsa cioè $sequence

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
				$value  = addslashes($field->value);


				$sql="INSERT INTO mdl_metadata(id_course, id_course_sections, id_resource, property, value, courseGrade) VALUES ('".$courseDest."', '".$id_dest_sez."', '".$id_risorsa_modulo."', '".$property."', '".$value."', 0)";
				$fields = $DB->execute($sql);
			
			}

		}
	}
	//Una volta inserito il corso completo, cancello quello incompleto e automaticamente si cancellano anche i relativi moduli
	$connection = GetMyConnection();
	if ($connection < 0)
	{
		print errorDB($connection);
		die();
	}
	$query = "DELETE FROM mdl_rs_incomplete_course WHERE token = '".$token."'";
	$result = mysqli_query($connection, $query);


	// Unset completo di tutta la variabile $_SESSION, ad eccezione del login (USER)
	$_SESSION = array_intersect_key($_SESSION, array_flip(array('USER')));

	echo '<meta http-equiv="refresh" content="0; url=' . $CFG->wwwroot . '/course/view.php?id='.$corso->id.'" />';
}

?>
