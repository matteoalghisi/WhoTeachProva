<?php

	$n_rules = count($_SESSION['summary']['parts'][$_SESSION["parte_corrente"]]['steps']['rules']) - 1;
	$n_key = count($_SESSION['summary']['parts'][$_SESSION["parte_corrente"]]['steps']['key']);

	print '
		<div id="flip">' . convert_RS('Summary previous steps') . '
			<img class="arrow-img slideUp" src="themes/img/small_down_arrow.png" />
		</div>
		<div id="hideMe">
			<div id="rules">		
				<font color="red"><strong>'.ucwords(strtolower(convert_RS('RULES'))).':'.'</strong></font>';
				for ($i = 0; $i < $n_rules; $i++)
				{
					print $_SESSION['summary']['parts'][$_SESSION["parte_corrente"]]['steps']['rules'][$i];
				}
	print '	</div>
			<br>
			<div id="key">
				<font color="red"><strong>'.convert_RS('Keywords previously selected').':</strong></font>';
				for ($i = 0; $i < $n_key; $i++)
				{
					print "<br>" . $_SESSION['summary']['parts'][$_SESSION["parte_corrente"]]['steps']['key'][$i];					
				}
	print '	</div>
		</div>
		';
?>