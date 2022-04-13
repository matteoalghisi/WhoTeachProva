<?php

	/***********************************************/
	/* Inserimento nel DB di moodle	dei metadati   */
	/***********************************************/

	//INSERIMENTO DEI METADATI ASSOCIATI A UNA RISORSA
	require_once($CFG->dirroot.'/metadata/metadata_page/metadata_functions.php');//s_t_mod Hui
	
    $data = $mform->get_data();

	//recupero l'id del corso padre
	$parent_course = $data->course;

	//recupero l'id della sezione padre
	$parent_section = $data->section;
	$sql="SELECT id FROM mdl_course_sections WHERE course = '".$parent_course."' AND section = '".$parent_section."'";
	$fields = $DB->get_records_sql($sql);
	foreach($fields as $field) {
		$parent_section = $field->id;
	}

	$add    = optional_param('add', '', PARAM_ALPHA);  
	
	//recupero l'id della risorsa
	$sql="SELECT max(id) AS id_r FROM mdl_course_modules";
	$fields = $DB->get_records_sql($sql);
	foreach($fields as $field) {
		$id_resource = $field->id_r;
	}
	
	$sql = "SELECT module,instance
	FROM {course_modules}
	WHERE id = $id_resource AND deletioninprogress = 0";
	if ($record = $DB->get_record_sql($sql)) {
		$moduleID = $record->module;
		$resourceIstance = $record->instance;
	}
	/*
	switch($moduleID)
	{
		case 1: $tabella_file = 'mdl_assign';
		break;
		// manca il 2
		case 3: $tabella_file = 'mdl_book';	
		break;
		case 4: $tabella_file = 'mdl_chat';
		break;
		case 5: $tabella_file = 'mdl_choice';
		break;
		case 6: $tabella_file = 'mdl_data';
		break;
		case 7: $tabella_file = 'mdl_folder';
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
		case 14: $tabella_file = 'mdl_lti';  // tool esterno?
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
		case 26: $tabella_file = 'mdl_bigbluebuttonbn';
		break;
		case 27: $tabella_file = 'mdl_zoom';
		break;
		case 28: $tabella_file = 'mdl_h5pactivity';
		break;
		case 30: $tabella_file = 'mdl_attendanceregister';
		break;
		case 31: $tabella_file = 'mdl_customcert';
		break;	
	}*/
	
	//sostituito switch statico con nome tabelle preso direttamente da DB
	$tabella_file = $DB->get_record('modules', array('id'=>$moduleID));
	$tabella_file = $tabella_file->name;
	$tabella_file = "mdl_".$tabella_file;
	
	
	$sql = "SELECT name
	FROM $tabella_file
	WHERE id = $resourceIstance";
	if ($record = $DB->get_record_sql($sql)) {
		$resourceName = $record->name;
	}

	
	
	//recupero valutazione del corso
	$sql = "SELECT ROUND(AVG(mdl_block_rate_course.rating*2)) as grade
			FROM mdl_block_rate_course
			WHERE mdl_block_rate_course.course = ".$parent_course."
			";
	if($DB->get_record_sql($sql)->grade != null)
		$courseGrade = $DB->get_record_sql($sql)->grade;
	else 
		$courseGrade = 0;
	//voto corso recuperato..

	//Language
	$index0 = $data->Language;
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'language'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}

    $sql="INSERT INTO mdl_metadata(id_course, id_course_sections, id_resource, property, value, courseGrade) VALUES ($parent_course, $parent_section, $id_resource, 'language', '".$choices[$index0]."', $courseGrade)";
    $DB->execute($sql);
	
	//recupero nome del corso e categoria di appartenenza
	$sql = "SELECT category,fullname FROM mdl_course WHERE id = '".$parent_course."'";
	if($record = $DB->get_record_sql($sql)){
		$courseName = $record->fullname;
		$categoryId = $record->category;
	}
	
	//s_t_mod Hui
	/*switch($categoryId)
	{
		case 1: $nomeCategoria = 'Abilità Informatiche';	
		break;
		case 14: $nomeCategoria = 'Competenze in economia';
		break;
		case 15: $nomeCategoria = 'Abilità comunicative';
		break;
		case 16: $nomeCategoria = 'Sviluppo personale';
		break;
		case 20: $nomeCategoria = 'Visione imprenditoriale';
		break;
		case 22: $nomeCategoria = 'Probability and Statistics';
		break;
	}*/
	
	$sql = "SELECT name FROM mdl_course_categories WHERE id = '".$categoryId."'";
	if($record = $DB->get_record_sql($sql)){
		$nomeCategoria=$record->name;
	}
	//-------------------------------------------------------------------
	
	$lang = current_language();
	
	//print_object($sql);
