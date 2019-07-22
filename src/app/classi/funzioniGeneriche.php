<?php
// FUNZIONE DI CORREZIONE CARATTERI NEI TESTI NORMALI
function correttoreCaratteri($testo) {
	$testo = str_replace( "–", "-", $testo);
	$testo = str_replace( "^", "°", $testo);
	$testo = str_replace( "’", "\'", $testo);
	$testo = str_replace( "‘", "\'", $testo);
	$testo = str_replace( "“", '\"', $testo);
	$testo = str_replace( "”", '\"', $testo);
	$testo = str_replace( "€", '&euro;', $testo);
	$testo = str_replace( "…", '...', $testo);
	return $testo;
}
function convert_smart_quotes($string) {
	$search = array('&lsquo;',
            '&rsquo;',
            '&ldquo;',
            '&rdquo;',
            '&mdash;');
	$replace = array("'",
			"'",
			'"',
			'"',
			'-'); 
    return str_replace($search, $replace, $string);
}

function correttoreCaratteriFile($testo) {
	$testo = str_replace( "–", "", $testo);
	$testo = str_replace( "^", "", $testo);
	$testo = str_replace( "’", "", $testo);
	$testo = str_replace( "“", '', $testo);
	$testo = str_replace( "”", '', $testo);
	return $testo;
}

// FUNZIONE DI CORREZIONE CARATTERI NEI TESTI NORMALI
function correttoreHtml($testo) {
	global $configurazione;

	if (!$configurazione['utilizza_tidy']) {
		$contCorElab = stripslashes($testo);
		$align = '[v,h]*align=( )*"[-;:#a-zA-Z0-9]*[^"]*"';
		$bgcolor = 'bgcolor=( )*"[-;:#a-zA-Z0-9]*[^"]*"';
		$target = 'target=( )*"[_;:#a-zA-Z0-9]*[^"]*"';
		$space = '[v,h]*space=( )*"[-;:#a-zA-Z0-9]*[^"]*"';
		$undlin1 = '<u>';
		$undlin2 = '</u>';
		$contCorElab = eregi_replace( $align, "", $contCorElab);
		$contCorElab = eregi_replace( $space, "", $contCorElab);
		$contCorElab = eregi_replace( $bgcolor, "", $contCorElab);
		$contCorElab = eregi_replace( $target, "", $contCorElab);
		$contCorElab = eregi_replace( $undlin1, "", $contCorElab);
		$contCorElab = eregi_replace( $undlin2, "", $contCorElab);
		return $contCorElab;
	}
	$contCor = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\"><html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\"><head><title>Contenuto da correggere</title></head><body>";
	$contCor .= stripslashes($testo);
	$contCor .= "</body></html>";

	//echo "CORREGGO TESTO CON TIDY: <div>".$testo."</div>";
	// configurazione delle tidy
	$configTidy = array(
			"output-xhtml" => true,
			"output-xml" => false,
			"doctype" => "strict",
			"accessibility-check"=> false,
			"show-body-only" => true,
			"lower-literals" => true,
			"literal-attributes" => true,
			"merge-spans" => false,
			"merge-divs" => false,
			"drop-proprietary-attributes" => true,
			"indent"=> false,
			"wrap" => 0,
			"drop-font-tags" => true,
			"word-2000" => true,
			"bare" => false,
			"clean" => false,
			"quiet" => true,
			"logical-emphasis" => false
	);

	// verifico quale versione di php sto utilizzando
	if (phpversion() < "5.0.0") {
		tidy_set_encoding('latin1');
		foreach ($configTidy as $key => $value) {
			tidy_setopt($key,$value);
		}
		if ($contCor != '') {
			// parso il contenuto superiore con tidy
			tidy_parse_string($contCor);
			tidy_diagnose();
			tidy_clean_repair();
			$contCorElab = tidy_get_output();
		}
	} else {
		// utilizzo tidy 2.0
		/*
		 $tidy = tidy_parse_string($contCor,$configTidy);
		 $tidy->CleanRepair();
		 echo "<hr />Correzzione tidy:<br />".tidy_get_error_buffer($tidy);
		 $tidy->diagnose();
		 echo "<hr />Correzzione tidy DOPO DIAGNOSI:<br />".tidy_get_error_buffer($tidy);
		 $contCorElab = trim(tidy_get_output($tidy));
		 */
		if($configurazione['tidy_encoding'] != '') {
			$contCorElab = tidy_parse_string(trim($contCor),$configTidy, $configurazione['tidy_encoding']);
		} else {
			$contCorElab = tidy_parse_string(trim($contCor),$configTidy);
		}
		tidy_clean_repair($contCorElab);
		$contCorElab = trim($contCorElab, " \t\n\r\0\x0B");
		//$contCorElab->CleanRepair();
		//
	}
	/*
	echo "ERRORI DI CONFIGURAZIONE: ".tidy_config_count($tidy);
	 echo "<pre>";
	 print_r(tidy_get_config($tidy));
	 echo "</pre>";
	 echo "<hr />TESTO CORRETTO CON TIDY: <div>".$contCorElab."</div>";
	 */

	 // ELIMINO ATTRIBUTI VIETATI
	$align = '[v,h]*align=( )*"[-;:#a-zA-Z0-9]*[^"]*"';
    $bgcolor = 'bgcolor=( )*"[-;:#a-zA-Z0-9]*[^"]*"';
	$target = 'target=( )*"[_;:#a-zA-Z0-9]*[^"]*"';
	$space = '[v,h]*space=( )*"[-;:#a-zA-Z0-9]*[^"]*"';
	$undlin1 = '<u>';
	$undlin2 = '</u>';
	$contCorElab = eregi_replace( $align, "", $contCorElab);
	$contCorElab = eregi_replace( $space, "", $contCorElab);
	$contCorElab = eregi_replace( $bgcolor, "", $contCorElab);
	//$contCorElab = eregi_replace( $target, "", $contCorElab);
	$contCorElab = eregi_replace( $undlin1, "", $contCorElab);
	$contCorElab = eregi_replace( $undlin2, "", $contCorElab);
	return $contCorElab;
}
?>