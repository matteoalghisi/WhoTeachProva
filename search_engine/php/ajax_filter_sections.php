<?php
require_once('../../config.php');
require 'knowledge_base.php';

if ($_SERVER['REQUEST_METHOD']=="POST") {
$HTTP_STR=$_POST;
}else{
$HTTP_STR=$_GET;
}
//var_dump($HTTP_STR);
$num_array=0;
$str_metadata_full = '';
$string_key = '';
$color = "white";
for ($i = 1; $i <= 10; $i++) {
	$stringa="select".$i;
	switch ($stringa) {
		case 'select1':
		$attr_name="language";
		break;
		case 'select2':
		$attr_name="category";
		break;
		case 'select3':
		$attr_name="d_req_skill";
		break;
		case 'select4':
		$attr_name="d_acq_skill";
		break;
		case 'select5':
		$attr_name="resourcetype";
		break;
		case 'select6':
		$attr_name="difficulty";
		break;
		case 'select7':
		$attr_name="min_age";
		break;
		case 'select8':
		$attr_name="max_age";
		break;
		case 'select9':
		$attr_name="format";
		break;
		case 'select10':
		$attr_name="time";
		break;
	} 



	
	
	//$key_value=clean_phrase_input($key_value);
	//old
	//$string_key=" and x.id in (SELECT id_section from full_text_section where  MATCH (name,summary,keywords) AGAINST('".$key_value."' IN BOOLEAN MODE));";
	
	//vado a prendere le keyword
	$key_value=$HTTP_STR["key_value"];
	if ($key_value != ''){
		$key_value = "'".$key_value."'";
		//aggiungo virgolette per la query tra le virgole ce separano le key
		$key_value=str_replace(",", "','", $key_value);
		//rimuovo spazi
		$key_value = preg_replace("/\'\s+/", "'", $key_value);
		$string_key=" and x.id in (SELECT Id_course_sections from mdl_metadata where  mdl_metadata.Property = 'keywords' and mdl_metadata.Value IN(".$key_value."));";
	}
	

	if ($HTTP_STR[$stringa]!='') {
	 $str_metadata=$attr_name."=".$HTTP_STR[$stringa].",-";
	 $str_metadata_full=$str_metadata_full.$str_metadata;
	 $num_array=$num_array+1;
	}

}

print_r($str_metadata_full);

$str_metadata_full=substr($str_metadata_full, 0, strlen($str_metadata_full)-2);
$str_metadata_full=str_replace(",-", "!", $str_metadata_full);
$stringa_where='';

for ($i = 1; $i <= $num_array; $i++) {
	$nome_array=substr($str_metadata_full, 0, strpos($str_metadata_full, '!'));
	if ($nome_array=='') {$nome_array=$str_metadata_full;}
	$str_metadata_full=substr($str_metadata_full, strlen($nome_array)+1, strlen($str_metadata_full) );

	$nome_array_key=substr($nome_array, 0, strpos($nome_array, '='));
	$nome_array=substr($nome_array, strlen($nome_array_key)+1, strlen($nome_array) );
	$array_ex= explode(",", $nome_array);
	$nome_array = "";
	for($j = 0; $j < count($array_ex); $j++)
		$nome_array = $nome_array."'".$array_ex[$j]."',";

	$nome_array = substr($nome_array, 0, -1);
	$nome_array = str_replace("l'", "l\\'", $nome_array);	
	
	//x.id = z.Id_course_sections and
	//VERSIONE VECCHIA ---> $stringa_where=$stringa_where."and (exists (select * from mdl_metadata z where  (z.Property='".$nome_array_key."' and z.Value IN (".$nome_array.")))) ";
	//se la chiave di ricerca e` una categoria, vado a cercarla anche a livello di corso non solo modulo che potrebbe averla persa
	if($nome_array_key == "category")
		
		//s_t_mod Hui: non uso più nome della categoria ma l'id
		/*$stringa_where=$stringa_where."AND 
										(x.id IN (SELECT m.Id_course_sections FROM mdl_metadata m WHERE  m.Property='".$nome_array_key."' and m.Value IN (".$nome_array."))
											OR x.course IN (SELECT m.Id_course FROM mdl_metadata m WHERE  m.Property='".$nome_array_key."' and m.Value IN (".$nome_array."))
										)";*/
		$stringa_where=$stringa_where."AND 
										(x.id IN (SELECT m.Id_course_sections FROM mdl_metadata m WHERE  m.Property='".$nome_array_key."' and m.Value IN (SELECT name FROM mdl_course_categories WHERE id IN ( ".$nome_array." )))
											OR x.course IN (SELECT m.Id_course FROM mdl_metadata m WHERE  m.Property='".$nome_array_key."' and m.Value IN (SELECT name FROM mdl_course_categories WHERE id IN( ".$nome_array." )))
										)";
		//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	//s_t_mod Hui: modifico la query in modo che controlla le skills dal corso delle sezioni e dai moduli(in caso che esistino)
	else if($nome_array_key == "d_req_skill" || $nome_array_key == "s_req_skill" )
		$stringa_where=$stringa_where."AND 
										x.id IN (SELECT m.Id_course_sections FROM mdl_metadata m 
													WHERE m.Id_course IN (SELECT m.Id_course FROM mdl_metadata m 
																				WHERE m.Property='s_req_skill' and m.Value IN ( SELECT property_value FROM mdl_metadata_descr 
																																	WHERE id IN (".$nome_array.")) )
													OR m.Property='d_req_skill' and m.Value IN ( SELECT property_value FROM mdl_metadata_descr 
																																	WHERE id IN (".$nome_array.")) 
												) ";
	else if( $nome_array_key == "d_acq_skill"  || $nome_array_key == "s_acq_skill")
		$stringa_where=$stringa_where."AND 
										x.id IN (SELECT m.Id_course_sections FROM mdl_metadata m 
													WHERE m.Id_course IN (SELECT m.Id_course FROM mdl_metadata m 
																			WHERE m.Property='s_acq_skill' and m.Value IN ( SELECT property_value FROM mdl_metadata_descr 
																																WHERE id IN (".$nome_array.")) )
													OR m.Property='d_acq_skill' and m.Value IN ( SELECT property_value FROM mdl_metadata_descr 
																																	WHERE id IN (".$nome_array.")) 																			
																																
												)";
	
	
	//----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	else
		$stringa_where=$stringa_where."AND x.id IN (SELECT m.Id_course_sections FROM mdl_metadata m WHERE  m.Property='".$nome_array_key."' and m.Value IN (".$nome_array."))";


	//while(list($chiave,$valore)=each($nome_array_value))
	//		{
	//		$stringa_where=$stringa_where."('".$valore."' in (SELECT mdl_metadata.Value from mdl_metadata  where x.id= mdl_metadata.Id_course_sections and mdl_metadata.Property='".$nome_array_key."')) or ";
		//	}
			
		//	$stringa_where=substr($stringa_where, 0, strlen($stringa_where)-4);
		//	$stringa_where=$stringa_where.") and (";
}


