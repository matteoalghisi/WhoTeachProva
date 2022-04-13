<?php
/*******************************
QUESTO FILE ELIMINA I METADTADATI E TUTTE LE RELATIVE SEZIONI E 
RISORSE DI UN CORSO CHE STA VENENDO ELIMINATO

//viene chiamato da /course/delete.php
********************************/

$id_courseToDelete = $course->id;
$sql = "DELETE FROM mdl_metadata WHERE Id_course = ".$id_courseToDelete."";
$DB->execute($sql);

?>