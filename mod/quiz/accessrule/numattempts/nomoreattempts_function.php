<?php


			//s_t_mod Hui
			$sql="SELECT id from mdl_course_modules WHERE instance='".$this->quiz->id."' and course='".$this->quiz->course."'";
			$fields = $DB->get_record_sql($sql);
			$module_id=reset($fields);
			
			$user_id=$USER->id;
			
			$sql="SELECT completionstate FROM mdl_course_modules_completion WHERE coursemoduleid='".$module_id."' and userid='".$user_id."'";
			$fields = $DB->get_record_sql($sql);
			$state=0;
			if($fields)
				$state=reset($fields);
			
			//valuto lo stato di completamento del quiz:
			//- 0 non completato
			//- 1 completato
			//- 2 completato con successo
			//- 3 completato senza successo
			if($state!=3)
				return get_string('nomoreattempts', 'quiz');
			
			//il quiz è stato completato senza successo
			//reset dei dati
			
			$courseid=$this->quiz->course;
			
			$sql="SELECT id,module,instance FROM mdl_course_modules WHERE course='".$courseid."'";
			$fields = $DB->get_records_sql($sql);
			
			foreach ($fields as $field){
				$course_mod_id=$field->id;
				$course_mod_type_id=$field->module;
				$instance_id=$field->instance;
				
				$sql="SELECT name FROM mdl_modules WHERE id='".$course_mod_type_id."'";
				$field=$DB->get_record_sql($sql);
				$mod_name = reset($field);
				
				$sql='';
				switch($mod_name){
					
					case 'quiz':
						save_and_delete('quiz_attempts',array('userid' => $user_id, 'quiz' =>$instance_id), $user_id, $courseid);
						save_and_delete('quiz_grades',array('userid' => $user_id, 'quiz' =>$instance_id), $user_id, $courseid);
						break;
						
					case 'lesson':
						save_and_delete('lesson_timer',array('userid' => $user_id, 'lessonid' =>$instance_id), $user_id, $courseid);
						save_and_delete('lesson_grades',array('userid' => $user_id, 'lessonid' =>$instance_id), $user_id, $courseid);
						save_and_delete('lesson_attempts',array('userid' => $user_id, 'lessonid' =>$instance_id), $user_id, $courseid);
						save_and_delete('lesson_branch',array('userid' => $user_id, 'lessonid' =>$instance_id), $user_id, $courseid);
						break;
						
					case 'assign':
						save_and_delete('assign_user_mapping',array('userid' => $user_id, 'assignment' =>$instance_id), $user_id, $courseid);
						
						$sql="SELECT id FROM mdl_assign_grades WHERE userid='".$user_id."' AND assignment=".$instance_id."";
						$field=$DB->get_record_sql($sql);
						if($field){
							$grade_id=reset($field);
							save_and_delete('assignfeedback_comments',array('grade' => $grade_id, 'assignment' => $instance_id) , $user_id, $courseid);
							save_and_delete('assignfeedback_editpdf_annot', array('gradeid' => $grade_id), $user_id, $courseid);
							save_and_delete('assignfeedback_editpdf_cmnt', array('gradeid' => $grade_id), $user_id, $courseid);
							save_and_delete('assignfeedback_editpdf_rot', array('gradeid' => $grade_id), $user_id, $courseid);
							save_and_delete('assignfeedback_file',array('grade' => $grade_id, 'assignment' => $instance_id), $user_id, $courseid);
							
						}
						
						save_and_delete('assign_grades',array('userid' => $user_id, 'assignment' =>$instance_id), $user_id, $courseid);
						save_and_delete('assign_user_flags',array('userid' => $user_id, 'assignment' =>$instance_id), $user_id, $courseid);
						
						$sql="SELECT id FROM mdl_assign_submission WHERE userid='".$user_id."' AND assignment=".$course_mod_id."";
						$field=$DB->get_record_sql($sql);
						if($field){
							$submit_id=reset($field);
							save_and_delete('assignsubmission_file',array('submission' => $submit_id, 'assignment' => $instance_id), $user_id, $courseid);
							save_and_delete('assignsubmission_onlinetext',array('submission' => $submit_id, 'assignment' => $instance_id), $user_id, $courseid);
						}
						
						save_and_delete('assign_submission',array('userid' => $user_id, 'assignment' => $instance_id), $user_id, $courseid);
						break;
						
					case 'survey':
						save_and_delete('survey_answers',array('userid' => $user_id, 'survey' => $instance_id), $user_id, $courseid);
						save_and_delete('survey_analysis',array('userid' => $user_id, 'survey' => $instance_id), $user_id, $courseid);
						break;
						
						
					case 'choice':
						save_and_delete('choice_answers',array('userid' => $user_id, 'choiceid' => $instance_id), $user_id, $courseid);
						break;
						
						
				}
				
				save_and_delete('course_modules_completion',array('userid' => $user_id, 'coursemoduleid' =>$course_mod_id), $user_id, $courseid);
				
			}
			
		
			save_and_delete_grade_grades($user_id,$courseid);
			
			save_and_delete('course_completions',array('course' => $courseid, 'userid' => $user_id), $user_id, $courseid);
			save_and_delete('course_completion_crit_compl',array('course' => $courseid, 'userid' => $user_id), $user_id, $courseid);
			
			// Difficult to find affected users, just purge all completion cache.
			cache::make('core', 'completion')->purge();
			cache::make('core', 'coursecompletion')->purge();
			
			//return 'Purtroppo non sei riuscito a superare il test. Devi rifare il corso.';
			return get_string('quiz_failed', 'quiz');

	 /**
     * Salva i dati che verranno eliminati nel db deleted_data
	 * e li elimina dal db lms_2020
     * @param strng $table_name: nome della tabella da cui i record vengono eliminati
     * @param array $param: i parametri per identificare il record da eliminare
	 * @param int $userid: id dell'utente
     * @param int $courseid: id del corso
     */
	function save_and_delete($table_name,$param, $userid, $courseid){
		global $DB;
		
		$data_to_save=$DB->get_records($table_name, $param);
		
		$conn = new mysqli('localhost', 'backup', 'Pippo123?', 'lms_2020');
		
		$json='';
		if($data_to_save){
			$json=json_encode($data_to_save);
			$sql="INSERT INTO mdl_deleted_data(table_name,userid,courseid,data) VALUES('$table_name',$userid,$courseid,'$json')";
			//$DB->execute($sql);	non è possibile usare la funzione di moodle per l'inserimento in quanto risultano problemi per le " presenti nel json data
			$conn->query($sql);
		}
		
		$conn->close();
		
		$DB->delete_records($table_name, $param);
		
		
	}
	
	 /**
     * Salva i dati della tabella MDL_GRADE_GRADES che verranno eliminati nel db deleted_data
	 * e li elimina dal db lms_2020
     * @param int $userid: id dell'utente a cui appartengono i record da eliminare
     * @param int $courseid: id del corso a cui appartengono i record da eliminare
     */
	function save_and_delete_grade_grades($userid,$courseid){
		global $DB;
		
		$data_to_save=$DB->get_records_sql("SELECT * FROM mdl_grade_grades  WHERE userid='".$userid."' and itemid IN (SELECT id from mdl_grade_items WHERE courseid='".$courseid."')");
		
		$conn = new mysqli('localhost', 'backup', 'Pippo123?', 'lms_2020');
		
		$json='';
		if($data_to_save){
			$json=json_encode($data_to_save);
			$sql="INSERT INTO mdl_deleted_data(table_name,userid,courseid,data) VALUES('grade_grades',$userid,$courseid,'$json')";
			//$DB->execute($sql);	non è possibile usare la funzione di moodle per l'inserimento in quanto risultano problemi per le " presenti nel json data
			$conn->query($sql);
		}
		
		$conn->close();
		
		$sql="DELETE FROM mdl_grade_grades  WHERE userid='".$userid."' and itemid IN (SELECT id from mdl_grade_items WHERE courseid='".$courseid."')";
		$DB->execute($sql);
		
		
	}


?>