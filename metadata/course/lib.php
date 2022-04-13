<?php		
		/*************rimozione tupla tabella mdl_duplicates****************/
		/* Vista la modifica effettuata, tolgo la sezione da mdl_duplicates*/
		/*******************************************************************/
		if (isset($section->id)){
			$sql = "UPDATE sssecm_duplicates SET flag = 1 WHERE id_sec_dest = '".$section->id."'";
			$DB->execute($sql);
		}
		/************************/
		/*		  FINE			*/
		/************************/
		?>