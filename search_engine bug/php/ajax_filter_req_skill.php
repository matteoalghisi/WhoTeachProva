<?php

if ($_SERVER['REQUEST_METHOD']=="POST") {
$HTTP_STR=$_POST;
}else{
$HTTP_STR=$_GET;
}
$course=$HTTP_STR["course"];
//var_dump($HTTP_STR);
/*
$entrepreneurial=array('Proactivity','Entrepreneurial behaviors and attitudes','Leadership','Self-evaluation','Self-organization','Innovative thinking','Creative thinking','Opportunities Management','Ability to promote initiatives','Management Skills','Risk Management');
$personal=array('Interpersonal Relations','Conflict Management','Team working','Career Planning','Job Search Skills','People Management','Training and Professional Development','Motivation','People and Performance Evaluation Skills','Responsibility');
$communication=array('Communications Basics','Communication Ethics','Information Management','Data Management','Information Technology Basics','Product and Service Marketing','Marketing Information Management','Strategic Marketing  Planning');
$economic=array('Business Basics','Business Attitudes','Decision Making','Economic Culture','Financial Basics','Treasury Management','Accounting','Enterprise Modeling','Distribution Channels Management','Purchasing Management','Operations Management');
$technical=array('Computer Skills','IT Basics','IT Applications Basics','Electronic System Tools Basics','Painting SW','Calculation SW','Project Management SW','Document Management SW','Planning and Control SW','Simulation SW','Accounting SW','Communication SW');
*/
require './mysql_conn.php';
$i=0;
$result = mysqli_query(GetMyConnection(), "SELECT Property,value FROM mdl_metadata where Id_course<>".$course."and Property in ('s_req_skill', 'd_req_skill', 's_acq_skill', 'd_acq_skill')");
$stringa_where='';
			
while ($row = mysqli_fetch_row($result)){
		$stringa_where=$stringa_where."(mdl_metadata.Property='".$row[0]."' and mdl_metadata.Value='".$row[1]."') or ";
		if (!$array_metadati[$row[0]])
				{
			$array_metadati[$row[0]]= array(0 => $row[1]);
			}
		else
			{
			$array_metadati[$row[0]][]= $row[1];
			}
		$i=$i+1;
	}
			
$array_category= explode(",", $HTTP_STR['category']);
//print_r($array_category);


while(list($chiave,$valore)=each($array_category))
	{
	if ($valore=='Entrepreneurial Vision') {
				$result = mysqli_query(GetMyConnection(), "SELECT property_value FROM mdl_metadata_descr where category='Entrepreneurial Vision' and (property_name='d_req_skill' or property_name='s_req_skill')");

							while ($row = mysqli_fetch_row($result)){
							if (in_array($row[0], $array_metadati['d_req_skill']) or in_array($row[0], $array_metadati['s_req_skill'])) {
							echo '<option value="'.$row[0].'" selected="selected">'.$row[0].'</option>';
							}
							else 
								echo '<option value="'.$row[0].'">'.$row[0].'</option>';
								
							}
				}
		if ($valore=='Personal Development') {
				$result = mysqli_query(GetMyConnection(), "SELECT property_value FROM mdl_metadata_descr where category='Personal Development' and (property_name='d_req_skill' or property_name='s_req_skill')");
							while ($row = mysqli_fetch_row($result)){
							if (in_array($row[0], $array_metadati['d_req_skill']) or in_array($row[0], $array_metadati['s_req_skill'])) {
							echo '<option value="'.$row[0].'" selected="selected">'.$row[0].'</option>';
							}
							else 
								echo '<option value="'.$row[0].'">'.$row[0].'</option>';
								
							}				
		}
		if ($valore=='Communication Skills') {
				$result = mysqli_query(GetMyConnection(), "SELECT property_value FROM mdl_metadata_descr where category='Communication Skills' and (property_name='d_req_skill' or property_name='s_req_skill')");
							while ($row = mysqli_fetch_row($result)){
							if (in_array($row[0], $array_metadati['d_req_skill']) or in_array($row[0], $array_metadati['s_req_skill'])) {
							echo '<option value="'.$row[0].'" selected="selected">'.$row[0].'</option>';
							}
							else 
								echo '<option value="'.$row[0].'">'.$row[0].'</option>';
								
							}
		}
		if ($valore=='Economic Skills') {
				$result = mysqli_query(GetMyConnection(), "SELECT property_value FROM mdl_metadata_descr where category='Economic Skills' and (property_name='d_req_skill' or property_name='s_req_skill')");
							while ($row = mysqli_fetch_row($result)){
							if (in_array($row[0], $array_metadati['d_req_skill']) or in_array($row[0], $array_metadati['s_req_skill'])) {
							echo '<option value="'.$row[0].'" selected="selected">'.$row[0].'</option>';
							}
							else 
								echo '<option value="'.$row[0].'">'.$row[0].'</option>';
								
							}
		}
		if ($valore=='Technical Skills') {
				$result = mysqli_query(GetMyConnection(), "SELECT property_value FROM mdl_metadata_descr where category='Technical Skills' and (property_name='d_req_skill' or property_name='s_req_skill')" );
							while ($row = mysqli_fetch_row($result)){
							if (in_array($row[0], $array_metadati['d_req_skill']) or in_array($row[0], $array_metadati['s_req_skill'])) {
							echo '<option value="'.$row[0].'" selected="selected">'.$row[0].'</option>';
							}
							else 
								echo '<option value="'.$row[0].'">'.$row[0].'</option>';
								
							}
		}
		if ($valore=='Abilità Informatiche') {
						$result = mysqli_query(GetMyConnection(), "SELECT property_value FROM mdl_metadata_descr where category='Abilità Informatiche' and (property_name='d_req_skill' or property_name='s_req_skill')" );
									while ($row = mysqli_fetch_row($result)){
									//if (in_array($row[0], $array_metadati['d_acq_skill']) or in_array($row[0], $array_metadati['s_acq_skill'])) {
									if (in_array($row[0], $array_metadati['d_req_skill']) or in_array($row[0], $array_metadati['s_req_skill'])) {
									echo '<option value="'.$row[0].'" selected="selected">'.$row[0].'</option>';
									}
									else 
										echo '<option value="'.$row[0].'">'.$row[0].'</option>';
										
									}
		}		
	}
				

?>