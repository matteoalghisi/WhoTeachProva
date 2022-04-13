<?php
function clean_phrase_input($input){
	$input_array= explode(",", $input);
	$i=0;

	while(list($chiave,$valore)=each($input_array))
			{
			$string = strtolower(htmlspecialchars($valore));
			$string = $string."*";
			$patterns[$i] = "manager*"; $replacements[$i] = "manager*, managing*, management*";
			$patterns[$i+1] = "economic*"; $replacements[$i+1] = "economic*, fiscal*, commercial*";
			$patterns[$i+2] = "IT*"; $replacements[$i+2] = "IT*, information technology*";
			$patterns[$i+3] = "excel*"; $replacements[$i+3] = "excel*, spreadsheet*, foglio elettronico*";
			$patterns[$i+4] = "access*"; $replacements[$i+4] = "db*, database*, access*, repository*";
			$patterns[$i+5] = "database*"; $replacements[$i+5] = "db*, database*, repository*";
			$patterns[$i+6] = "db*"; $replacements[$i+6] = "db*, database*, repository*";
			$patterns[$i+7] = "php*"; $replacements[$i+7] = "php*, script*, web technology*";
			$patterns[$i+8] = "script*"; $replacements[$i+8] = "php*, script*, web technology*";
			$phrase[$i] = str_replace( $patterns, $replacements, $string);
			$i=$i+9;
				}
	$comma_separated = implode(",", $phrase);

	return $comma_separated;
}
?> 