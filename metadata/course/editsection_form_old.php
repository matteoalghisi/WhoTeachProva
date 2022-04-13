<?php

	/******************************************************/
	/*      	Form per l'inserimento dei metadati	      */
	/******************************************************/

	$id_course = $course->id;
	$id_category = $course->category;
	$sql = "SELECT name FROM mdl_course_categories WHERE  id = '".$id_category."'";
	$fields = $DB->get_records_sql($sql);
	foreach($fields as $field) {
			$cat = $field->name;
	}

	//ViewPreviouslyInsertedMetadata
	$sql="SELECT count(*) FROM mdl_metadata WHERE id_course_sections = '".$_GET['id']."'";
	$num_rows = $DB->count_records_sql($sql);

	if($num_rows > 0) {
		
		$mform->addElement('header','View Previously Inserted Metadata', 'View Previously Inserted Metadata');
		
		$sql="SELECT id_metadata, property, value FROM mdl_metadata WHERE id_course IS NOT NULL AND id_course_sections = '".$_GET['id']."' AND id_resource IS NULL AND (property = 'keywords' OR property = 'difficulty' OR property = 'd_req_skill' OR property = 'd_acq_skill')";
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

		$mform->addElement('header','Metadata', 'Metadata');
	
	$missing = array(
    		"missing" => "Missing Difficulty",
	);

	//Keywords
	$mform->addElement('text','Keywords', 'Keywords (separator ", ")','id="text-area" class="text_area" maxlength="254" size="50"');
	$mform->setType('Keywords', PARAM_NOTAGS);  

	//Difficulty
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'difficulty'";
	$fields = $DB->get_records_sql($sql);

	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}

	$mform->addElement('select', 'Difficulty', 'Difficulty', $choices);
	$mform->addRule('Difficulty', $missing['missing'], 'required', null, 'client');

	// Derived Required/Acquired Skills
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 's_req_skill' AND category = '".$cat."'";
	$fields = $DB->get_records_sql($sql);

	$choices = array();
	$type = array('Required/Acquired Skill', 'Required Skill', 'Acquired Skill');
	$scale = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10');

	$mform->addElement('header','Required Skills Metadata', 'Background: Metadata');

	$i = 0;

	foreach($fields as $field) {
		$choices[$i] = $field->value;

		$elementgroup1 = array();
		$elementgroup1[] = $mform->createElement('advcheckbox', 'checkbox1_'.$i, '', '', array('group' => 1), array(0, 1));
		$elementgroup1[] = $mform->createElement('select', 'scale1_'.$i, 'scale1_'.$i, $scale);
		$mform->addGroup($elementgroup1, $choices[$i], $choices[$i], array(' Coverage level: '), false);

		$i++;	
	}
	$mform->addElement('header','Acquired Skills Metadata', 'Acquired Skills Metadata');
	
	$i = 0;

	foreach($fields as $field) {
		$choices[$i] = $field->value;

		$elementgroup2 = array();
		$elementgroup2[] = $mform->createElement('advcheckbox', 'checkbox2_'.$i, '', '', array('group' => 1), array(0, 1));
		$elementgroup2[] = $mform->createElement('select', 'scale2_'.$i, 'scale2_'.$i, $scale);
		$mform->addGroup($elementgroup2, $choices[$i], $choices[$i], array(' Coverage level: '), false);

		$i++;	
	}

    /******************************************************/
	/*		         	FINE             			      */
	/******************************************************/

?>