/* 	echo $courseName;
	echo '<br/>';
	echo $nomeCategoria; */
	//$resourceName = addslashes($resourceName);
	//$courseName = addslashes($courseName);
	/*$sql="INSERT INTO mdl_metadata(id_course, id_course_sections, id_resource, property, value, courseGrade) VALUES 
		($parent_course, $parent_section, $id_resource, 'keywords', ?, $courseGrade),
		($parent_course, $parent_section, $id_resource, 'keywords', ?, $courseGrade),
		($parent_course, $parent_section, $id_resource, 'keywords', ?, $courseGrade)";
	echo($sql);
	$params = [$nomeCategoria, $courseName, $resourceName];
    $DB->execute($sql, $params);*/
	
	//s_t_mod Hui
	$resourceName_array=removeCommonWords($resourceName);
	$courseName_array=removeCommonWords($courseName);
	
	$keywords_array=array_merge($resourceName_array,$courseName_array);
	
	/*$sql="INSERT INTO mdl_metadata(id_course, id_course_sections, id_resource, property, value, courseGrade) VALUES 
		($parent_course, $parent_section, $id_resource, 'keywords', \"".$nomeCategoria."\", $courseGrade),
		($parent_course, $parent_section, $id_resource, 'keywords', \"".$courseName."\", $courseGrade),
		($parent_course, $parent_section, $id_resource, 'keywords', \"".$resourceName."\", $courseGrade)";*/
	
	foreach($keywords_array as $kw){
		$sql="INSERT INTO mdl_metadata(id_course, id_course_sections, id_resource, property, value, courseGrade) VALUES 
		($parent_course, $parent_section, $id_resource, 'keywords', \"".$kw."\", $courseGrade)";
		//echo($sql);
		$DB->execute($sql);
		
	}
	$sql="INSERT INTO mdl_metadata(id_course, id_course_sections, id_resource, property, value, courseGrade) VALUES 
		($parent_course, $parent_section, $id_resource, 'keywords', \"".$nomeCategoria."\", $courseGrade)";
	//echo($sql);
    $DB->execute($sql);
	//--------------------------------------------------------------------------------------------------------------------


	//Keywords
	// doppio ciclo da togliere
	$lang = current_language();
	$index1=$data->Keywords;
	if($index1 != NULL) {
/* 		$pieces = explode(", ", $index1);
		for($i = 0; $i < count($pieces); $i++) {
            		$temp = $pieces[$i]; */
             		if(($pieces2 = explode (",", $index1)) != false) {
						$sql="INSERT INTO mdl_metadata(id_course, id_course_sections,id_resource, property, value, courseGrade, lang) VALUES ";
						$params = [];
                   		for($j = 0; $j < count($pieces2); $j++) {
                       			$lower = mb_strtolower($pieces2[$j]);
                       			$white_space = trim($lower);
                       			if(!strlen(trim($white_space)) == 0 ) {
									array_push($params, $white_space);
									// insert value + append
									if($j > 0)
										$sql = $sql.",";
									$sql = $sql."($parent_course, $parent_section,$id_resource, 'keywords', ?, $courseGrade, '".$lang."')";
                       			//$DB->execute($sql);
                       			}
								echo "<script>console.log('pieces 2 = ". $white_space ."');</script>";
                    		}
						$DB->execute($sql,$params);
             		}			
					else { 
                    		$lower = mb_strtolower($temp);
                    		$white_space = trim($lower);
                    		if(!strlen(trim($white_space)) ==0 ) {
                    			$sql="INSERT INTO mdl_metadata(id_course, id_course_sections,id_resource, property, value, courseGrade) VALUES ($parent_course, $parent_section,$id_resource, 'keywords', ?, $courseGrade)";
								$params = [$white_space];
								$DB->execute($sql, $params);
                    		}
            		}
		
	}



	//Format
	$index2 = $data->Format;
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'format'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}

    $sql="INSERT INTO mdl_metadata(id_course, id_course_sections, id_resource, property, value, courseGrade) VALUES ($parent_course, $parent_section, $id_resource, 'format', '".$choices[$index2]."', $courseGrade)";
    $DB->execute($sql);


	//LearningResourceType
	$index3 = $data->LearningResourceType;
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'resourcetype'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}

    $sql="INSERT INTO mdl_metadata(id_course, id_course_sections, id_resource, property, value, courseGrade) VALUES ($parent_course, $parent_section, $id_resource, 'resourcetype', '".$choices[$index3]."', $courseGrade)";
    $DB->execute($sql);


	//TypicalLearningTime
	$index4 = $data->TypicalLearningTime;
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'time'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}

    $sql="INSERT INTO mdl_metadata(id_course, id_course_sections, id_resource, property, value, courseGrade) VALUES ($parent_course, $parent_section, $id_resource, 'time', '".$choices[$index4]."', $courseGrade)";
    $DB->execute($sql);
	
	//s_t_mod Hui: estrarre le keywords dalle risorse
	extract_keywords($moduleID,$parent_course, $parent_section, $id_resource,$courseGrade,2);
	
    	/***********************************************/
    	/* 			FINE   		       */
    	/***********************************************/
?>