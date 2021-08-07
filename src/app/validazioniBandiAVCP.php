<?
include_once('app/funzioniAVCP.php');

$visualizzaAlert = false;
$visualizzaAlertErroreAvviso = false;
$testoTooltip = '';
$numErrori = 0;

if(moduloAttivo('bandigara')) {
    if($istanzaOggetto['tipologia'] == 'bandi ed inviti') {
        //bandi ed inviti
        if(strlen($istanzaOggetto['oggetto']) > 250) {
            $visualizzaAlert = true;
            $testoTooltip .= 'Oggetto (lunghezza maggiore di 250 caratteri), ';
            $numErrori++;
        }
        if(!validaCIG($istanzaOggetto['cig'])) {
            $visualizzaAlert = true;
            $testoTooltip .= 'CIG, ';
            $numErrori++;
        }
        if(!validaCfPi($istanzaOggetto['dati_aggiudicatrice']) OR trim($istanzaOggetto['dati_aggiudicatrice']) == '') {
            $visualizzaAlert = true;
            $testoTooltip .= 'codice fiscale dell\'amministrazione aggiudicatrice, ';
            $numErrori++;
        }
        if(trim($istanzaOggetto['denominazione_aggiudicatrice']) == '') {
            $visualizzaAlert = true;
            $testoTooltip .= 'denominazione dell\'amministrazione aggiudicatrice, ';
            $numErrori++;
        }
        if(!validaSceltaContraente($istanzaOggetto['scelta_contraente'], $istanzaOggetto['anac_anno'], $istanzaOggetto['ultima_modifica'])) {
            $visualizzaAlert = true;
            $testoTooltip .= 'procedura di scelta del contraente, ';
            $numErrori++;
        }
        if($istanzaOggetto['anac_anno'] <= 0) {
            $visualizzaAlert = true;
            $testoTooltip .= 'ANAC - anno di riferimento, ';
            $numErrori++;
        }
    } else if($istanzaOggetto['tipologia'] == 'lotto') {
        //lotti
        if(strlen($istanzaOggetto['oggetto']) > 250) {
            $visualizzaAlert = true;
            $testoTooltip .= 'Oggetto (lunghezza maggiore di 250 caratteri), ';
            $numErrori++;
        }
        if(!validaCIG($istanzaOggetto['cig'])) {
            $visualizzaAlert = true;
            $testoTooltip .= 'CIG, ';
            $numErrori++;
        }
    } else if($istanzaOggetto['tipologia'] == 'delibere e determine a contrarre') {
        //delibere e determine a contrarre
        if(strlen($istanzaOggetto['oggetto']) > 250) {
            $visualizzaAlert = true;
            $testoTooltip .= 'Oggetto (lunghezza maggiore di 250 caratteri), ';
            $numErrori++;
        }
        if(!validaCIG($istanzaOggetto['cig'])) {
            $visualizzaAlert = true;
            $testoTooltip .= 'CIG, ';
            $numErrori++;
        }
        if(!validaImporto($istanzaOggetto['valore_importo_aggiudicazione'])) {
            $visualizzaAlert = true;
            $testoTooltip .= 'valore importo di aggiudicazione, ';
            $numErrori++;
        }
        if(!validaCfPi($istanzaOggetto['dati_aggiudicatrice']) OR trim($istanzaOggetto['dati_aggiudicatrice']) == '') {
            $visualizzaAlert = true;
            $testoTooltip .= 'codice fiscale dell\'amministrazione aggiudicatrice, ';
            $numErrori++;
        }
        if(trim($istanzaOggetto['denominazione_aggiudicatrice']) == '') {
            $visualizzaAlert = true;
            $testoTooltip .= 'denominazione dell\'amministrazione aggiudicatrice, ';
            $numErrori++;
        }
        if(!validaSceltaContraente($istanzaOggetto['scelta_contraente'], $istanzaOggetto['anac_anno'], $istanzaOggetto['ultima_modifica'])) {
            $visualizzaAlert = true;
            $testoTooltip .= 'procedura di scelta del contraente, ';
            $numErrori++;
        }
        if($istanzaOggetto['anac_anno'] <= 0) {
            $visualizzaAlert = true;
            $testoTooltip .= 'ANAC - anno di riferimento, ';
            $numErrori++;
        }
    } else if($istanzaOggetto['tipologia'] == 'affidamenti') {
        //affidamenti
        if(strlen($istanzaOggetto['oggetto']) > 250) {
            $visualizzaAlert = true;
            $testoTooltip .= 'Oggetto (lunghezza maggiore di 250 caratteri), ';
            $numErrori++;
        }
        if(!validaCIG($istanzaOggetto['cig'])) {
            $visualizzaAlert = true;
            $testoTooltip .= 'CIG, ';
            $numErrori++;
        }
        if(!validaImporto($istanzaOggetto['valore_importo_aggiudicazione'])) {
            $visualizzaAlert = true;
            $testoTooltip .= 'valore importo di aggiudicazione, ';
            $numErrori++;
        }
        if(!validaImporto($istanzaOggetto['importo_liquidato'])) {
            $visualizzaAlert = true;
            $testoTooltip .= 'valore importo liquidato, ';
            $numErrori++;
        }
        if(!validaSceltaContraente($istanzaOggetto['scelta_contraente'], $istanzaOggetto['anac_anno'], $istanzaOggetto['ultima_modifica'])) {
            $visualizzaAlert = true;
            $testoTooltip .= 'procedura di scelta del contraente, ';
            $numErrori++;
        }
        if(!validaCfPi($istanzaOggetto['dati_aggiudicatrice']) OR trim($istanzaOggetto['dati_aggiudicatrice']) == '') {
            $visualizzaAlert = true;
            $testoTooltip .= 'codice fiscale dell\'amministrazione aggiudicatrice, ';
            $numErrori++;
        }
        if(trim($istanzaOggetto['denominazione_aggiudicatrice']) == '') {
            $visualizzaAlert = true;
            $testoTooltip .= 'denominazione dell\'amministrazione aggiudicatrice, ';
            $numErrori++;
        }
        if(!validaData(date('Y-m-d', $istanzaOggetto['data_inizio_lavori']))) {
            $visualizzaAlert = true;
            $testoTooltip .= 'Data di effettivo inizio dei lavori, servizi o forniture, ';
            $numErrori++;
        }
        if(!validaData(date('Y-m-d', $istanzaOggetto['data_lavori_fine']))) {
            $visualizzaAlert = true;
            $testoTooltip .= 'data di ultimazione dei lavori, servizi o forniture, ';
            $numErrori++;
        }
        if($istanzaOggetto['anac_anno'] <= 0) {
            $visualizzaAlert = true;
            $testoTooltip .= 'ANAC - anno di riferimento, ';
            $numErrori++;
        }
    } else if($istanzaOggetto['tipologia'] == 'esiti') {
        //esiti
        if(strlen($istanzaOggetto['oggetto']) > 250) {
            $visualizzaAlert = true;
            $testoTooltip .= 'Oggetto (lunghezza maggiore di 250 caratteri), ';
            $numErrori++;
        }
        if(!validaCfPi($istanzaOggetto['dati_aggiudicatrice']) OR trim($istanzaOggetto['dati_aggiudicatrice']) == '') {
            $visualizzaAlert = true;
            $testoTooltip .= 'codice fiscale dell\'amministrazione aggiudicatrice, ';
            $numErrori++;
        }
        if(trim($istanzaOggetto['denominazione_aggiudicatrice']) == '') {
            $visualizzaAlert = true;
            $testoTooltip .= 'denominazione dell\'amministrazione aggiudicatrice, ';
            $numErrori++;
        }
        if(!validaSceltaContraente($istanzaOggetto['scelta_contraente'], $istanzaOggetto['anac_anno'], $istanzaOggetto['ultima_modifica'])) {
            $visualizzaAlert = true;
            $testoTooltip .= 'procedura di scelta del contraente, ';
            $numErrori++;
        }
        if(!validaImporto($istanzaOggetto['importo_liquidato'])) {
            $visualizzaAlert = true;
            $testoTooltip .= 'valore importo liquidato, ';
            $numErrori++;
        }
        if(!validaData(date('Y-m-d', $istanzaOggetto['data_inizio_lavori']))) {
            $visualizzaAlert = true;
            $testoTooltip .= 'Data di effettivo inizio dei lavori, servizi o forniture, ';
            $numErrori++;
        }
        if(!validaData(date('Y-m-d', $istanzaOggetto['data_lavori_fine']))) {
            $visualizzaAlert = true;
            $testoTooltip .= 'data di ultimazione dei lavori, servizi o forniture, ';
            $numErrori++;
        }
        if($istanzaOggetto['anac_anno'] <= 0) {
            $visualizzaAlert = true;
            $testoTooltip .= 'ANAC - anno di riferimento, ';
            $numErrori++;
        }
    } else if($istanzaOggetto['tipologia'] == 'somme liquidate') {
        if(strlen($istanzaOggetto['oggetto']) > 250) {
            $visualizzaAlert = true;
            $testoTooltip .= 'Oggetto (lunghezza maggiore di 250 caratteri), ';
            $numErrori++;
        }
        /*
         if(!validaData(date('Y-m-d', $istanzaOggetto['data_attivazione'])) or $istanzaOggetto['data_attivazione'] == '') {
         $visualizzaAlert = true;
         $testoTooltip .= 'data di pubblicazione, ';
         $numErrori++;
         }
         */
        if($istanzaOggetto['anac_anno'] <= 0) {
            $visualizzaAlert = true;
            $testoTooltip .= 'ANAC - anno di riferimento, ';
            $numErrori++;
        }
        if(trim($istanzaOggetto['bando_collegato']) == '' or $istanzaOggetto['bando_collegato'] <= 0) {
            $visualizzaAlert = true;
            $testoTooltip .= 'procedura relativa, ';
            $numErrori++;
        }
        if(!validaImporto($istanzaOggetto['importo_liquidato'])) {
            $visualizzaAlert = true;
            $testoTooltip .= 'valore importo liquidato, ';
            $numErrori++;
        }
    }
} else {
    
    if($istanzaOggetto['tipologia'] == 'bandi ed inviti' or $istanzaOggetto['tipologia'] == 'esiti'
        or $istanzaOggetto['tipologia'] == 'delibere e determine a contrarre' or $istanzaOggetto['tipologia'] == 'affidamenti') {
            
            if(strlen($istanzaOggetto['oggetto']) > 250) {
                $visualizzaAlert = true;
                $testoTooltip .= 'Oggetto (lunghezza maggiore di 250 caratteri), ';
                $numErrori++;
            }
            if(!validaCIG($istanzaOggetto['cig'])) {
                $visualizzaAlert = true;
                $testoTooltip .= 'CIG, ';
                $numErrori++;
            }
            if(!validaCfPi($istanzaOggetto['dati_aggiudicatrice']) OR trim($istanzaOggetto['dati_aggiudicatrice']) == '') {
                $visualizzaAlert = true;
                $testoTooltip .= 'codice fiscale dell\'amministrazione aggiudicatrice, ';
                $numErrori++;
            }
            if(trim($istanzaOggetto['denominazione_aggiudicatrice']) == '') {
                $visualizzaAlert = true;
                $testoTooltip .= 'denominazione dell\'amministrazione aggiudicatrice, ';
                $numErrori++;
            }
            if(!validaSceltaContraente($istanzaOggetto['scelta_contraente'], $istanzaOggetto['anac_anno'], $istanzaOggetto['ultima_modifica'])) {
                $visualizzaAlert = true;
                $testoTooltip .= 'procedura di scelta del contraente, ';
                $numErrori++;
            }
            
            if(!validaData(date('Y-m-d', $istanzaOggetto['data_inizio_lavori']))) {
                $visualizzaAlert = true;
                $testoTooltip .= 'Data di effettivo inizio dei lavori, servizi o forniture, ';
                $numErrori++;
            }
            if(!validaData(date('Y-m-d', $istanzaOggetto['data_lavori_fine']))) {
                $visualizzaAlert = true;
                $testoTooltip .= 'data di ultimazione dei lavori, servizi o forniture, ';
                $numErrori++;
            }
            
            if(!validaImporto($istanzaOggetto['valore_importo_aggiudicazione'])) {
                $visualizzaAlert = true;
                $testoTooltip .= 'valore importo di aggiudicazione, ';
                $numErrori++;
            }
            if(!validaImporto($istanzaOggetto['importo_liquidato'])) {
                $visualizzaAlert = true;
                $testoTooltip .= 'valore importo liquidato, ';
                $numErrori++;
            }
            if($istanzaOggetto['anac_anno'] <= 0) {
                $visualizzaAlert = true;
                $testoTooltip .= 'ANAC - anno di riferimento, ';
                $numErrori++;
            }
            
        } else if($istanzaOggetto['tipologia'] == 'somme liquidate') {
            if(strlen($istanzaOggetto['oggetto']) > 250) {
                $visualizzaAlert = true;
                $testoTooltip .= 'Oggetto (lunghezza maggiore di 250 caratteri), ';
                $numErrori++;
            }
            /*
             if(!validaData(date('Y-m-d', $istanzaOggetto['data_attivazione'])) or $istanzaOggetto['data_attivazione'] == '') {
             $visualizzaAlert = true;
             $testoTooltip .= 'data di pubblicazione, ';
             $numErrori++;
             }
             */
            if($istanzaOggetto['bando_collegato'] <= 0) {
                $visualizzaAlert = true;
                $testoTooltip .= 'procedura relativa, ';
                $numErrori++;
            }
        }
        
        if($istanzaOggetto['bando_collegato'] > 0) {
            //verificare se la procedura attuale è collegata ad un avviso
            if(mostraDatoOggetto($istanzaOggetto['bando_collegato'], 11, 'tipologia') == 'avvisi pubblici') {
                $visualizzaAlertErroreAvviso = true;
            }
        }
        
}

