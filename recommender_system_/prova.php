<?php

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');

    if(isset($_POST["id"])){
        echo "webhook received";
    }else{
        echo "webhook error";
    }


?>