<?php

	/******************************************************/
	/*     	Form per l'inserimento dei metadati	          */
	/******************************************************/

	//s_t_mod Hui: acquisisco il nome della categoria dal db usando il suo id, altrimenti causa problemi quando seleziono lingua inglese
	//$cat = $displaylist[$category->id]; 
	$course_id = $course->id;
	
	$sql="SELECT name FROM mdl_course_categories WHERE id = '".$category->id."'";
	$fields = $DB->get_records_sql($sql);
	foreach($fields as $field) {
			$cat=$field->name;
	}
	//----------------------------------------------------------------------------------
	
	//ViewPreviouslyInsertedMetadata
		$sql="SELECT count(*) FROM mdl_metadata WHERE Id_course = '".$course_id."'";
		$num_rows = $DB->count_records_sql($sql);
		
		$old_metadata = array();
	  
		if($num_rows > 0) {
			//$mform->addElement('header','View Previously Inserted Metadata', 'View Previously Inserted Metadata');
			$mform->addElement('header','View Previously Inserted Metadata', convert_metadata('prec_metadata')); //s_t_mod

			$sql="SELECT id_metadata, property, value, grade FROM mdl_metadata WHERE id_course = '".$course_id."' AND 
				(property = 'keywords' OR property = 'min_age' OR property = 'max_age' OR property = 'category' OR property = 's_req_skill' OR property = 's_acq_skill')  AND 
				id_course_sections IS NULL AND id_resource IS NULL";
			$fields = $DB->get_records_sql($sql);
			$choices = array();
			$i = 0;
			foreach($fields as $field) {
				$choices[$i] = convert_metadata($field->property).": ".$field->value;
				$old_metadata[$i] = "$".$field->property.":".$field->value.",".$field->grade."$";
				$i++;
			}
			//$select = $mform->addElement('select', 'CheckMetadata', 'Check Metadata:', $choices, array('style'=>'min-height:130px;'));
			$select = $mform->addElement('select', 'CheckMetadata', convert_metadata('cm'), $choices, array('style'=>'min-height:130px;')); //s_t_mod
			$select->setMultiple(true);
		}
    
	//$mform->addElement('header','Basic Metadata', 'Basic Metadata');	
	$mform->addElement('header','Basic Metadata', convert_metadata('basic_metadata')); //s_t_mod
 	
	$missing = array(
		"missing1" => "The Minimal Age must be smaller than the Maximal Age",	//s_t_mod Hui
		"missing2" => "Missing Category",
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
	$i=0;
	foreach($old_metadata as $old) {
		$old_property[$i] = get_string_between($old, '$', ':');
		$old_value[$i] = get_string_between($old, ':', ',');
		$old_grade[$i] = get_string_between($old, ',', '$');
		//echo($old_property[$i]);
		$i++;
	}

	//Keywords	
	
	$default = "";
	$i = 0;
	//print_object($old_property);
	foreach($old_property as $o){
		if($o == "keywords"){
			//echo($old_value[$i]);
			$default = $default.($old_value[$i]).", ";
		}
		$i++;
	}
	$default = substr($default, 0, -2);
	
	//$mform->addElement('text','Keywords', 'Keywords (separator ", ")','maxlength="254" size="50"');
	$mform->addElement('text','Keywords', convert_metadata('ks'),'maxlength="254" size="50"'); //s_t_mod
	//$mform->addElement('text','Keywords', convert_metadata('ks'),'id="text-area" class="text_area" maxlength="254" size="50"');
 	$mform->setType('Keywords', PARAM_NOTAGS); 
	if ($default != "")
		$mform->setDefault('Keywords', $default);

	$mform->addHelpButton('Keywords', 'Keywords'); //s_t_mod Hui
	
	//Minimal Age
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'min_age'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}
	////////////////////////////////////////////////////////////////////////MODIFICA
	$default = $old_value[array_search("min_age",$old_property)];
	//echo($default);
	//default is the index key
	$default = array_search(translate_type($default), $choices);
	////////////////////////////////////////////////////////////////////////
        //$mform->addElement('select', 'MinimalAge', 'Minimal Age', $choices);
		$mform->addElement('select', 'MinimalAge', convert_metadata('min_age'), $choices); //s_t_mod
		$mform->setDefault('MinimalAge', $default);
		
		$mform->addRule('MinimalAge', get_string('required'), 'required', null, 'client'); //s_t_mod Hui

	//Maximal Age
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'max_age'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}
	
	////////////////////////////////////////////////////////////////////////MODIFICA
	$default = $old_value[array_search("max_age",$old_property)];
	//echo($default);
 	//print_object($old_property);
	//print_object($old_value);
	//print_object($old_grade); 
	//default is the index key
	//$default = array_search(translate_type($default), $choices);
	$default = array_search(translate_type($default), $choices); //_s_t_mod C
	////////////////////////////////////////////////////////////////////////
    
	//$mform->addElement('select', 'MaximalAge', 'Maximal Age', $choices);
	$mform->addElement('select', 'MaximalAge', convert_metadata('max_age'), $choices); //s_t_mod
	$mform->setDefault('MaximalAge', $default);
	
	$mform->addRule('MaximalAge', get_string('required'), 'required', null, 'client'); //s_t_mod Hui
	
	//Minimal Age < Maximal Age!
	//$mform->addRule(array('MinimalAge','MaximalAge'), $missing['missing1'],'compare','<');
	$mform->addRule(array('MinimalAge','MaximalAge'), convert_metadata('age_rule'),'compare','<');	//s_t_mod Hui

	//s_t_mod
	//Category

	$sql="SELECT id, property_value AS value FROM mdl_metadata_descr WHERE property_value = '".$cat."'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}
	
	//s_t_mod Hui
    //$mform->addElement('label', 'Category', 'Category', $choices, 'style="visibility:hidden;"');
	//$mform->addElement('hidden', 'Category', $field->value);
	//$mform->setType('Category', PARAM_TEXT);
	//$mform->setDefault('Category', $field->value);
	//$mform->addRule('Category', $missing['missing2'], 'required', null, 'client');

	
	//Specified Required/Acquired Skills
 	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 's_req_skill' AND category = '".$cat."'";
 	$fields = $DB->get_records_sql($sql);

 	$choices = array();
 	$type = array('Required/Acquired Skill', 'Required Skill', 'Acquired Skill');
 	$scale = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10');
 	
	//$mform->addElement('header','Required Skills Metadata', 'Required Skills Metadata');
	$mform->addElement('header','Required Skills Metadata', convert_metadata('req_skills')); //s_t_mod

 	$i = 0;
 	foreach($fields as $field) {
		$j = 0;
 		$choices[$i] = $field->value;
		
 		$elementgroup1 = array();
 		$elementgroup1[] = $mform->createElement('advcheckbox', 'checkbox1_'.$i, '', '', array('group' => 1), array(0, 1));
 		$elementgroup1[] = $mform->createElement('select', 'scale1_'.$i, 'scale1_'.$i, $scale);
 		//$mform->addGroup($elementgroup1, $choices[$i], translate_element($choices[$i]), array(' Grade: '), false);
		$mform->addGroup($elementgroup1, $choices[$i], translate_element($choices[$i]), array(convert_metadata('grade')), false); //s_t_mod
		
		foreach($old_property as $o){
			if($o == "s_req_skill"){
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
		//$mform->addGroup($elementgroup2, $choices[$i], $choices[$i], array(' Grade: '), false);
 		$mform->addGroup($elementgroup2, $choices[$i], translate_element($choices[$i]), array(convert_metadata('grade')), false); //_s_t_mod

		foreach($old_property as $o){
			if($o == "s_acq_skill"){
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

	//questo servirebbe per permettrere il cambiamento della categoria mentre si crea il corso,
	//ma ci sono problemi con le skills, quindi è stato disabilitato
	// echo("<script>
			// window.addEventListener('load', function() {
				// var id_cat = document.getElementById('id_category');
				// $(id_cat).change(function(){
					// document.getElementsByName('Category')[0].value = id_cat.options[id_cat.selectedIndex].text;
				// });
			// }
		// </script>");

    /******************************************************/
	/*			         FINE	            		      */
	/******************************************************/

?>	
  