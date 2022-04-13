<?php

	/******************************************************/
	/*      	Form per l'inserimento dei metadati	      */
	/******************************************************/
	
	require_once('C:/inetpub/wwwroot/lms/metadata/metadata_page/metadata_functions.php'); // s_t_mod Hui
	
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
	
	$old_metadata = array();

	if($num_rows > 0) {
		
		//$mform->addElement('header','View Previously Inserted Metadata', 'View Previously Inserted Metadata'); 
		$mform->addElement('header','View Previously Inserted Metadata', convert_metadata('prec_metadata')); //s_t_mod
		
		$sql="SELECT id_metadata, property, value, grade FROM mdl_metadata WHERE id_course IS NOT NULL AND id_course_sections = '".$_GET['id']."' AND id_resource IS NULL AND (property = 'keywords' OR property = 'difficulty' OR property = 'd_req_skill' OR property = 'd_acq_skill')";
		$fields = $DB->get_records_sql($sql);
		$choices = array();
		$i = 0;
		foreach($fields as $field) {
			$choices[$i] = convert_metadata($field->property).": ".$field->value;
			$old_metadata[$i] = "$".$field->property.":".$field->value.",".$field->grade."$";
			$i++;
		}
		
		
		//$select = $mform->addElement('select', 'CheckMetadata', 'Check Metadata: ', $choices, array('style'=>'min-height:130px;'));
		$select = $mform->addElement('select', 'CheckMetadata', convert_metadata('cm'), $choices, array('style'=>'min-height:130px;')); //s_t_mod
		
		$select->setMultiple(true);
	}

		//$mform->addElement('header','Metadata', 'Metadata');
		$mform->addElement('header','Metadata', convert_metadata('metadata')); //s_t_mod
	
	$missing = array(
    		"missing" => "Missing Difficulty",
	);
	
	//espressioni regolari
	function get_string_between($string, $start, $end){
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini == 0) return '';
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
	}
	
	
	//separo la proprietĂ  dal valore del metadato 
	$old_property = array();
	$old_value = array();
	$old_grade = array();
	$i=0;
	foreach($old_metadata as $old) {
		$old_property[$i] = get_string_between($old, '$', ':');
		$old_value[$i] = get_string_between($old, ':', ',');
		$old_grade[$i] = get_string_between($old, ',', '$');
		//echo($old_property[$i]);
		$i++;
	}

	//Keywords
	////////////////////////////////////////////////////////////////////////MODIFICA
	$default = "";
	$i = 0;
	foreach($old_property as $o){
		if($o == "keywords"){
			//echo($old_value[$i]);
			$default = $default.($old_value[$i]).", ";
		}
		$i++;
	}
	$default = substr($default, 0, -2);
	///////////////////////////////////////////////////////////////////////////////
	
	//$mform->addElement('text','Keywords', 'Keywords (separator ", ")','id="text-area" class="text_area" maxlength="254" size="50"');
	$mform->addElement('text','Keywords', convert_metadata('ks'),'id="text-area" class="text_area" maxlength="254" size="50"'); //s_t_mod
	$mform->addRule('Keywords', $missing['missing0'], 'required', null, 'client'); 
	$mform->setType('Keywords', PARAM_NOTAGS);  
	if ($default != "")
		$mform->setDefault('Keywords', $default);

	//Difficulty
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'difficulty'";
	$fields = $DB->get_records_sql($sql);

	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}

	////////////////////////////////////////////////////////////////////////MODIFICA
	$default = $old_value[array_search("difficulty",$old_property)];
	//echo($default);
	//default is the index key
	
	$default = array_search(translate_type($default), $choices);
	
	
	////////////////////////////////////////////////////////////////////////
	
	//$mform->addElement('select', 'Difficulty', 'Difficulty', $choices);
	$mform->addElement('select', 'Difficulty', convert_metadata('difficulty'), $choices); //s_t_mod
	$mform->addRule('Difficulty', $missing['missing'], 'required', null, 'client');
	$mform->setDefault('Difficulty', $default);

	// Derived Required/Acquired Skills
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
	$type = array('Required/Acquired Skill', 'Required Skill', 'Acquired Skill');
	$scale = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10');

	//$mform->addElement('header','Required Skills Metadata', 'Background: Metadata');
	$mform->addElement('header','Required Skills Metadata', convert_metadata('req_skills')); //s_t_mod

	$i = 0;
	$j = 0;

	foreach($fields as $field) {
		$j = 0;
		$choices[$i] = $field->value;

		$elementgroup1 = array();
		$elementgroup1[] = $mform->createElement('advcheckbox', 'checkbox1_'.$i, '', '', array('group' => 1), array(0, 1));
		$elementgroup1[] = $mform->createElement('select', 'scale1_'.$i, 'scale1_'.$i, $scale);
		//$mform->addGroup($elementgroup1, $choices[$i], $choices[$i], array(' Coverage level: '), false);
		$mform->addGroup($elementgroup1, $choices[$i], $choices[$i], array(convert_metadata('grade')), false); //s_t_mod
		
		
		foreach($old_property as $o){
			if($o == "d_req_skill"){
				if($old_value[$j] == $choices[$i]){
					$default = $old_grade[$j]-1;
					$mform->setDefault('checkbox1_'.$i, 1);
					$mform->setDefault('scale1_'.$i, $default);	 
				}
			}
			$j++;
		}
		$i++;	 
	}
	//$mform->addElement('header','Acquired Skills Metadata', 'Acquired Skills Metadata');
	$mform->addElement('header','Acquired Skills Metadata', convert_metadata('acq_skills')); //s_t_mod
	
	$i = 0;

	foreach($fields as $field) {
		$j = 0;
		$choices[$i] = $field->value;

		$elementgroup2 = array();
		$elementgroup2[] = $mform->createElement('advcheckbox', 'checkbox2_'.$i, '', '', array('group' => 1), array(0, 1));
		$elementgroup2[] = $mform->createElement('select', 'scale2_'.$i, 'scale2_'.$i, $scale);
		//$mform->addGroup($elementgroup2, $choices[$i], $choices[$i], array(' Coverage level: '), false);
		$mform->addGroup($elementgroup2, $choices[$i], translate_element($choices[$i]), array(convert_metadata('grade')), false); //s_t_mod

		foreach($old_property as $o){
			if($o == "d_acq_skill"){
				if($old_value[$j] == $choices[$i]){
					$default = $old_grade[$j]-1;
					$mform->setDefault('checkbox2_'.$i, 1);
					$mform->setDefault('scale2_'.$i, $default);	 
				}
			}
			$j++;
		}
		
		$i++;	
	}

    /******************************************************/
	/*		         	FINE             			      */
	/******************************************************/

?>