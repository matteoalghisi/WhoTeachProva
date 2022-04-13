<?php

    $conn = false;
        
    function connectToDB()
    {
        global $conn;
        $dbname='lms_2021';
        if( $conn )
            return $conn;
        $conn = mysqli_connect( 'localhost', 'admin', '45Etjaj81ZYtCGxh') or die('Could not connect to server.' );
        $conn->set_charset("utf8");
        mysqli_select_db($conn, $dbname) or die('Could not select database.');

        return $conn;
    }

?>