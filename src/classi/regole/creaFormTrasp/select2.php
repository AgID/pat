<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////PAT - Portale Amministrazione Trasparente////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////versione 1.5 - //////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/*
	* Copyright 2015,2017 - AgID Agenzia per l'Italia Digitale
	*
	* Concesso in licenza a norma dell'EUPL, versione 1.1 o
	successive dell'EUPL (la "Licenza")– non appena saranno
	approvate dalla Commissione europea;
	* Non è possibile utilizzare l'opera salvo nel rispetto
	della Licenza.
	* È possibile ottenere una copia della Licenza al seguente
	indirizzo:
	*
	* https://joinup.ec.europa.eu/software/page/eupl
	*
	* Salvo diversamente indicato dalla legge applicabile o
	concordato per iscritto, il software distribuito secondo
	i termini della Licenza è distribuito "TAL QUALE",
	* SENZA GARANZIE O CONDIZIONI DI ALCUN TIPO,
	esplicite o implicite.
	* Si veda la Licenza per la lingua specifica che disciplina
	le autorizzazioni e le limitazioni secondo i termini della
	Licenza.
	*/ 
	/**
	 * @file
	 * classi/regole/creaFormTrasp/select2.php
	 * 
	 * @Descrizione
	 * Output campo form con plugin select2 per associazioni avanzate creato da creaFormTrasp()
	 *
	 */

//bugfix elementi eliminati
$valoriSelezionati = explode(",",$valoreVero);
//pulizia dei valori
$valoriSelezionatiTemp = array();
foreach((array)$valoriSelezionati as $val) {
	if($val != '' and $val != 0 and $val > 0) {
		$valoriSelezionatiTemp[] = $val;
	}
}
$valoriSelezionati = $valoriSelezionatiTemp;
$valoreVero = implode(',', $valoriSelezionati);
if($valoreVero != '') {
	switch($campoAjax)  {
		case 'referente':
			$sql = "SELECT id FROM ".$dati_db['prefisso']."oggetto_riferimenti WHERE id IN (".$valoreVero.")";
		break;
		case 'struttura':
			$sql = "SELECT id FROM ".$dati_db['prefisso']."oggetto_uffici WHERE id IN (".$valoreVero.")";
		break;
		case 'incarichimulti':
			$sql = "SELECT id FROM ".$dati_db['prefisso']."oggetto_incarichi WHERE id IN (".$valoreVero.")";
		break;
		case 'normativa':
			$sql = "SELECT id FROM ".$dati_db['prefisso']."oggetto_normativa WHERE id IN (".$valoreVero.")";
		break;
		case 'procedimenti':
			$sql = "SELECT id FROM ".$dati_db['prefisso']."oggetto_procedimenti WHERE id IN (".$valoreVero.")";
		break;
		case 'fornitore_singolo':
		case 'fornitori':
			$sql = "SELECT id FROM ".$dati_db['prefisso']."oggetto_elenco_fornitori WHERE id IN (".$valoreVero.")";
		break;
		case 'bandiconcorso':
			$sql = "SELECT id FROM ".$dati_db['prefisso']."oggetto_concorsi WHERE id IN (".$valoreVero.")";
		break;
		case 'regolamenti':
			$sql = "SELECT id FROM ".$dati_db['prefisso']."oggetto_regolamenti WHERE id IN (".$valoreVero.")";
		break;
		case 'provvedimenti':
			$sql = "SELECT id FROM ".$dati_db['prefisso']."oggetto_provvedimenti WHERE id IN (".$valoreVero.")";
		break;
		case 'modulistica':
			$sql = "SELECT id FROM ".$dati_db['prefisso']."oggetto_modulistica_regolamenti WHERE id IN (".$valoreVero.")";
		break;
	}
	if ( !($result = $database->connessioneConReturn($sql)) ) {
		die('Errore durante il recupero di tutti i referenti (con condizione)'.$sql);
	}
	$records = $database->sqlArrayAss($result);
	$valoriSelezionatiTemp = array();
	foreach ((array)$records as $val) {
		if($val['id'] != '' and $val['id'] != 0 and $val['id'] > 0) {
			$valoriSelezionatiTemp[] = $val['id'];
		}
	}
	$valoriSelezionati = $valoriSelezionatiTemp;
	$valoreVero = implode(',', $valoriSelezionati);
}

