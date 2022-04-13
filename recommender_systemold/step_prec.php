<?php

	$n_rules = count($_SESSION['summary']['parts'][$_SESSION["parte_corrente"]]['steps']['rules']) - 1;
	$n_key = count($_SESSION['summary']['parts'][$_SESSION["parte_corrente"]]['steps']['key']);

	//s_t_mod Chiara : aggiunto stile a div "flip" e a div "key"
	print '
		<div id="flip" style="margin-top:3%; margin-bottom:3%; font-style:italic;">' . convert_RS('Summary previous steps') . '
			<img class="arrow-img slideUp" src="themes/img/small_down_arrow.png" />
		</div>
		<div id="hideMe" style="margin-top:-3%;" >
			<div id="rules">		
				<font color="red"><strong>'.ucwords(strtolower(convert_RS('RULES'))).':'.'</strong></font>';
				for ($i = 0; $i < $n_rules; $i++)
				{
					//print $_SESSION['summary']['parts'][$_SESSION["parte_corrente"]]['steps']['rules'][$i];	
					print "<div style='margin-top:18px; margin-left:-80px'>" . $_SESSION['summary']['parts'][$_SESSION["parte_corrente"]]['steps']['rules'][$i]."</div>"; //s_t_mod Nuovo Stile					
				}
	print '	</div>
			<br>
			<div id="key" style="margin-bottom:3%;">
				<font color="red"><strong>'.convert_RS('Keywords previously selected').':</strong></font>';
				for ($i = 0; $i < $n_key; $i++)
				{
					print "<br>" . $_SESSION['summary']['parts'][$_SESSION["parte_corrente"]]['steps']['key'][$i];					
				}
	print '	</div>
		</div>
		';
?>