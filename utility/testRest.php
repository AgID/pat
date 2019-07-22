<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////PAT - Portale Amministrazione Trasparente////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////versione 1.8 - //////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/*
	* Copyright 2015 - AgID Agenzia per l'Italia Digitale
	*
	* Concesso in licenza a norma dell'EUPL(la "Licenza"), versione 1.2;
	
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
 * testRest.php
 * 
 * @Descrizione
 * File di test per interoperabilità PAT con servizio REST
 *
 */
 
$configurazione['trasparenza_id_ente'] = 0; // editare identificativo ente di cui esportare i dati
$idOggettoTrasparenza = 0; // editare identificativo dell'oggetto su cui interagire
$id = 0; // editare identificativo istanze di test


echo "http://nomedominio/restful/get.xml/".md5('isweb'.$configurazione['trasparenza_id_ente'].$idOggettoTrasparenza.$id)."/".$configurazione['trasparenza_id_ente']."/".$idOggettoTrasparenza."/".$id;

echo "<br /><br />";

$idOggetti = "11-22-29-4-5-16-3-19-13-27-28-30-33";
$numero = 10;
$q = "disposizioni";
$output = '';

$call = "http://nomedominio/restful/searchOggetti.xml/".md5('isweb'.$configurazione['trasparenza_id_ente'].$idOggetti.$numero)."/".$configurazione['trasparenza_id_ente']."/".$idOggetti."/".$numero."/".$q;
echo $call;

$response = file_get_contents($call);
try {
	$response = new SimpleXMLElement($response);
	$oggetti = $response->oggetti;
	foreach($oggetti->children() as $o) {
		$output .= "pagina".$o->idSezioneElenco."_elenco.html|<div class=\"".$classeTitolo."\">".$o->nome."</div>|t\n";
		$istanze = $o->istanze;
		foreach($istanze->children() as $ist) {
			$output .= $ist->link."|".$ist->titolo."|e\n";
		}
	}
} catch (Exception $e) {
	echo "ERRORE";
}
echo $output;



function lognascosto($s, $par = array(), $nascondi = true) {
	global $datiUser;
	if($datiUser['permessi'] != '10' and $nascondi) {
		$style = "display:none;";
	}
	if(!$nascondi){
		$nascondi = 'visibile';
	}
	echo "<pre style='$style'><br/>($nascondi) Log: ";
	echo $s."<br/>";
	if(count($par) > 0) {
		print_r($par);
	}
	echo "</pre>";
}

function lognormale($s,$arr = array()){
	lognascosto($s,$arr,false);
}
?>