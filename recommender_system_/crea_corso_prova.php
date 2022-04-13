<?php

    $nomeCorso=$_POST['courseName'];
    $categoriaCorso=$_POST['courseCategory'];
    $sottocategoriaCorso=$_POST['courseSubcategory'];
    $sezioniCorso=$_POST['courseSections'];
    $risorseCorso=$_POST['idResources'];

    $risorse=array();


    for($i=0;$i<count($risorseCorso);$i++){
        array_push($risorse,array("id"=>$risorseCorso[$i],"name"=>"risorsa ".$risorseCorso[$i],"module"=>"resource"));
    }

    $infoCorso=array("1"=>array("name"=>"modulo di prova","resources"=>$risorse));

    echo json_encode($infoCorso);


?>