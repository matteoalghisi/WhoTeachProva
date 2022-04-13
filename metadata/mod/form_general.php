<?php
/******************************************************/
	/*	Form per l'inserimento dei metadati	      */
	/******************************************************/
	global $DB;	//s_t_mod Hui
	
	$id = $this->current->coursemodule;

	//ViewPreviouslyInsertedMetadata
	$sql="SELECT count(*) FROM mdl_metadata WHERE id_resource = '".$id."'";
	$num_rows = $DB->count_records_sql($sql);

	$old_metadata = array();

	if($num_rows > 0) {
		$mform->addElement('header','View Previously Inserted Metadata', convert_metadata('vpim'));

		$sql="SELECT id_metadata, property, value FROM mdl_metadata WHERE id_resource = '".$id."' AND id_resource IS NOT NULL AND id_course IS NOT NULL AND id_course_sections IS NOT NULL";
		$fields = $DB->get_records_sql($sql);
		$choices = array();
		$i = 0;
		foreach($fields as $field) {
			$choices[$i] = convert_metadata($field->property).": ".translate_language(translate_format(translate_type(translate_time($field->value))));
			//aggiunto old_metadata per vedere metadati vecchi
			$old_metadata[$i] = "$".$field->property.":".$field->value."$";
			$i++;
		}
	
		$select = $mform->addElement('select', 'CheckMetadata', convert_metadata('cm'), $choices, array('style'=>'min-height:130px;'));
		$select->setMultiple(true);
	}
	
	$mform->addElement('header','Metadata', convert_metadata('metadata'));

	$missing = array(
		"missing0" => convert_metadata("missKeyword"),
    	"missing1" => convert_metadata("missLang"),
		"missing2" => convert_metadata("missFormat"),
		"missing3" => convert_metadata("missLRT"),
		"missing4" => convert_metadata("missTLT")
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
		$old_value[$i] = get_string_between($old, ':', '$');
		//echo($old_property[$i]);
		$i++;
	}
	
	// sopra ho definito old_property e old_value
	// old_property = array contenente proprieta`/nome dei metadati (es lenguage,keyword,resourcetype, ecc.)
	// old_value = array contente valore per il metadato (es italiano, inglese, lettura, pdf, ecc...) 
	// NOTA: al indice di old_property corrisponde lo stesso indice di old_value per il corrispondente metadato
	
	

	//Language
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'language'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = translate_language($field->value);
		$i++;
	}
	
	////////////////////////////////////////////////////////////////////////MODIFICA
	$default = $old_value[array_search("language",$old_property)];
	//default is the index key
	$default = array_search(translate_language($default), $choices);
	////////////////////////////////////////////////////////////////////////
	
    $mform->addElement('select', 'Language', convert_metadata('language'), $choices);
	$mform->addRule('Language', $missing['missing1'], 'required', null, 'client'); 
	$mform->setDefault('Language', $default);

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
	if ($default != "")
		$default = substr($default, 0, -2);
	///////////////////////////////////////////////////////////////////////////////
	
	$mform->addElement('text','Keywords', convert_metadata('ks'),'id="text-area" class="text_area" maxlength="254" size="50"');
	$mform->addRule('Keywords', $missing['missing0'], 'required', null, 'client'); 
	$mform->setType('Keywords', PARAM_NOTAGS); 
	$mform->setDefault('Keywords', $default);
	
	
	

	//Format
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'format'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = translate_format($field->value);
		$i++;
	}
	
	////////////////////////////////////////////////////////////////////////MODIFICA
	$default = $old_value[array_search("format",$old_property)];
	//echo($default);
	//default is the index key
	$default = array_search(translate_format($default), $choices);
	////////////////////////////////////////////////////////////////////////
	
    $mform->addElement('select', 'Format', convert_metadata('format'), $choices);
	$mform->addRule('Format', $missing['missing2'], 'required', null, 'client');
	$mform->setDefault('Format', $default);



	//LearningResourceType   // the name in DB is resourcetype
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'resourcetype'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = translate_type($field->value);
		$i++;
	}
	
	////////////////////////////////////////////////////////////////////////MODIFICA
	$default = $old_value[array_search("resourcetype",$old_property)];
	//echo($default);
	//default is the index key
	$default = array_search(translate_type($default), $choices);
	////////////////////////////////////////////////////////////////////////
	
    $mform->addElement('select', 'LearningResourceType', convert_metadata('resourcetype'), $choices);
	$mform->addRule('LearningResourceType', $missing['missing3'], 'required', null, 'client');
	$mform->setDefault('LearningResourceType', $default);
	

	//TypicalLearningTime
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'time'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = translate_time($field->value);
		$i++;
	}
	
	////////////////////////////////////////////////////////////////////////MODIFICA
	$default = $old_value[array_search("time",$old_property)];
	//echo($default);
	//default is the index key
	$default = array_search(translate_time($default), $choices);
	////////////////////////////////////////////////////////////////////////
	
    $mform->addElement('select', 'TypicalLearningTime', convert_metadata('time'), $choices);
	$mform->addRule('TypicalLearningTime', $missing['missing4'], 'required', null, 'client');
	$mform->setDefault('TypicalLearningTime', $default);

	/******************************************************/
	/*			FINE	      		      */
	/******************************************************/
	?>
