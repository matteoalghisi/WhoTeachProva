<?php

    $conn = false;
    
	//funzione che connette a moodlenett
    function GetMyConnection($dbname = "lms_2020")
    {
    	global $conn;
        CleanUpDB();
		
		$conn = mysqli_connect( 'localhost', 'admin', '45Etjaj81ZYtCGxh');
		if (!$conn)
			return -1;
		else
		{		
			mysqli_set_charset($conn, 'utf8');
	   		if (!mysqli_select_db($conn, $dbname))
				return -2;
			else		
				return $conn;
		}
    }
    //funzione che connette a nettrs
    /*function GetMyConnection2()
    {
        global $conn;
        if( $conn )
            return $conn;
        $conn = mysql_connect( 'localhost', 'root', 'davide') or die('Could not connect to server.' );
       mysql_set_charset('utf8',$conn);
	   mysql_select_db('nettrs', $conn) or die('Could not select database.');
	
        return $conn;
    }*/

    function CleanUpDB()
    {
        global $conn;
        if( $conn != false )
        	mysqli_close($conn);
        $conn = false;
    }

	function errorDB($number) {
		switch ($number)
		{
			case -1:
				return "Could not connect to db";
				break;
			case -2:
				return "Could not select db";
				break;
		}
	}

?>
