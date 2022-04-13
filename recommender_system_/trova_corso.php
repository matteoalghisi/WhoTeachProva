<?php

    require_once('../config.php');
    require_once('../search_engine/php/mysql_conn.php');
    require_once('dbConnection.php');


    $connection = connectToDB();

	if ($connection < 0){
        print errorDB($connection);
        die();
    }


	$result = mysqli_query($connection, "SELECT Max(id) FROM mdl_course");

    while($row=mysqli_fetch_row($result)){

            $idcorso=$row[0];

    }

    echo $idcorso;


?>