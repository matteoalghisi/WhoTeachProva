<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Instance add/edit form
 *
 * @package    mod_recommend
 * @copyright  2004-2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 

print '<link rel="stylesheet" href="'.$CFG->wwwroot.'/mod/recommend/tagcloud/tagcloud.css" />';
print '<link rel="stylesheet" href="'.$CFG->wwwroot.'/mod/recommend/tagcloud/jqcloud.min.css"/>';
//print '<link rel="stylesheet" href="'.$CFG->wwwroot.'/mod/recommend/styles.css"/>';
print "<style>
	#fgroup_id_buttonar{
		visibility:hidden;
	}
	#id_competenciessection{
		visibility:hidden;
	} 
	#id_tagshdr{
		visibility:hidden;
	} 
	#id_activitycompletionheader{
		visibility:hidden;
	} 
	#id_modstandardelshdr{
		visibility:hidden;
	}
	#id_availabilityconditionsheader{
		visibility:hidden;
	}
</style>";


defined('MOODLE_INTERNAL') || die;

require_once(__DIR__.'/locallib.php');
require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_recommend_mod_form extends moodleform_mod {

    function definition() {
        global $CFG,$DB, $PAGE;
        $mform = $this->_form;

        $config = get_config('recommend');		
		
		//Modificare le keywords più frequenti
		
		//get keywords list for wordcloud
		$category = $DB->get_records_sql('SELECT name FROM mdl_course_categories WHERE id = (SELECT category FROM mdl_course WHERE id = '.$PAGE->course->id.')');
		//echo(var_dump(reset($category)));
		$category_name = reset($category)->name;
		$sql="SELECT Id_metadata, value FROM mdl_metadata WHERE Id_resource IS NOT NULL AND Id_course IS NOT NULL AND Id_course_sections IS NOT NULL 
		AND property = 'keywords' and Id_course IN(SELECT Id_course FROM mdl_metadata WHERE property = 'category' AND value = '".$category_name."' )";
		$fields = $DB->get_records_sql($sql);
		$keyword_list = array();
		foreach($fields as $field) {
			array_push($keyword_list, $field->value);
			//echo ($field->value);
			//$choices[$i] = convert_metadata($field->property).": ".translate_language(translate_format(translate_type(translate_time($field->value))));
			//$i++;
		}
		//clacolo frequenza ogni parola
		$frequency = array();
		foreach ($keyword_list as $w)
	    { 	
			$w= trim($w);
			if (strlen($w))
			{	
				if (array_key_exists($w, $frequency)){
					$frequency[$w]+=1;
				}
				else{
					$frequency[$w] = 1;
				}
			}
		}
						

        $mform->addElement('header', 'general', get_string('general', 'form'));

        /*$mform->addElement('text', 'name', get_string('name'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
		*/
		
		//stampo la tagcloud di keywords
		$mform->addElement('text', 'tagcloud', '', array('size'=>'64'));
		$mform->setType('tagcloud', PARAM_CLEANHTML);
		$mform->addElement('text', 'keywords', 'Scegli le keywords (separator ", ")', array('size'=>'64'));
		$mform->setType('keywords', PARAM_TEXT);
		$mform->addElement('submit', 'submit_recommend', 'Suggeriscimi delle risorse!');
		
		//include('../metadata/mod/form_general.php'); // _s_t_mod_metadata
		
	/*	
		
        
		$this->standard_intro_elements(get_string('moduleintro'));

        // Appearance.
        $mform->addElement('header', 'appearancehdr', get_string('appearance'));

        $alloptions = recommend_get_numbering_types();
        $allowed = explode(',', $config->numberingoptions);
        $options = array();
        foreach ($allowed as $type) {
            if (isset($alloptions[$type])) {
                $options[$type] = $alloptions[$type];
            }
        }
        if ($this->current->instance) {
            if (!isset($options[$this->current->numbering])) {
                if (isset($alloptions[$this->current->numbering])) {
                    $options[$this->current->numbering] = $alloptions[$this->current->numbering];
                }
            }
        }
        $mform->addElement('select', 'numbering', get_string('numbering', 'recommend'), $options);
        $mform->addHelpButton('numbering', 'numbering', 'mod_recommend');
        $mform->setDefault('numbering', $config->numbering);

	*/
	
        $alloptions = recommend_get_nav_types();
        $allowed = explode(',', $config->navoptions);
        $options = array();
        foreach ($allowed as $type) {
            if (isset($alloptions[$type])) {
                $options[$type] = $alloptions[$type];
            }
        }
        if ($this->current->instance) {
            if (!isset($options[$this->current->navstyle])) {
                if (isset($alloptions[$this->current->navstyle])) {
                    $options[$this->current->navstyle] = $alloptions[$this->current->navstyle];
                }
            }
        }
		
	/*
		
        $mform->addElement('select', 'navstyle', get_string('navstyle', 'recommend'), $options);
        $mform->addHelpButton('navstyle', 'navstyle', 'mod_recommend');
        $mform->setDefault('navstyle', $config->navstyle);

        $mform->addElement('checkbox', 'customtitles', get_string('customtitles', 'recommend'));
        $mform->addHelpButton('customtitles', 'customtitles', 'mod_recommend');
        $mform->setDefault('customtitles', 0);

	*/
	
		$this->standard_coursemodule_elements();
		
	

        $this->add_action_buttons();
		
		?>
		<!-- TagCloud -->
		<script type="text/javascript">
				var word_array = [
					<?php
						$i=0;
						foreach ($frequency as $w => $f)
						{
							//$f = $f % 5; // devo normalizzare perché le classi sono al massimo 5
							if($i<50)//stampo solo le prime 50 parole nel tagcloud
							{ 
								echo '{text: "'.$w.'", weight: '.$f.', link: "javascript:;;"},';
							}
							else
								break;
							$i++;
						}
					?>
				  ];
			</script>
			<?php
    }
}

