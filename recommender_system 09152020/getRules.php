<?php 
	session_start();
  
	//Controllo che la pagina sia stata riaggiornata SENZA CAMBIARE LINGUA
	if($_POST['action'] == 1)
	{ 
	  	error_reporting(E_ALL);

		// Allow the script to hang around waiting for connections.
		set_time_limit(0);

		// Turn on implicit output flushing so we see what we're getting as it comes in.
		ob_implicit_flush();

		$address = '::1';
		$port = 20001;

		if (($sock = socket_create(AF_INET6, SOCK_STREAM, 0)) === false) {
			echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
			exit(0);
		}
		
		socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 30, 'usec' => 0));
		socket_set_option($sock, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 30, 'usec' => 0)); 

		if (socket_connect($sock, $address, $port) === false) {
			echo "socket_connect() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
			exit(0);
		}

		$token = array();
		$token["token"] = $_POST["token"]; 
		$data_json = json_encode($token);
		$message = $data_json. "\r\n";

		$_SESSION["tmp"] = $message;


		if (socket_write($sock, $message, strlen($message)) === false){
			echo "socket_write() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
			exit(0);
		}
		
		$rules = socket_read($sock,200000,PHP_NORMAL_READ);

		$_SESSION['rules_risp'] = $rules;
		print_r($rules);

		socket_close($sock);
	}
	else
	{
		$risp = $_SESSION['rules_risp'];
		print_r(utf8_decode($risp));
   	}

?>
