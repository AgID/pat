<?php

// importo la classe di amministrazione oggetti
require_once('classi/admin_oggetti.php');

if(file_exists('app/moduli/menu_amm/operazioni/oggetti/'.$menuSecondario.'.php')) {
	$opOgg = $menuSecondario;
} else {
	$opOgg = 'OperazioneDefault';
}
require('app/moduli/menu_amm/operazioni/oggetti/'.$opOgg.'.php');
$opOgg = new $opOgg();

switch ($azione) {

	//////////////////LISTA ENTI INSTALLATI///////

	case "lista" :
		
		//costruizco la query per prendere i workflow dell'utente loggato
		$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_etrasp_wf WHERE id_ente = ".$idEnteAdmin." AND " .
				"(utenti = '".$datiUser['id']."' OR utenti LIKE '".$datiUser['id'].",%' OR utenti LIKE '%,".$datiUser['id'].",%' OR utenti LIKE '%,".$datiUser['id']."') OR" .
				"(id_utenti_intermedi = '".$datiUser['id']."' OR id_utenti_intermedi LIKE '".$datiUser['id'].",%' OR id_utenti_intermedi LIKE '%,".$datiUser['id'].",%' OR id_utenti_intermedi LIKE '%,".$datiUser['id']."')" .
				" ORDER BY id_oggetto, nome";
		if( !($result = $database->connessioneConReturn($sql)) ) {
			die('Non posso caricare i workflow per l\'utente loggato '.$sql);
		}
        $wfs = $database->sqlArrayAss($result);
		$arrayWf = array();
        foreach((array)$wfs as $wf) {
        	$utenti = explode(',', $wf['utenti']);
        	$composizione = unserialize(base64_decode($wf['composizione_workflow']));
        	if(in_array($datiUser['id'], $utenti)) {
        		//utente per lo stato iniziale
        		$stato = 'iniziale';
        		$nomeStato = 'Iniziale';
        	} else {
        		//utente per lo stato intermedio
        		foreach((array)$composizione as $st) {
        			if (in_array($datiUser['id'], explode(',', $st['utenti']))) {
        				$stato = $st['id'];
        				$nomeStato = $st['nome'];
        			}
        		}
        	}
        	$menu = $oggettiTrasparenza[$wf['id_oggetto']]['menu'];
        	$menuSecondario = $oggettiTrasparenza[$wf['id_oggetto']]['menuSec'];
        	//query sulla tabella dell'oggetto coinvolto nel workflow $wf
        	$ot = new oggettiAdmin($wf['id_oggetto']);
        	$listaOggetti = $ot->visualizzaListaWorkflow($stato);
        	foreach((array)$listaOggetti as $ob) {
	        	$art = array();
	        	$art['wf'] = $wf['nome'];
	        	$art['nome_stato'] = $nomeStato;
	        	$art['id_oggetto'] = $wf['id_oggetto'];
	        	$art['id_documento'] = $ob['id'];
	        	if($art['id_oggetto'] == 33) {
	        		$art['nome_oggetto'] = 'Pagine generiche';
	        		$art['nome'] = nomeSezDaId($ob['id_sezione_etrasp']);
	        	} else {
		        	$art['nome_oggetto'] = $oggettiTrasparenza[$wf['id_oggetto']]['nomePagina'];
		        	$art['nome'] = mostraDatoOggetto($ob['id'], $wf['id_oggetto'], $ot->campo_default);
	        	}
	        	$art['istanza'] = $ob;
	        	if (!$aclTrasparenza[$menuSecondario]['modifica'] AND !$aclTrasparenza[$menuSecondario]['creazione']) {
					$art['strumenti'] = '';
					if($art['id_oggetto'] == 33 and $aclTrasparenza['contenuti'][$ob['id_sezione_etrasp']]['modifica']) {
						$art['strumenti'] = "admin__pat.php?menu=contenuti&menusec=editpagina&azione=editpagina&id=".$ob['id'];
					}
				} else {
					$art['strumenti'] = "admin__pat.php?menu=".$menu."&menusec=".$menuSecondario."&azione=modifica&id=".$ob['id'];
					if(moduloAttivo('bandigara') and $wf['id_oggetto'] == 11) {
						$tipo = mostraDatoOggetto($ob['id'], $wf['id_oggetto'], 'tipologia');
						switch($tipo) {
							//bandi ed inviti,esiti,delibere e determine a contrarre,affidamenti,avvisi pubblici,somme liquidate
							case 'bandi ed inviti':
								$art['strumenti'] .= '&tipo=bando';
							break;
							case 'lotto':
								$art['strumenti'] .= '&tipo=lotto';
							break;
							case 'esiti':
								$art['strumenti'] .= '&tipo=esito';
							break;
							case 'delibere e determine a contrarre':
								$art['strumenti'] .= '&tipo=delibera';
							break;
							case 'determina_32':
								$art['strumenti'] .= '&tipo=determina_32';
							break;
							case 'affidamenti':
								$art['strumenti'] .= '&tipo=affidamento';
							break;
							case 'avvisi pubblici':
								$art['strumenti'] .= '&tipo=avviso';
							break;
							case 'somme liquidate':
								$art['strumenti'] .= '&tipo=liquidazione';
							break;
						}
						$sottotipo = mostraDatoOggetto($ob['id'], $wf['id_oggetto'], 'sottotipo');
						if($sottotipo != '') {
							$art['strumenti'] .= '&sottotipo='.$sottotipo;
						}
					}
				}
	        	$arrayWf[] = $art;
        	}
        }
			
		include ('./app/admin_template/stato_workflow/tab_start.tmp');
		
		foreach((array)$arrayWf as $istanzaOggetto) {
			include ('./app/admin_template/stato_workflow/tab_row.tmp');
		}
		
		include ('./app/admin_template/stato_workflow/tab_end.tmp');
			
	break;
}
?>