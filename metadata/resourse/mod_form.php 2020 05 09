<?php

	/******************************************************/
	/*	     Form per l'inserimento dei metadati	      */
	/******************************************************/

	$id = $this->current->coursemodule;

	//ViewPreviouslyInsertedMetadata
	$sql="SELECT count(*) FROM mdl_metadata WHERE id_resource = '".$id."'";
	$num_rows = $DB->count_records_sql($sql);

	if($num_rows > 0) {
		$mform->addElement('header','View Previously Inserted Metadata', convert_metadata('vpim'));

		$sql="SELECT id_metadata, property, value FROM mdl_metadata WHERE id_resource = '".$id."' AND id_resource IS NOT NULL AND id_course IS NOT NULL AND id_course_sections IS NOT NULL";
		$fields = $DB->get_records_sql($sql);
		$choices = array();
		$i = 0;
		foreach($fields as $field) {
			$choices[$i] = convert_metadata($field->property).": ".$field->value;
			$i++;
		}
		$select = $mform->addElement('select', 'CheckMetadata', convert_metadata('cm'), $choices, array('style'=>'min-height:130px;'));
		$select->setMultiple(true);
	}

	$mform->addElement('header','Metadata', convert_metadata('metadata'));

	$missing = array(
    	"missing1" => convert_metadata("missLang"),
		"missing2" => convert_metadata("missFormat"),
		"missing3" => convert_metadata("missLRT"),
		"missing4" => convert_metadata("missTLT")
	);

	//Language
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'language'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}
        $mform->addElement('select', 'Language', convert_metadata('language'), $choices);
	$mform->addRule('Language', $missing['missing1'], 'required', null, 'client'); 

	//Keywords
	$mform->addElement('text','Keywords', convert_metadata('ks'),'id="text-area" class="text_area" maxlength="254" size="50"');
	$mform->setType('Keywords', PARAM_NOTAGS); 
	


	//Format
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'format'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}
        $mform->addElement('select', 'Format', convert_metadata('format'), $choices);
	$mform->addRule('Format', $missing['missing2'], 'required', null, 'client');

	//LearningResourceType
	/*$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'resourcetype'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}
        $mform->addElement('select', 'LearningResourceType', convert_metadata('resourcetype'), $choices);
	$mform->addRule('LearningResourceType', $missing['missing3'], 'required', null, 'client');*/

	//LearningResourceType
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'resourcetype'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
        $choices[$i] = $field->value;
        $i++;
    }
    $mform->addElement('html', '<div id="fitem_id_LearningResourceType" class="fitem required fitem_select">
        <div class="fitemtitle">
            <label for="id_LearningResourceType">'.convert_metadata('resourcetype').'
                <font size = 2%>'.convert_metadata('(either contents or activities)').'</font></label><img class="req" title="Required field" alt="Required field" src="http://siren.laren.di.unimi.it/nett/mnett/theme/image.php/afterburner/core/1402079562/req">
	    </div>
        <div class="felement fselect" id="yui_3_9_1_2_1405520962176_1132">
             <select name="LearningResourceType" onblur="validate_mod_resource_mod_form_LearningResourceType(this)" onchange="validate_mod_resource_mod_form_LearningResourceType(this)" id="id_LearningResourceType">
                    <optgroup label="'.convert_metadata('Contents').'">
                        <option value="3">'.$choices[3].'</option>
                        <option value="4">'.$choices[4].'</option>
                        <option value="5">'.$choices[5].'</option>
                        <option value="6">'.$choices[6].'</option>
                        <option value="7">'.$choices[7].'</option>
					    <option value="8">'.$choices[8].'</option>
					    <option value="9">'.$choices[9].'</option>
                        <option value="14">'.$choices[14].'</option>

                    </optgroup>
                    <optgroup label="'.convert_metadata('Activities').'">
                        <option value="0">'.$choices[0].'</option>
					    <option value="1">'.$choices[1].'</option>
	                    <option value="2">'.$choices[2].'</option>
                        <option value="10">'.$choices[10].'</option>
					   <option value="11">'.$choices[11].'</option>
					   <option value="12">'.$choices[12].'</option>
					   <option value="13">'.$choices[13].'</option>
                    </optgroup>
            </select>
        </div></br></br></br>');
//$mform->addRule('LearningResourceType', $missing['missing3'], 'required', null, 'client');

	//TypicalLearningTime
	$sql="SELECT property_value AS value FROM mdl_metadata_descr WHERE property_name = 'time'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}

        $mform->addElement('select', 'TypicalLearningTime', convert_metadata('time'), $choices);
	$mform->addRule('TypicalLearningTime', $missing['missing4'], 'required', null, 'client');

	/******************************************************/
	/*		         	FINE	      	        	      */
	/******************************************************/

?>