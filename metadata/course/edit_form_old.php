<?php

	/******************************************************/
	/*     	Form per l'inserimento dei metadati	          */
	/******************************************************/

	$cat = $displaylist[$category->id];
	$course_id = $course->id;
	
	
	
	//ViewPreviouslyInsertedMetadata
		$sql="SELECT count(*) FROM mdl_metadata WHERE Id_course = '".$course_id."'";
		$num_rows = $DB->count_records_sql($sql);
	  
		if($num_rows > 0) {
			$mform->addElement('header','View Previously Inserted Metadata', 'View Previously Inserted Metadata');

			$sql="SELECT id_metadata, property, value FROM mdl_metadata WHERE id_course = '".$course_id."' AND (property = 'keywords' OR property = 'min_age' OR property = 'max_age' OR property = 'category' OR property = 's_req_skill' OR property = 's_acq_skill')";
			$fields = $DB->get_records_sql($sql);
			$choices = array();
			$i = 0;
			foreach($fields as $field) {
				$choices[$i] = convert_metadata($field->property).": ".$field->value;
				$i++;
			}
			$select = $mform->addElement('select', 'CheckMetadata', 'Check Metadata: ', $choices, array('style'=>'min-height:130px;'));
			$select->setMultiple(true);
		}
            
	
	$mform->addElement('header','Basic Metadata', 'Basic Metadata');
 	
	$missing = array(
		"missing1" => "Minimal Age is not less than Maximal Age",
		"missing2" => "Missing Category",
	);

	//Keywords	
	$mform->addElement('text','Keywords', 'Keywords (separator ", ")','maxlength="254" size="50"');
 	$mform->setType('Keywords', PARAM_NOTAGS); 


	//Minimal Age
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'min_age'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}
        $mform->addElement('select', 'MinimalAge', 'Minimal Age', $choices);

	//Maximal Age
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'max_age'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}
    
	$mform->addElement('select', 'MaximalAge', 'Maximal Age', $choices);


	//Minimal Age < Maximal Age!
	$mform->addRule(array('MinimalAge','MaximalAge'), $missing['missing1'],'compare','<');

	//Category
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_value = '".$cat."'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}
	
    $mform->addElement('select', 'Category', 'Category', $choices);
	$mform->addRule('Category', $missing['missing2'], 'required', null, 'client');

	
	//Specified Required/Acquired Skills
 	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 's_req_skill' AND category = '".$cat."'";
 	$fields = $DB->get_records_sql($sql);

 	$choices = array();
 	$type = array('Required/Acquired Skill', 'Required Skill', 'Acquired Skill');
 	$scale = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10');
 	
	$mform->addElement('header','Required Skills Metadata', 'Required Skills Metadata');

 	$i = 0;
 	foreach($fields as $field) {
 		$choices[$i] = $field->value;

 		$elementgroup1 = array();
 		$elementgroup1[] = $mform->createElement('advcheckbox', 'checkbox1_'.$i, '', '', array('group' => 1), array(0, 1));
 		$elementgroup1[] = $mform->createElement('select', 'scale1_'.$i, 'scale1_'.$i, $scale);
 		$mform->addGroup($elementgroup1, $choices[$i], $choices[$i], array(' Grade: '), false);

 		$i++;	
 	}

 	$mform->addElement('header','Acquired Skills Metadata', 'Acquired Skills Metadata');

 	$i = 0;
 	foreach($fields as $field) {
 		$choices[$i] = $field->value;

 		$elementgroup2 = array();
 		$elementgroup2[] = $mform->createElement('advcheckbox', 'checkbox2_'.$i, '', '', array('group' => 1), array(0, 1));
 		$elementgroup2[] = $mform->createElement('select', 'scale2_'.$i, 'scale2_'.$i, $scale);
 		$mform->addGroup($elementgroup2, $choices[$i], $choices[$i], array(' Grade: '), false);

 		$i++;	
 	}


    /******************************************************/
	/*			         FINE	            		      */
	/******************************************************/

?>	
  