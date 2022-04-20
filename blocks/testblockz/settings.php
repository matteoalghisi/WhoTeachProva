<?php
defined('MOODLE_INTERNAL') || die();

global $DB;
$results = $DB->get_records_sql('SELECT DISTINCT d.data
                 FROM {user_info_data} as d JOIN {user_info_field} as f ON d.fieldid=f.id WHERE shortname = ? ORDER BY d.data',
				 ['Settore']);
				 
$options = array();
foreach ($results as $key => $object) {
		array_push($options,($object->data));
	}

if ($ADMIN->fulltree){
        $settings->add(new admin_setting_configcheckbox('block_testblockz/showcourses',
                                'Show courses', 'Show courses instead of users', 0));
		$settings->add(new admin_setting_configselect('block_testblockz/showcourses2',
                                'Show courses2', 'Show courses instead of users2', 'datatables', $options));
								
}
