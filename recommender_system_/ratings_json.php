<?php

    require_once('../config.php');
    require_once('../search_engine/php/mysql_conn.php');
    require_once('dbConnection.php');

    $risorse=$_POST['arrayRisorse'];
    $valutazioniTotali=[];

    $connection = connectToDB();

	if ($connection < 0){
        print errorDB($connection);
        die();
    }

    for($i=0;$i<count($risorse);$i++){

        $id=$risorse[$i];
        $votiRisorsa=array();


		$result = mysqli_query($connection, "SELECT rating AS Voto, COUNT(rating) AS NumValutazioni FROM mdl_merlot_comments WHERE resource=$id GROUP BY rating");

        while($row=mysqli_fetch_row($result)){

            $voto=array($row[0]=>$row[1]);
            array_push($votiRisorsa,$voto);

        }

        $valutazioniTotali[$id]=$votiRisorsa;


    }


    echo json_encode($valutazioniTotali);


?>