<?php

    require_once('../config.php');
    require_once('../search_engine/php/mysql_conn.php');

    $resource=$_POST["nameResource"];
    $resourceAttributes=[];

    $connection = GetMyConnection();

	if ($connection < 0){
        print errorDB($connection);
        die();
    }

    $result = mysqli_query($connection, "SELECT language,difficulty,duration,format,type,min_age,max_age,description FROM mdl_merlot_data WHERE title=$resource");
    echo $result;

    /*while($row=mysqli_fetch_row($result)){
        array_push($resourceAttributes,$row[0]);
        array_push($resourceAttributes,$row[1]);
        array_push($resourceAttributes,$row[2]);
        array_push($resourceAttributes,$row[3]);
        array_push($resourceAttributes,$row[4]);
        array_push($resourceAttributes,$row[5]);
        array_push($resourceAttributes,$row[6]);
        array_push($resourceAttributes,$row[7]);
    }*/

    //echo $resourceAttributes;



?>