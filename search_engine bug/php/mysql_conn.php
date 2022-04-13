<?php

    $conn = false;
    
    function GetMyConnection()
    {
        global $conn;
        if( $conn )
            return $conn;
        $conn = mysqli_connect( 'localhost', 'admin', '45Etjaj81ZYtCGxh') or die('Could not connect to server.' );
		$conn->set_charset("utf8");
        mysqli_select_db($conn, 'lms_2020') or die('Could not select database.');
	
        return $conn;
    }
    
    function CleanUpDB()
    {
        global $conn;
        if( $conn != false )
            mysql_close($conn);
        $conn = false;
    }
	
?>
