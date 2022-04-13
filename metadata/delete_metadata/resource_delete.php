<?php
/*******************************
QUESTO FILE ELIMINA TUTTI METADTADATI DI UNA DATA RISORSORSA CHE STA VENEDO ELIMINATA

//viene chiamato da /course/lib.php
********************************/


$id_resourceToDelete = $modid;
echo "id: ".$id_resourceToDelete."";

$sql = "DELETE FROM mdl_metadata WHERE Id_resource = ".$id_resourceToDelete."";
$DB->execute($sql);

?>