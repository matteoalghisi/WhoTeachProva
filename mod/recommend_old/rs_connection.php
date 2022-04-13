<?php
require_once('../../config.php');
global $CFG;
require_once($CFG->libdir.'/moodlelib.php');
//check if usrer is logged in and has capability to create resource
require_login($course);
if (!has_capability('moodle/course:managefiles', context_system::instance()))
	die();


function CallAPI($url, $method = 'POST', $data = false)
{
    $curl = curl_init();

    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Optional Authentication:
    // curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    //curl_setopt($curl, CURLOPT_USERPWD, "username:password");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}


header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$data['apikey'] = 'b8e306d7d4985d93';
/*
$data = array(
    'apikey' => 'b8e306d7d4985d93',
    'type' => 'recommend',
	'userid' => '2',
    'keywords' => ["ciao"]
);*/

$data = json_encode($data);


//test
//echo (CallAPI('http://localhost:7071/api/HttpExample', 'POST', $data));
echo (CallAPI('https://socialthings-rs-ml.westeurope.cloudapp.azure.com:5000/api/rs', 'POST', $data));
//controllo certificato sulla porta 50001
//echo (CallAPI('https://socialthings-rs-ml.westeurope.cloudapp.azure.com:5001/api/rs', 'POST', $data));



?>