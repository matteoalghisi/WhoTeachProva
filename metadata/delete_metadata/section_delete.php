<?php
/*******************************
QUESTO FILE ELIMINA I METADTADATI E TUTTE LE RELATIVE
RISORSE DI UNA SEZIONE/MODULO CHE STA VENENDO ELIMINATO

//viene chiamato da /course/editsection.php
********************************/

$course_id = (is_object($course)) ? $course->id : (int)$course;
$section_num = (is_object($section)) ? $section->section : (int)$section;
$sectionToDelete = $DB->get_record('course_sections', array('course' => $course_id, 'section' => $section_num));
$id_sectionToDelete = $sectionToDelete->id;

$sql = "DELETE FROM mdl_metadata WHERE Id_course_sections = ".$id_sectionToDelete."";
$DB->execute($sql);

?>