if ($stringa_where=='') 
		{$sql_sections="SELECT distinct x.section,x.name,x.summary, x.id, y.fullname FROM mdl_course y,mdl_course_sections x where x.course = y.id and x.name is not null AND x.id > 2 AND sequence <> ''";
	}
	else {
//VERSIONE VECCHIA ---> $sql_sections="SELECT distinct x.section,x.name,x.summary, x.id, y.fullname FROM mdl_course y,mdl_course_sections x,mdl_metadata where x.course = y.id and x.name is not null ".$stringa_where."))) AND x.visible = 1 AND x.id > 2 AND sequence <> ''";
		$sql_sections="SELECT distinct x.section,x.name,x.summary, x.id, y.fullname 
						FROM mdl_course y,mdl_course_sections x,mdl_metadata z
						where x.course = y.id 
						and x.id = z.Id_course_sections
						and x.name is not null 
						".$stringa_where."
						AND x.visible = 1 
						AND x.id > 2 
						AND sequence <> ''";

			}
			
//print "<pre>la select : ". $sql_sections . '</pre>';
   
  // $handle = fopen('sql.txt', 'w');

  // if (fwrite($handle, $sql_sections) === FALSE) {
  //      echo "Non si riesce a scrivere nel file ($filename)";
  //      exit;
  //  }
  // fclose($handle);
//$sql_sections=$sql_sections." and x.id  not in (select id_sec_dest from nett_duplicates WHERE flag = 0)  ";
$sql_sections=$sql_sections." and x.id  not in (select id_sec_dest from nett_duplicates WHERE flag = 0) AND x.id  not in (SELECT id FROM mdl_course_sections WHERE course = '".$HTTP_STR["course"]."') ";
  
//$sql_sections=$sql_sections." and x.id (select id_sec_dest from nett_duplicates WHERE flag = 0)";

if ($key_value!='*') $sql_sections=$sql_sections.$string_key;
	
echo $sql_sections;
require 'mysql_conn.php';
$result = mysqli_query(GetMyConnection(), $sql_sections);
$i=1;

Global $DB;


while ($row = mysqli_fetch_row($result)){
			
			$val1= strip_tags($row[1]);
			$val2= strip_tags($row[2]);
			$val3= strip_tags($row[3]);
			$val4= strip_tags($row[4]);
			
			//s_t_mod Hui
			/*$sql = "SELECT mdl_metadata.Id_metadata, mdl_metadata.Value
				FROM mdl_metadata 
				WHERE id_course_sections=" . $val3 . " AND Property='status'";
			$fields = $DB->get_records_sql($sql);

			
			foreach ($fields as $field) {
				$color = $field->value;
			}
			if($color == "green"){
				$name = "background-color: #00ff01";
			}
			elseif($color == "yellow"){
				$name = "background-color: #ffff00";
			}
			elseif($color == "red"){
				$name = "background-color: #ff0000";
			}
			elseif($color == "white"){
				$name = "background-color: #ffffff";
			}
			elseif($color == "gold"){
				$name = "background-color: #ffd700";
			}
			elseif ($color == "black") {
				$name = "background-color: #000000; color: #ffffff";
			}*/
			
			if (!ctype_space($val1) && $val1!=null && $val1!='')
				//echo "<option style=\"" .str_replace('\'', '&#39;',$name). "\" value=\"".str_replace('\'', '&#39;',$val3)."\" title='Module Description = ".str_replace('\'', '&#39;',$val2)." \nCourse Title = ".str_replace('\'', '&#39;',$val4)."'>".str_replace('\'', '&#39;',$val1)."</option>";
				echo "<option value=\"".str_replace('\'', '&#39;',$val3)."\" title='Module Description = ".str_replace('\'', '&#39;',$val2)." \nCourse Title = ".str_replace('\'', '&#39;',$val4)."'>".str_replace('\'', '&#39;',$val1)."</option>";

$i=$i+1;
}
				


?>
