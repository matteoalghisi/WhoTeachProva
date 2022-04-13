<?PHP /***********************************************/
	/* Inserimento nel DB di moodle	dei metadati   */
	/***********************************************/
	
	//MODIFICA DEI METADATI ASSOCIATI AL CORSO
	
	require_once('C:/inetpub/wwwroot/lms/metadata/metadata_page/metadata_functions.php'); // s_t_mod Hui
	
	$data = $editform->get_data();

	//recupero dell'id della categoria padre
	//$id_category = $course->category;
	$id_category = $data->category;//s_t_mod Hui
	
	$sql = "SELECT id, name FROM mdl_course_categories WHERE  id = '".$id_category."'";
	$fields = $DB->get_records_sql($sql);
	foreach($fields as $field) {
			$cat = $field->name;
	}

	//recupero dell'id del corso
	$id = $course->id;

	//recupero valutazione del corso
	$sql = "SELECT ROUND(AVG(mdl_block_rate_course.rating*2)) as grade
			FROM mdl_block_rate_course
			WHERE mdl_block_rate_course.course = ".$id."
			";
	if($DB->get_record_sql($sql)->grade != null)
		$courseGrade = $DB->get_record_sql($sql)->grade;
	else 
		$courseGrade = 0;
	//voto corso recuperato...

	//eliminazione dei vecchi metadati 
	$sql="DELETE FROM mdl_metadata WHERE id_course = '".$id."' AND id_course_sections IS NULL AND id_resource IS NULL";
	$DB->execute($sql);
	

	/*Keywords
	$index0=$data->Keywords;
	if($index0 != NULL) {
		$pieces = explode(", ", $index0);
		for($i = 0; $i < count($pieces); $i++) {
			$sql="INSERT INTO mdl_metadata(id_course, property, value) VALUES ($id, 'keywords', '".$pieces[$i]."')";
			$DB->execute($sql);
		}
	}*/

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
                       				$sql="INSERT INTO mdl_metadata(id_course, property, value, courseGrade, lang) VALUES ($id, 'keywords', ?, ".$courseGrade.", '".$lang."')";
									$params = [$white_space];
									$DB->execute($sql, $params);
                       			}
                    		}
             		}
			else { 
                    		$lower = mb_strtolower($temp);
                    		$white_space = trim($lower);
                    		if(!strlen(trim($white_space)) ==0 ) {
                    			$sql="INSERT INTO mdl_metadata(id_course, property, value, courseGrade) VALUES ($id, 'keywords', ?, ".$courseGrade.")";
								$params = [$white_space];
								$DB->execute($sql, $params);
                    		}
            		}
		}
	}

	//Minimal Age
	$index1 = $data->MinimalAge;
	$sql="SELECT id, property_value AS value FROM mdl_metadata_descr WHERE property_name = 'min_age'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}
	
	$sql="INSERT INTO mdl_metadata(id_course, property, value, courseGrade) VALUES ($id, 'min_age', '".$choices[$index1]."', $courseGrade)";
	$DB->execute($sql);
	$sql="UPDATE mdl_metadata SET value='".$choices[$index1]."' WHERE property='min_age' AND id_course='".$id."' AND id_course_sections IS NOT NULL";
	$DB->execute($sql);

	//Maximal Age
	$index2 = $data->MaximalAge;
	$sql="SELECT id, property_value AS value FROM mdl_metadata_descr WHERE property_name = 'max_age'";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}
	
	$sql="INSERT INTO mdl_metadata(id_course, property, value, courseGrade) VALUES ($id, 'max_age', '".$choices[$index2]."', $courseGrade)";
	$DB->execute($sql);
	$sql="UPDATE mdl_metadata SET value='".$choices[$index2]."' WHERE property='max_age' AND id_course='".$id."' AND id_course_sections IS NOT NULL";
	$DB->execute($sql);
	
	//Age da fare con singolo metadato... per RS
	$sql="INSERT INTO mdl_metadata(id_course, property, value, courseGrade) VALUES ($id, 'age', '".$choices[$index1]."-".$choices[$index2]."', ".$courseGrade.")";
	$DB->execute($sql);
	$sql="UPDATE mdl_metadata SET value='".$choices[$index1]."-".$choices[$index2]."' WHERE property='age' AND id_course='".$id."' AND id_course_sections IS NOT NULL";
	$DB->execute($sql);
	

	//Category
	/*$index3 = $data->Category;
	$sql="SELECT DISTINCT property_value AS value FROM mdl_metadata_descr WHERE property_value = \"".$cat."\"";
	$fields = $DB->get_records_sql($sql);
	$choices = array();
	$i = 0;
	foreach($fields as $field) {
		$choices[$i] = $field->value;
		$i++;
	}
	for($i = 0; $i < count($index3); $i++) {
		$value = $index3[$i];
		$sql="INSERT INTO mdl_metadata(id_course, property, value, courseGrade) VALUES ($id, 'category', \"".$choices[$value]."\", ".$courseGrade.")";
		$DB->execute($sql);
	}*/
	
	//s_t_mod Hui
	$sql="INSERT INTO mdl_metadata(id_course, property, value, courseGrade) VALUES ($id, 'category', \"".$cat."\", ".$courseGrade.")";
	$DB->execute($sql);
	//------------------------------------------------------------------------------


	//Specified Required/Acquired Skills

	//preleva le skills associate alla categoria
	
	//s_t_mod Hui: controllo se si tratta di una sottocategoria 
	
	//$sql="SELECT id, property_value AS value FROM mdl_metadata_descr WHERE property_name = 's_req_skill' AND category = \"".$cat."\"";

	if(is_subcategory($category->id))
		$categoryName=get_parent_name($category->id);
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

	//Specified Required Skills
	for ($j = 0; $j <= $i; $j++) {
		//preleva l'i-esima checkbox
		$checkbox = 'checkbox1_'.$j;
		$current_checkbox = $data->$checkbox;

		if($current_checkbox == 1) {
			//preleva il grado
			$scale = 'scale1_'.$j;
			$current_scale = $data->$scale;
			$current_scale++;

			$sql="INSERT INTO mdl_metadata(id_course, property, value, grade, courseGrade) VALUES ($id, 's_req_skill', \"".$choices[$j]."\", '".$current_scale."', ".$courseGrade.")";
			$DB->execute($sql);
		}
	}

	//Specified Acquired Skills
	for ($j = 0; $j <= $i; $j++) {
		//preleva l'i-esima checkbox
		$checkbox = 'checkbox2_'.$j;
		$current_checkbox = $data->$checkbox;

		if($current_checkbox == 1) {
			//preleva il grado
			$scale = 'scale2_'.$j;
			$current_scale = $data->$scale;
			$current_scale++;

			$sql="INSERT INTO mdl_metadata(id_course, property, value, grade, courseGrade) VALUES ($id, 's_acq_skill', \"".$choices[$j]."\", '".$current_scale."', ".$courseGrade.")";
			$DB->execute($sql);
		}
	}

	/***********************************************/
	/* 		      FINE                     */
	/***********************************************/
	?>