?>

		<script type="text/javascript">

			course_id = <?php echo($course->id); ?>;
			section_id = <?php echo($cw->id); ?>;
		</script>

<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.1.min.js"></script>

<!-- TagCloud -->
			
			<script id = "script_tagcloud" src="<?php echo $CFG->wwwroot ?>/mod/recommend/tagcloud/jqcloud.js"></script>
			<script id = "script_rs" src="<?php echo $CFG->wwwroot ?>/mod/recommend/script.js"></script>
			<script id = "script_rs" src="<?php echo $CFG->wwwroot ?>/mod/recommend/tagcloud/script.js"></script>
			<script>	
			
				function getKeys(){
					key_arr = $("#id_keywords").val().split(",");
					key_arr.forEach(function(item, index){
						key_arr[index] = item.trimStart().trimEnd();
					});
					return key_arr;
				}
				
				$(document).ready(function(){
					
					$("#id_tagcloud").parent().children("span").children("a").addClass("keyword")

					$(".keyword").click(function(){ 
						console.log("ok");
						var keyName = $(this).text(); 
						var formId = $("#id_keywords");
						var formValue = formId.val()

						if(formValue) {
							if (formValue.indexOf(keyName)) {
								formId.val(formValue + "," + keyName); 
							}
						} else {
							formId.val(keyName);
						}  
					});
					
					//recupero id utente con php
					<?php 
					Global $USER;
					$myuser=$USER->id;
					echo ("user_id = ".$myuser.";");
					?>
					
				
						
					$("#id_tagcloud").css({"height": "280px", "visibility":"hidden", "width":"80%"});
					$("#id_tagcloud").parent().jQCloud(
						word_array, 
						{
							//classPattern: null,
							autoResize: true,
						  	colors: [ "#e04908", "#f4a770","#ed6c09", "#e8853a"],
							//colors: ["#800026", "#bd0026", "#e31a1c", "#fc4e2a", "#fd8d3c", "#feb24c", "#fed976", "#ffeda0", "#ffffcc"],
						  	fontSize: {from: 0.02, to: 0.03},
							
										
						},
						
						
					);
					
									
					
					$("#id_submit_recommend").attr('type', 'button');
					$("#id_submit_recommend").attr('onclick', 'getRecommendation(user_id,getKeys())');
					//console.log("ciao");
				});
			</script>
			
			
<!-- -->

<script src="https://cdn.anychart.com/releases/v8/js/anychart-base.min.js"></script>
<script src="https://cdn.anychart.com/releases/v8/js/anychart-tag-cloud.min.js"></script>

