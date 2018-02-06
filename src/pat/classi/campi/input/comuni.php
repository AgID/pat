<?
include ('inc/variabili.php');
if ($accessibile) {
	if (!$valore) {
		///////// regioni
		echo "<select " . $classeStr . " id=\"" . $nome . "\" name=\"" . $nome . "\">";
		foreach ((array)$arrayJsRegioni as $regione) {
			$stringa = '';
			if ($regione[0] == $valoreVero) {
				$stringa = ' selected="selected"';
			}
			if ($regione[0] != '') {
				echo "<option value=\"" . $regione[0] . "\"" . $stringa . ">" . $regione[1] . "</option>";
			}
		}
		echo "</select>";
	} else
		if ($valore == 1) {
			///////// province
			echo "<select " . $classeStr . " id=\"" . $nome . "\" name=\"" . $nome . "\">";
			foreach ($arrayJsProvince as $provincia) {
				$stringa = '';
				if ($provincia[0] == $valoreVero) {
					$stringa = ' selected="selected"';
				}
				echo "<option value=\"" . $provincia[0] . "\"" . $stringa . ">" . $provincia[1] . "</option>";
			}
			echo "</select>";
		} else {
			///////// comuni
			echo "<select " . $classeStr . " id=\"" . $nome . "\" name=\"" . $nome . "\">";
			foreach ($arrayJsComuni as $comuni) {
				$stringa = '';
				if ($comuni[0] == $valoreVero) {
					$stringa = ' selected="selected"';
				}
				echo "<option value=\"" . $comuni[0] . "\"" . $stringa . ">" . $comuni[1] . "</option>";
			}
			echo "</select>";
		}
	return;
}
// verifico quale interfaccia pubblicare (regioni/province/comuni)
if (!$valore) {
	///////// SOLO LISTA REGIONI, NON SERVE JS
	echo "<select " . $classeStr . " id=\"" . $nome . "\" name=\"" . $nome . "\">";
	foreach ($arrayJsRegioni as $regione) {
		$stringa = '';
		if ($regione[0] == $valoreVero) {
			$stringa = ' selected="selected"';
		}
		echo "<option value=\"" . $regione[0] . "\"" . $stringa . ">" . $regione[1] . "</option>";
	}
	echo "</select>";
} else {
	if ($valore == 1) {
		///////// LISTA PROVINCE
		// verifico se c'e' un valore 
		if ($valoreVero != '') {
			// trovo la regione di questa provincia
			$valoreRegione = '';
			foreach ($arrayJsProvince as $provincia) {
				if ($provincia[0] == $valoreVero) {
					$valoreRegione = $provincia[2];
				}
			}
		}
		$listaProvJs = '';
		foreach ($arrayJsProvince as $provincia) {
			if ($listaProvJs != '') {
				$listaProvJs .= ', ';
			}
			$listaProvJs .= "[\"" . $provincia[0] . "\",\"" . $provincia[1] . "\",\"" . $provincia[2] . "\"]";
		}
		$listaProvJs = '[' . $listaProvJs . ']';
		// codice Js per lista province
		echo "<script type=\"text/javascript\">
					var province" . $nome . " = " . $listaProvJs . ";
					function DropDownList_" . $nome . "regioni_onchange(v) {
					    //chiave regione
					    var key = v.value;
					    //cancello tutti le province
					    document.getElementById(\"" . $nome . "\").options.length = 1;
					    //creo lista provincia
					    for(var i in province" . $nome . ") {
					        if(province" . $nome . "[i][2] == key) {
					            lista_push(document.getElementById(\"" . $nome . "\"), province" . $nome . "[i][1], province" . $nome . "[i][0]);
					        }
					    }
					    document.getElementById(\"" . $nome . "\").style.display = 'inline';
					}
				</script>";
		echo "<select " . $classeStr . " id=\"" . $nome . "_LISTAREGIONI\" name=\"" . $nome . "_LISTAREGIONI\"  onchange=\"return DropDownList_" . $nome . "regioni_onchange(this)\" style=\"display:inline !important;margin:0px 4px 0px 0px;\">";
		foreach ($arrayJsRegioni as $regione) {
			$stringa = '';
			if ($regione[0] == $valoreRegione) {
				$stringa = ' selected="selected"';
			}
			echo "<option value=\"" . $regione[0] . "\"" . $stringa . ">" . $regione[1] . "</option>";
		}
		echo "</select>";
		//echo "valorevero: ".$valoreVero;
		echo "<select " . $classeStr . " id=\"" . $nome . "\" name=\"" . $nome . "\" style=\"display:none;\"><option value=\"\">seleziona una provincia</option>";
		if ($valoreVero != '') {
			foreach ($arrayJsProvince as $provincia) {
				if ($provincia[2] == $valoreRegione) {
					$stringa = '';
					if ($provincia[0] == $valoreVero) {
						$stringa = ' selected="selected"';
					}
					echo "<option value=\"" . $provincia[0] . "\"" . $stringa . ">" . $provincia[1] . "</option>";
				}
			}
		}
		echo "</select>";
		if ($valoreVero != '') {
			echo "<script type=\"text/javascript\">document.getElementById('" . $nome . "').style.display='inline';</script>";
		}
	} else {
		///////// LISTA COMUNI (COMPLETA)
		// verifico se c'e' un valore 
		if ($valoreVero != '') {
			// trovo la provincia di questo comune
			$valoreProvincia = '';
			foreach ($arrayJsComuni as $comuni) {
				if ($comuni[0] == $valoreVero) {
					$valoreProvincia = $comuni[2];
				}
			}
			// trovo la regione di questa provincia
			$valoreRegione = '';
			foreach ($arrayJsProvince as $provincia) {
				if ($provincia[0] == $valoreProvincia) {
					$valoreRegione = $provincia[2];
				}
			}
		}
		$listaProvJs = '';
		foreach ($arrayJsProvince as $provincia) {
			if ($listaProvJs != '') {
				$listaProvJs .= ', ';
			}
			$listaProvJs .= "[\"" . $provincia[0] . "\",\"" . $provincia[1] . "\",\"" . $provincia[2] . "\"]";
		}
		$listaProvJs = '[' . $listaProvJs . ']';
		$listaComJs = '';
		foreach ($arrayJsComuni as $comuni) {
			if ($listaComJs != '') {
				$listaComJs .= ', ';
			}
			$listaComJs .= "[\"" . $comuni[0] . "\",\"" . $comuni[1] . "\",\"" . $comuni[2] . "\"]";
		}
		$listaComJs = '[' . $listaComJs . ']';
		// codice Js per lista comuni
		echo "<script type=\"text/javascript\">
					var province" . $nome . " = " . $listaProvJs . ";
					var comuni" . $nome . " = " . $listaComJs . ";
					function DropDownList_" . $nome . "regioni_onchange(v) {
					    //chiave regione
					    var key = v.value;
					    //cancello tutti le province
					    document.getElementById(\"" . $nome . "_LISTAPROVINCE\").options.length = 1;
					    //cancello tutti i comuni
					    document.getElementById(\"" . $nome . "\").options.length = 1;
					    //creo lista provincia
					    for(var i in province" . $nome . ") {
					        if(province" . $nome . "[i][2] == key) {
					            lista_push(document.getElementById(\"" . $nome . "_LISTAPROVINCE\"), province" . $nome . "[i][1], province" . $nome . "[i][0]);
					        }
					    }
					    document.getElementById(\"" . $nome . "_LISTAPROVINCE\").style.display = 'inline';
					}
					function DropDownList_" . $nome . "province_onchange(v) {
					    //chiave provincia
					    var key = v.value;
					    //cancello tutti i comuni
					    document.getElementById(\"" . $nome . "\").options.length = 1;
					    //creo lista comuni
					    for(var i in comuni" . $nome . ") {
					        if(comuni" . $nome . "[i][2] == key) {
					            lista_push(document.getElementById(\"" . $nome . "\"), comuni" . $nome . "[i][1], comuni" . $nome . "[i][0]);
					        }
					    }
					    document.getElementById(\"" . $nome . "\").style.display = 'inline';
					}
				</script>";
		// select regioni
		echo "<select " . $classeStr . " id=\"" . $nome . "_LISTAREGIONI\" name=\"" . $nome . "_LISTAREGIONI\" onchange=\"return DropDownList_" . $nome . "regioni_onchange(this)\" style=\"display:inline !important;margin:0px 4px 0px 0px;\">";
		foreach ($arrayJsRegioni as $regione) {
			$stringa = '';
			if ($regione[0] == $valoreRegione) {
				$stringa = ' selected="selected"';
			}
			echo "<option value=\"" . $regione[0] . "\"" . $stringa . ">" . $regione[1] . "</option>";
		}
		echo "</select>";
		// select province
		echo "<select " . $classeStr . " id=\"" . $nome . "_LISTAPROVINCE\" name=\"" . $nome . "_LISTAPROVINCE\" onchange=\"return DropDownList_" . $nome . "province_onchange(this)\" style=\"display:none;margin:0px 4px 0px 0px;\"><option value=\"\">seleziona una provincia</option>";
		if ($valoreVero != '') {
			foreach ($arrayJsProvince as $provincia) {
				if ($provincia[2] == $valoreRegione) {
					$stringa = '';
					if ($provincia[0] == $valoreProvincia) {
						$stringa = ' selected="selected"';
					}
					echo "<option value=\"" . $provincia[0] . "\"" . $stringa . ">" . $provincia[1] . "</option>";
				}
			}
		}
		echo "</select>";
		//select comuni
		echo "<select " . $classeStr . " id=\"" . $nome . "\" name=\"" . $nome . "\" style=\"display:none;\"><option value=\"\">seleziona un comune</option>";
		if ($valoreVero != '') {
			foreach ($arrayJsComuni as $comuni) {
				if ($comuni[2] == $valoreProvincia) {
					$stringa = '';
					if ($comuni[0] == $valoreVero) {
						$stringa = ' selected="selected"';
					}
					echo "<option value=\"" . $comuni[0] . "\"" . $stringa . ">" . $comuni[1] . "</option>";
				}
			}
		}
		echo "</select>";
		if ($valoreVero != '') {
			echo "<script type=\"text/javascript\">document.getElementById('" . $nome . "_LISTAPROVINCE').style.display='inline';document.getElementById('" . $nome . "').style.display='inline';</script>";
		}
	}
}
?>
