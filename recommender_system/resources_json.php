<?php

    require_once('../config.php');
    require_once('../search_engine/php/mysql_conn.php');

    $risorse=$_POST['arrayRisorse'];
    $utilità=$_POST['utilitàRisorse'];
    $regole=$_POST['regolaRisorse'];
    $arrayRisorse=array();

    $connection = GetMyConnection();

	if ($connection < 0){
        print errorDB($connection);
        die();
    }

    for($i=0;$i<count($risorse);$i++){

        $id=$risorse[$i];


		$result = mysqli_query($connection, "SELECT title,disciplines,resource_image,average_rating,language,difficulty,duration,format,type,min_age,max_age,description FROM mdl_merlot_data WHERE id=$id");

        while($row=mysqli_fetch_row($result)){

            $currentResource=array("title"=>$row[0],"disciplines"=>$row[1],"resource_image"=>$row[2],"average_rating"=>$row[3],"language"=>$row[4],"difficulty"=>$row[5],"duration"=>$row[6],"format"=>$row[7],"type"=>$row[8],"min_age"=>$row[9],"max_age"=>$row[10],"description"=>$row[11],"id"=>$id,"utility"=>$utilità[$i],"preferences"=>$regole[$i]);
            array_push($arrayRisorse,$currentResource);

        }

    }


    echo json_encode($arrayRisorse);


?>