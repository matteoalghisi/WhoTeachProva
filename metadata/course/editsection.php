<?php

/************************************************/
	/* Inserimento nel DB di moodle	dei metadati	*/
	/************************************************/
	
	require_once('C:/inetpub/wwwroot/lms/metadata/metadata_page/metadata_functions.php'); // s_t_mod Hui
	
	$data = $mform->get_data();
	
	//recupero dell'id della categoria
	$id_category = $course->category;
	$sql = "SELECT name FROM mdl_course_categories WHERE  id = '".$id_category."'";
	$fields = $DB->get_records_sql($sql);
	foreach($fields as $field) {
			$cat = $field->name;
	}

	//recupero dell'id del corso
	$id_course = $course->id;
	
	//recupero valutazione del corso
	$sql = "SELECT ROUND(AVG(mdl_block_rate_course.rating*2)) as grade
			FROM mdl_block_rate_course
			WHERE mdl_block_rate_course.course = ".$id_course."
			";
	if($DB->get_record_sql($sql)->grade != null)
		$courseGrade = $DB->get_record_sql($sql)->grade;
	else 
		$courseGrade = 0;
	//voto corso recuperato...

	//eliminazione dei vecchi metadati 
	$sql="DELETE FROM mdl_metadata WHERE id_course = '".$id_course."' AND id_course_sections = '".$id."' AND id_resource IS NULL";
	$DB->execute($sql);

	//Category (Propagation from course)
	$sql="INSERT INTO mdl_metadata(id_course, id_course_sections, property, value, courseGrade) VALUES ($id_course, $id, 'category', \"".$cat."\", $courseGrade)";
	$DB->execute($sql);


	//Minimal Age (Propagation from course)
	$sql="SELECT value FROM mdl_metadata WHERE property = 'min_age' AND id_course = '".$id_course."'";
	$fields = $DB->get_records_sql($sql);
	foreach($fields as $field) {
			$value = $field->value;
	}
	$sql="INSERT INTO mdl_metadata(id_course, id_course_sections, property, value, courseGrade) VALUES ($id_course, $id, 'min_age', '".$value."', $courseGrade)";
	$DB->execute($sql);

	//Maximal Age (Propagation from course)
	$sql="SELECT value FROM mdl_metadata WHERE property = 'max_age' AND id_course = '".$id_course."'";
	$fields = $DB->get_records_sql($sql);
	foreach($fields as $field) {
			$value = $field->value;
	}
	$sql="INSERT INTO mdl_metadata(id_course, id_course_sections, property, value, courseGrade) VALUES ($id_course, $id, 'max_age', '".$value."', $courseGrade)";
	$DB->execute($sql);


	//Age (Propagation from course) in seguito....
	/*
	$sql="SELECT value FROM mdl_metadata WHERE property = 'age' AND id_course = '".$id_course."'";
	$fields = $DB->get_records_sql($sql);
	foreach($fields as $field) {
			$value = $field->value;
	}
	$sql="INSERT INTO mdl_metadata(id_course, id_course_sections, property, value, courseGrade) VALUES ($id_course, $id, 'age', '".$value."', $courseGrade)";
	$DB->execute($sql);
	*/
	
	