if($visualizzaAlert) {
    $testoTooltip = 'I dati non validi e/o mancanti ai fini della comunicazione ANAC sono: '.substr($testoTooltip, 0, strlen($testoTooltip)-2);
    $strumentiSelezione .= " <span class=\"intTooltip\"><a href=\"#\" data-placement=\"top\" data-rel=\"tooltip\" data-original-title=\"".$testoTooltip."\" class=\"btn\"><span class=\"iconfa-warning-sign\" style=\"color: #D50000; font-size: 80%\"></span></a></span>";
    
    $sql = "UPDATE ".$dati_db['prefisso']."oggetto_gare_atti SET __errori_anac = '1' WHERE id_ente = ".$istanzaOggetto['id_ente']." and id = ".$istanzaOggetto['id'].";";
    if ( !($risultato = $database->connessioneConReturn($sql)) ) {
        echo 'ERRORE: Update non eseguito:';
    }
}
if($visualizzaAlertErroreAvviso) {
    $testoTooltip = 'Il presente elemento non verr&agrave; comunicato all\'ANAC perch&egrave; erroneamente associato ad un avviso pubblico. Modificare l\'elemento andando a valorizzare il campo \'Procedura relativa\'';
    $strumentiSelezione .= " <span class=\"intTooltip\"><a href=\"#\" data-placement=\"top\" data-rel=\"tooltip\" data-original-title=\"".$testoTooltip."\" class=\"btn\"><span class=\"iconfa-remove-sign\" style=\"color: #D50000; font-size: 80%\"></span></a></span>";
}
?>