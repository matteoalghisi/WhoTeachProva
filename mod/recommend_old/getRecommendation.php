<?php
require_once('../../config.php');
global $CFG, $course;
require_once($CFG->libdir.'/moodlelib.php');
//check if usrer is logged in and has capability to create resource
require_login($course);
if (!has_capability('moodle/course:managefiles', context_system::instance())){
	$aResult['error'] = 'non hai i privilegi per utilizzare il Recommender System';
    echo json_encode($aResult);
	die();
}

global $DB;
	
	function get_module_name($module){
		global $DB;
		$tabella_file = '';
		
		/*switch($module)
			{
				case 1: $tabella_file = 'assign';
				break;
				// manca il 2 
				case 3: $tabella_file = 'book';	
				break;
				case 4: $tabella_file = 'chat';
				break;
				case 5: $tabella_file = 'choice';
				break;
				case 6: $tabella_file = 'data';
				break;
				// manca il 7 
				case 7: $tabella_file = 'folder';
				break;
				case 8: $tabella_file = 'folder';
				break;
				case 9: $tabella_file = 'forum';
				break;
				case 10: $tabella_file = 'glossary';
				break;
				case 11: $tabella_file = 'imscp';
				break;
				case 12: $tabella_file = 'label';
				break;
				case 13: $tabella_file = 'lesson';
				break;
				case 14: $tabella_file = 'lti';  // tool esterno?
				break;
				case 15: $tabella_file = 'page';
				break;
				case 16: $tabella_file = 'quiz';
				break;
				case 17: $tabella_file = 'resource';  
				break;
				case 18: $tabella_file = 'scorm'; 
				break;
				case 19: $tabella_file = 'survey';	
				break;
				case 20: $tabella_file = 'url';
				break;
				case 21: $tabella_file = 'wiki';
				break;
				case 22: $tabella_file = 'workshop';
				break;
				case 26: $tabella_file = 'bigbluebuttonbn';
				break;
			}*/
			
			//sostituito switch statico con nome tabelle preso direttamente da DB
			$tabella_file = $DB->get_record('modules', array('id'=>$module));
			$tabella_file = $tabella_file->name;
			
		return $tabella_file;
	}




    header('Content-Type: application/json');

    $aResult = array();

    if( !isset($_POST['functionname']) ) { $aResult['error'] = 'No function name!'; }

    if( !isset($_POST['arguments']) ) { $aResult['error'] = 'No function arguments!'; }

    if( !isset($aResult['error']) ) {

        switch($_POST['functionname']) {
            case 'getRS':
				//$aResult['result'] = "";
				$resId = implode(",",$_POST["arguments"]);	

				$result = [];
				$res = $DB->get_records_sql('SELECT id, module, instance FROM mdl_course_modules WHERE id IN('.$resId.') and deletioninprogress = 0');
				//print_object($res);
				$i = 0;
                foreach ($res as $r){
					$module = get_module_name($r->module); 
					$instance = $r->instance; 

					$res_name = $DB->get_record_sql('SELECT name FROM mdl_'.$module.' WHERE id = '.$instance.'')->name;
					//questo sotto è un array di oggetti contenti:
					//nome risorsa ($res_name)
					//url di anteprima risorsa
					$result[$i] = new stdClass();					
					$result[$i]->id = $r->id;
					$result[$i]->name =$res_name;
					$result[$i]->module = $module;
					$result[$i]->url = $CFG->wwwroot."/mod/".$module."/view.php?id=".$r->id;
					
					$i+=1;
				}	
				
				//echo(var_dump($instance));
				$aResult = $result;
				break;

            default:
               $aResult['error'] = 'Not found function '.$_POST['functionname'].'!';
               break;
        }

    }

    echo json_encode($aResult);

?>