/*
        //Keywords
	$index0=$data->Keywords;
	if($index0 != NULL) {
		$pieces = explode(", ", $index0);
		for($i = 0; $i < count($pieces); $i++) {	
			$sql="INSERT INTO mdl_metadata(id_course, id_course_sections, property, value) VALUES ($id_course, $id, 'keywords', '".$pieces[$i]."')";
			$DB->execute($sql);
		}
	}
*/
	//Keywords
	$lang = current_language();
    	$index0=$data->Keywords;
		if($index0 != NULL) {
        		$pieces = explode(", ", $index0);
        		for($i = 0; $i < count($pieces); $i++) {
            			$temp = $pieces[$i];
             			if (($pieces2 = explode (",", $temp)) != false) {
                   			for($j = 0; $j < count($pieces2); $j++) {
                       			$lower = mb_strtolower($pieces2[$j]);
                       			$white_space = trim($lower);
                    			if(!strlen(trim($white_space)) ==0 ) {                       	
                       				$sql="INSERT INTO mdl_metadata(id_course, id_course_sections, property, value, courseGrade, lang) VALUES ($id_course, $id, 'keywords', ?, $courseGrade, '".$lang."')";
									$params = [$white_space];
									$DB->execute($sql, $params);
                       			}
                    			}
             			}
				else {
                    			$lower = mb_strtolower($temp);
                    			$white_space = trim($lower);
                    			if(!strlen(trim($white_space)) ==0 ) {                       	
									$sql="INSERT INTO mdl_metadata(id_course, id_course_sections, property, value, courseGrade) VALUES ($id_course, $id, 'keywords', ?, $courseGrade)";
									$params = [$white_space];
									$DB->execute($sql, $params);
                    			}
            			}
			}
		}

	//Difficulty
	$index1=$data->Difficulty;
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'difficulty'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}
	for($i = 0; $i < count($index1); $i++) {
		$value = $index1[$i];
		$sql="INSERT INTO mdl_metadata(id_course, id_course_sections, property, value, courseGrade) VALUES ($id_course, $id, 'difficulty', '".$choices[$value]."', $courseGrade)";
		$DB->execute($sql);
	}

	//Derived Required/Acquired Skills
	//$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 's_req_skill' AND category = '".$cat."'";
	
	//s_t_mod Hui: controlla se si tratta di una sottocategoria e preleva le skills associate
	
	if(is_subcategory($id_category))
		$categoryName=get_parent_name($id_category);
	else
		$categoryName=$cat;
	
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 's_req_skill' AND category = '".$categoryName."'";
	
	//--------------------------------------------------------
	
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}
	//numero di skills
	$i--;

	//Derived Required Skills
	for ($j = 0; $j <= $i; $j++) {
		//preleva l'i-esima checkbox
		$checkbox = 'checkbox1_'.$j;
		$current_checkbox = $data->$checkbox;

		if($current_checkbox == 1) {
			//preleva il grado
			$scale = 'scale1_'.$j;
			$current_scale = $data->$scale;
			$current_scale++;

			$sql="INSERT INTO mdl_metadata(id_course, id_course_sections, property, value, grade, courseGrade) VALUES ($id_course, $id, 'd_req_skill', \"".$choices[$j]."\", '".$current_scale."', $courseGrade)";
			$DB->execute($sql);
		}
	}

	//SDerived Acquired Skills
	for ($j = 0; $j <= $i; $j++) {
		//preleva l'i-esima checkbox
		$checkbox = 'checkbox2_'.$j;
		$current_checkbox = $data->$checkbox;

		if($current_checkbox == 1) {
			//preleva il grado
			$scale = 'scale2_'.$j;
			$current_scale = $data->$scale;
			$current_scale++;

			$sql="INSERT INTO mdl_metadata(id_course, id_course_sections, property, value, grade, courseGrade) VALUES ($id_course, $id, 'd_acq_skill', \"".$choices[$j]."\", '".$current_scale."', $courseGrade)";
			$DB->execute($sql);
		}
	}

	/*******************************************************************/
	/* 	INSERIMENTO DELLE INFORMAZIONI IN sssecm_developedBY         */
	/*******************************************************************/

	$user_id = $USER->id;
	$date = date('Y-m-d');

	$sql = "DELETE FROM sssecm_developedby WHERE user_id = '".$user_id."' AND module_id = '".$id."'";
	$DB->execute($sql);

	$sql = "INSERT INTO sssecm_developedby(user_id, module_id, date) VALUES('".$user_id."', '".$id."', '".$date."')";
	$DB->execute($sql);

	/*************rimozione tupla tabella mdl_duplicates****************/
	/* Vista la modifica effettuata, tolgo la sezione da mdl_duplicates*/
	/*******************************************************************/
	 
	$sql = "UPDATE sssecm_duplicates SET flag = 1 WHERE id_sec_dest = '".$id."'";
	$DB->execute($sql);
	
	/*************Invio della sezione alla tabella sssecm_review***********/
	/* Vista la modifica effettuata, tolgo la sezione da mdl_duplicates*/
	/*******************************************************************/

	$sql="select count(*) from sssecm_review_master WHERE id_course_sections = '".$id."'";
	$num_rows = $DB->count_records_sql($sql);
	
	$sql = "SELECT mdl_course_sections.visible FROM mdl_course_sections WHERE id=" . $id;
	$results = $DB->get_records_sql($sql);
	foreach($results as $result) {
		$visible = $result->visible;
	}
	//inserimento del nuovo modulo in sssecm_review_master e del colore in mdl_metadata
	if($num_rows == 0 && $visible == 1){
		$sql="INSERT INTO sssecm_review_master(id_course_sections, submissionDate) VALUES('".$id."', CURDATE())";
		$DB->execute($sql);
		
		$sql = "INSERT INTO mdl_metadata(Id_course_sections, Id_course, Property, Value, courseGrade) VALUES('".$id."', $id_course, 'status', 'yellow', $courseGrade)";
		$DB->execute($sql);
	}
	//modifica del colore in mdl_metadata e reset in sssecm_review_master e sssecm_review
	elseif($num_rows > 0 && $visible == 1){
		$sql = "SELECT count(*) FROM mdl_metadata WHERE Id_course_sections=" . $id . " AND Property='status'";
		$num_rev = $DB->count_records_sql($sql);
		if(num_rev == 0){
			$sql = "INSERT INTO mdl_metadata(Id_course_sections, Id_course, Property, Value, courseGrade) VALUES('".$id."', $id_course, 'status', 'yellow', $courseGrade)";
		}
		elseif(num_rev > 0){
			$sql = "UPDATE mdl_metadata SET Value='yellow' WHERE Id_course_sections=" . $id . " AND Property='status'";
		}
		$DB->execute($sql);
		
		$sql = "UPDATE sssecm_review_master SET submissionDate=CURDATE(), completitionDate=null, comments=null, review_status=0 WHERE id_course_sections=" . $id;
		$DB->execute($sql);
		
		$sql = "SELECT id FROM sssecm_review_master WHERE id_course_sections=" . $id;
		$results = $DB->get_records_sql($sql);
		foreach($results as $result) {
			$id_review = $result->id;
		}
		$sql = "UPDATE sssecm_review SET completitionDate=null, comments=null, review_status=0 WHERE id_review_master=" . $id_review;
		$DB->execute($sql);
	}
	//inserimento del colore bianco per il modulo privato
	elseif ($visible == 0) {
		$sql = "INSERT INTO mdl_metadata(Id_course_sections, Id_course, Property, Value, courseGrade) VALUES('".$id."', $id_course, 'status', 'white', $courseGrade)";
		$DB->execute($sql);
	}

	/************************************************/
	/* 		      FINE		        */
	/************************************************/
?>