if($multiplo) {
	echo "<input type=\"hidden\" ".$disabilitatoTxt." data-placeholder=\"".$dataPlaceholder."\" value=\"".$valoreVero."\" name=\"".$nome."[]\" id=\"".$nome."\" class=\"".$disabilitatoClasse."select2Classe ".$classe."\">";
} else {
	echo "<input type=\"hidden\" ".$disabilitatoTxt." data-placeholder=\"".$dataPlaceholder."\" value=\"".$valoreVero."\" name=\"".$nome."\" id=\"".$nome."\" class=\"".$disabilitatoClasse."select2Classe ".$classe."\">";
}

if(!$box) {
	//pubblico il pulsante di aggiunta solo se non sono nella finestra modale			
	echo " &nbsp; <a class=\"aggInForm aggModale".$nome."\"><i class=\"iconfa-plus-sign\"></i> &nbsp; ".$testoAggiungi."</a>";
}

// inserisco javascript necessario
echo "

<script type=\"text/javascript\">
	jQuery(document).ready(function(){
		function format".$nome."(item".$nome.") {
			if (!item".$nome.".id) return item".$nome.".text; // optgroup
			return \"<div class=\\\"risFormAjax\\\">\"+item".$nome.".text+\"</div>\";
		}
		function format".$nome."Sel(item".$nome.") {
			return \"<div>\"+item".$nome.".text+\"</div>\";
		}
		// funzione di aggiunta
		jQuery(\".aggModale".$nome."\").click(function(){
			idReferente = jQuery(this).attr('obiettivo');
			jQuery.colorbox({
				iframe: true,
				fastIframe: false,
				preloading: false,
				width: \"78%\",
				height: \"88%\",
				title: '".$testoAggiungi."',
				href:\"".$server_url."admin_pat.php?menu=".$menuAggiunta."&menusec=".$menusecAggiunta."&azione=aggiungi&box=1\"
			});
		});						
		
		jQuery(\"#".$nome."\").select2({					
			ajax: {
				url: \"".$server_url."ajax_campi.php?campo=".$campoAjax."&condizione=".$condizione."\",
				dataType: 'json',
				quietMillis: 100,
				data: function (term, page) { 
					return {
						q: term,
						referente: jQuery('#".$campoAjax."').val()
					};
				},
				results: function (data, page) {
					return {results: data.".$campoAjax."};
				}
			},	
			initSelection: function(element, callback) {
				var id=jQuery(element).val();
				//alert('valore selezionato:'+id );
				if (id!==\"\") {
					jQuery.ajax(\"".$server_url."ajax_campi.php?campo=".$campoAjax."&id_sel=\"+id, {
						data: {
							id_sel: id
						},
						dataType: \"json\"
					}).done(function(data) { 
						callback(data); 
					});
				}							
			},
			multiple: ".($multiplo ? 'true' : 'false').",				
			formatResult: format".$nome.",
			formatSelection: format".$nome."Sel,
			formatNoMatches: function(term) {return \"Non ci sono risultati per questa ricerca\"},
			/*
			minimumInputLength: 1,
			formatInputTooShort: function () {
				return \"Inserisci almeno un carattere\";
			},
			*/
			allowClear: true,
			escapeMarkup: function(m) { return m; }
		});	
		";
		if ($multiplo) {
		echo "
			// abilito ordine via drag and drop
			jQuery(\"#".$nome."\").on(\"change\", function() { 
				jQuery(\"#".$nome."_val\").html(jQuery(\"#".$nome."\").val());
			});					 
			jQuery(\"#".$nome."\").select2(\"container\").find(\"ul.select2-choices\").sortable({
				containment: 'parent',
				start: function() { 
					jQuery(\"#".$nome."\").select2(\"onSortStart\"); 
				},
				update: function() { 
					jQuery(\"#".$nome."\").select2(\"onSortEnd\"); 
				}
			});					
		";
		}	
		echo "
	});

</script>";
?>