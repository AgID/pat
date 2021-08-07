<?php
/*
 * Created on 14/nov/2014
 */
$tree = getTree(18);
echo $restResponse->response($tree);
$restResponse->exitApp();

function  getTree($id) {
	global $database, $dati_db, $restResponse, $entePubblicato,$tipoEnte;
	
	$sql = "SELECT id,id_riferimento,nome,permessi_lettura,link FROM ".$dati_db['prefisso']."sezioni WHERE id_riferimento = ".$id." AND permessi_lettura = 'N/A' ORDER BY priorita";
	if ( !($result = $database->connessioneConReturn($sql)) ) {
		echo $restResponse->returnError('Errore in estrazione sezioni response');
		$restResponse->exitApp();
	}
	$sez = $database->sqlArrayAss($result);
	$return = array();
	foreach((array)$sez as $s) {
		$sezTemp = $s;
		$sezTemp['sezioni'] = getTree($s['id']);
		
		if ($s['permessi_lettura'] != 'H' and $s['permessi_lettura'] != 'HM' and $s['permessi_lettura'] != 'R+HM' and $s['permessi_lettura'] != 'RHM' and $s['permessi_lettura'] != 'HL') {
			$includi = 1;
			
			//CONTROLLO MODULO 'concorsi' per visualizzazione sottosezioni
			if( !moduloAttivo('concorsi') AND ($s['id']==806 OR $s['id']==807)){
				$includi=0;
			}
			
			//PERSONALIZZAZIONE Aziende sanitarie
			if($entePubblicato['tipo_ente']==17 AND $s['id']==710){
				$includi=0;
			}
			
			// analisi sezioni degli organi politici
			foreach($tipoEnte['traduzioni_organi'] as $trad) {
				if ($s['id'] == $trad['id']) {
					if ($trad['nome'] == '') {
						$includi = 0;
					} else {
						$sezTmp['nome'] = $trad['nome'];
					}
				}
			}
			/*
			if ($s['id']==702) {
				if ($tipoEnte['org_sindaco'] == '') {
					$includi = 0;
				} else {
					$sezTmp['nome'] = $tipoEnte['org_sindaco'];
				}				
			}
			if ($s['id']==703) {
				if ($tipoEnte['org_giunta'] == '') {
					$includi = 0;
				} else {
					$sezTmp['nome'] = $tipoEnte['org_giunta'];
				}				
			}							
			if ($s['id']==704) {
				if ($tipoEnte['org_presidente'] == '') {
					$includi = 0;
				} else {
					$sezTmp['nome'] = $tipoEnte['org_presidente'];
				}				
			}
			if ($s['id']==705) {
				if ($tipoEnte['org_consiglio'] == '') {
					$includi = 0;
				} else {
					$sezTmp['nome'] = $tipoEnte['org_consiglio'];
				}				
			}
			if ($s['id']==706) {
				if ($tipoEnte['org_direzione'] == '') {
					$includi = 0;
				} else {
					$sezTmp['nome'] = $tipoEnte['org_direzione'];
				}				
			}
			if ($s['id']==707) {
				if ($tipoEnte['org_segretario'] == '') {
					$includi = 0;
				} else {
					$sezTmp['nome'] = $tipoEnte['org_segretario'];
				}				
			}	
			if ($s['id']==708) {
				if ($tipoEnte['org_commissioni'] == '') {
					$includi = 0;
				} else {
					$sezTmp['nome'] = $tipoEnte['org_commissioni'];
				}				
			}
			if ($s['id']==792) {
				if ($tipoEnte['org_vicesindaco'] == '') {
					$includi = 0;
				} else {
					$sezTmp['nome'] = $tipoEnte['org_vicesindaco'];
				}				
			}
			if ($s['id']==793) {
				if ($tipoEnte['org_gruppi_consiliari'] == '') {
					$includi = 0;
				} else {
					$sezTmp['nome'] = $tipoEnte['org_gruppi_consiliari'];
				}				
			}
			if ($s['id']==796) {
				if ($tipoEnte['org_commissario'] == '') {
					$includi = 0;
				} else {
					$sezTmp['nome'] = $tipoEnte['org_commissario'];
				}				
			}
			if ($s['id']==809) {
				if ($tipoEnte['org_ass_sindaci'] == '') {
					$includi = 0;
				} else {
					$sezTmp['nome'] = $tipoEnte['org_ass_sindaci'];
				}				
			}
			if ($s['id']==810) {
				if ($tipoEnte['org_sub_commissario'] == '') {
					$includi = 0;
				} else {
					$sezTmp['nome'] = $tipoEnte['org_sub_commissario'];
				}				
			}
			if ($s['id']==827) {
				if ($tipoEnte['org_comitato_esecutivo'] == '') {
					$includi = 0;
				} else {
					$sezTmp['nome'] = $tipoEnte['org_comitato_esecutivo'];
				}				
			}
			if ($s['id']==828) {
				if ($tipoEnte['org_consiglio_sportivo_nazionale'] == '') {
					$includi = 0;
				} else {
					$sezTmp['nome'] = $tipoEnte['org_consiglio_sportivo_nazionale'];
				}				
			}
			if ($s['id']==829) {
				if ($tipoEnte['org_giunta_sportiva'] == '') {
					$includi = 0;
				} else {
					$sezTmp['nome'] = $tipoEnte['org_giunta_sportiva'];
				}				
			}
			*/
			
			/////////// verifico se la sezione, appartiene all'elenco di sezioni da escludere
			$sezioniEscludo = explode(",",$tipoEnte['sezioni_esclusione']);				
			if (in_array($s['id'], $sezioniEscludo)) {
				$includi = 0;
			}
			
			if($includi) {
				$return[] = $sezTemp;
			}
		}
	}
	if(count($return) > 0) {
		return $return;
	} else {
		return null;
	}
}
?>