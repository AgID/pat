<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////PAT - Portale Amministrazione Trasparente////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////versione 1.0 - AgID release//////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * @file
 * webservice/ClientWS.php
 * 
 * @Descrizione
 * Servizi di tipo SOAP per interoperabilità con PAT
 *
 */
 
require_once('../inc/config.php');
require_once('../classi/database.php');
require_once('../classi/documento.php');
require_once('../classi/ResponseWS.php'); // includo WebServices di ISWEB

ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache

//Connessione al Database
$database = new database($dati_db['host'], $dati_db['user'], $dati_db['password'], $dati_db['database'], $dati_db['persistenza']);

//Carico la configurazione
$sql = "SELECT * FROM ".$dati_db['prefisso']."configurazione";
if( !($result = $database->connessioneConReturn($sql)) ) {
    die("Database non installato o non disponibile: errore critico.");
}
$configurazione = array();
while ( $riga = $database->sqlArray($result) ) {
    $configurazione[$riga['nome']] = $riga['valore'];
}

//Carico le sezioni
$sql = "SELECT * FROM ".$dati_db['prefisso']."sezioni ORDER by priorita";
if( !($result = $database->connessioneConReturn($sql)) ) {
    die("non riesco a prendere le informazioni selle sezioni in db");
}
$sezioni=$database->sqlArrayAss($result);

//Carico la sezione Home
if (count($sezioni) != 0) {
    foreach ($sezioni as $sezione) {
        if ($sezione['id'] == 0) {
            $sezioneHome = $sezione;
        }
    }
}

//Carico gli oggetti
$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetti ORDER BY nome";
if( !($result = $database->connessioneConReturn($sql)) ) {
    die("non riesco a prendere le informazioni sugli oggetti in db");
}
$oggetti=$database->sqlArrayAss($result);
$oggetti[] = array(
    'id' => -2,
    'nome' => 'Sezioni dal server',
    'nomedb' => 'sezioni',
    'descrizione' => 'Oggetto sezione, per la gestione di diverse pagine nel progetto.',
    'tabella' => 'sezioni',
    'proprieta' => 'sezione',
    'int_speciale' => 'normale',
    'campi_ricerca' => 'nome,descrizione',
    'int_admin' => 'ricerca completa',
    'campi_ricerca_admin' => 'nome,descrizione,id_proprietario,tags',
    'ordine_default_admin' => 'id_riferimento',
    'campi_admin' => 'nome{tags{id_proprietario',
    'campi_admin_titoli' => 'Nome della sezione{Tags associati{Proprietario',
    'campi_admin_proprieta' => '{normale{Proprietario',
    'campi_admin_stile' => '0{0{0',
    'campo_default' => 'nome',
    'template' => 'sezioni',
    'id_categoria' => 0
);
$datiUser = array();

class ServerWS {

    function isAuthenticated($token, $dominio, $auth = 1) {
        global $dati_db,$database,$configurazione;

        if($auth == 0)
            return true;

        if(!$configurazione['modulo_webservice_server'])
            return false;

        $query = "SELECT username, password FROM ".$dati_db['prefisso']."utenti_webservice WHERE dominio = '$dominio'";
        if( !($result = $database->connessioneConReturn($query)) ) {
            $message = "errore";
        }
        $istanzeDB = $database->sqlArrayAss($result, MYSQL_ASSOC);
        if(!$istanzeDB) {
            $message = "errore";
        }
        //        $f = fopen("debug.txt", 'w');
        //        fwrite($f, $token."\n");
        //        fclose($f);
        if($token == md5($istanzeDB[0]['username'].$istanzeDB[0]['password'].$dominio))
            return true;
        else
            return false;
    }

    function sayHello($name) {
        return "Ciao ".$name."!";
    }

    function getOggetti($request) {
        global $dati_db,$database;
        //Controllo autorizzazioni: non per questo metodo

        $query = "SELECT * FROM ".$dati_db['prefisso']."oggetti WHERE proprieta = 'informativa'";

        if( !($result = $database->connessioneConReturn($query)) ) {
            $message = "errore";
        }
        $istanzeDB = $database->sqlArrayAss($result, MYSQL_ASSOC);
        if(!$istanzeDB) {
            $message = "errore";
        } else {
            $message = "ok";
        }

        $istanzeDB[] = array(
            'id' => -2,
            'nome' => 'Sezioni dal server',
            'nomedb' => 'sezioni',
            'descrizione' => 'Oggetto sezione, per la gestione di diverse pagine nel progetto.',
            'tabella' => 'sezioni',
            'proprieta' => 'sezione',
            'int_speciale' => 'normale',
            'campi_ricerca' => 'nome,descrizione',
            'int_admin' => 'ricerca completa',
            'campi_ricerca_admin' => 'nome,descrizione,id_proprietario,tags',
            'ordine_default_admin' => 'id_riferimento',
            'campi_admin' => 'nome{tags{id_proprietario',
            'campi_admin_titoli' => 'Nome della sezione{Tags associati{Proprietario',
            'campi_admin_proprieta' => '{normale{Proprietario',
            'campi_admin_stile' => '0{0{0',
            'campo_default' => 'nome',
            'template' => 'sezioni',
            'id_categoria' => 0
        );
        //TODO NICO: fare per tutti!
        $ret = $this->utf8_encode_recursive($istanzeDB);
        $response = new ResponseWS();
        $response->message = $message;
        $response->value = $ret;


        return $response;
    }

    function getCategorieOggetto($request) {
        global $dati_db,$database;
        //Controllo autorizzazioni: da mettere in ogni metodo
        if(!$this->isAuthenticated($request->value['token'], $request->value['dominio'])) {
            return $this->permessiNegatiResponse();
        }

        $query = "SELECT * FROM ".$dati_db['prefisso']."categoria_".$request->value['oggetto']." ORDER by id_riferimento,priorita";

        //        $f = fopen("debug.txt", 'a+');
        //        fwrite($f, date("d.m.y H:i:s").": ".$query."\n");

        if( !($result = $database->connessioneConReturn($query)) ) {
            $message = "errore";
        }
        $istanzeDB = $database->sqlArrayAss($result, MYSQL_ASSOC);
        if(!$istanzeDB) {
            $message = "vuoto";
        } else {
            $message = "ok";
        }

        //TODO NICO: fare per tutti!
        $ret = $this->utf8_encode_recursive($istanzeDB);
        $response = new ResponseWS();
        $response->message = $message;
        $response->value = $ret;

        return $response;
    }

    function getStrutturaOggetto($request) {
        global $dati_db,$database;
        //Controllo autorizzazioni: da mettere in ogni metodo
        if(!$this->isAuthenticated($request->value['token'], $request->value['dominio'])) {
            return $this->permessiNegatiResponse();
        }

        $query = "SELECT * FROM ".$dati_db['prefisso'].$request->value['oggetto']." limit 0,1";
        if( !($result = $database->connessioneConReturn($query)) ) {
            $message = "errore";
        }
        $numeroCampi = $database->sqlNumCampi($result);

        $struttura = array();

        $this->caricaOggetto($request->value['oggetto']);

        // analizzo le proprieta' dell'oggetto per vedere quanti sono i campi di default
        if ($this->idOggetto == -2) {
            $numTemp = $numeroCampi;

        } else {
        // oggetti normali
            switch ($this->proprieta) {
                case "contatto":
                    $numTemp = 6;
                    break;
                default:
                    $numTemp = 15;
                    break;
            }
        }

        $numTempVecchio = 0;
        if ($completa != 'no') {
            $numTempVecchio = $numTemp;
            $numTemp = 0;
        }

        for ($i=$numTemp;$i<$numeroCampi;$i++) {
            $struttura[] = array(
                'nomecampo' => $database->sqlNomeCampo($i,$result),
                'tipoinput' => $database->sqlTipoCampo($i,$result)
            );
        }


        // ora parso i valori in stringa sulla struttura
        $arrayValori = explode("{",$this->strutturaValori);
        $arrayEtichette = explode("{",$this->strutturaEtichette);
        $arrayDefault = explode("{",$this->strutturaDefault);
        $arrayTipi = explode("{",$this->strutturaTipo);
        $arrayProprieta = explode("{",$this->strutturaProprieta);
        $arrayOrdinamento = explode("{",$this->strutturaOrdinamento);
        $arrayMaxchar = explode("{",$this->strutturaMaxchar);
        $arrayStili = explode("{",$this->strutturaStili);
        $numeroValori = count($arrayValori)-1;
        $arrayPers = array();
        for ($i=0;$i<($numeroCampi-$numTemp);$i++) {
            if ($numTemp == 0 and $i<$numTempVecchio) {
            // campi di sistema
            // campi persionalizzati
                switch ($struttura[$i]['nomecampo']) {
                    case "data_creazione":
                        $struttura[$i]['etichetta'] = "CORE.data di inserimento";
                        $struttura[$i]['tipocampo'] =  'data_calendario';
                        break;
                    case "id_proprietario":
                        $struttura[$i]['etichetta'] = "CORE.utente proprietario";
                        $struttura[$i]['tipocampo'] = 'campoutente';
                        break;
                    case "ultima_modifica":
                        $struttura[$i]['etichetta'] = "CORE.data di ultima modifica";
                        $struttura[$i]['tipocampo'] =  'data_calendario';
                        break;
                    case "id_sezione":
                        $struttura[$i]['etichetta'] = "CORE.categoria di appartenenza";
                        $struttura[$i]['tipocampo'] = 'campocatoggetto';
                        break;
                    case "id_lingua":
                        $struttura[$i]['etichetta'] = "CORE.lingua di pubblicazione";
                        break;
                    case "numero_letture":
                        $struttura[$i]['etichetta'] = "CORE.numero di letture";
                        $struttura[$i]['tipocampo'] =  'numerico';
                    case "permessi_lettura":
                        $struttura[$i]['etichetta'] = "CORE.permessi di lettura";
                        $struttura[$i]['tipocampo'] =  'text';
                        break;
                    default:
                        $struttura[$i]['etichetta'] = "CORE.RISERVATO.".$struttura[$i]['nomecampo'];
                        $struttura[$i]['tipocampo'] = 'text';
                }
                $struttura[$i]['default'] =1;
                $struttura[$i]['stile'] =0;
                if ($this->idOggetto == -2) {
                    switch ($struttura[$i]['nomecampo']) {
                        case "tags":
                            $struttura[$i]['tipocampo'] = 'tags';
                            break;
                        case "id_riferimento":
                            $struttura[$i]['tipocampo'] = 'camposezione';
                            break;
                    }
                }
            } else {
            // campi persionalizzati
                $struttura[$i]['valorecampo'] = $arrayValori[$i-$numTempVecchio];
                $struttura[$i]['tipocampo'] =$arrayTipi[$i-$numTempVecchio];
                $struttura[$i]['etichetta'] =$arrayEtichette[$i-$numTempVecchio];
                $struttura[$i]['default'] =$arrayDefault[$i-$numTempVecchio];
                $struttura[$i]['proprieta'] =$arrayProprieta[$i-$numTempVecchio];
                $struttura[$i]['maxchar'] =$arrayMaxchar[$i-$numTempVecchio];
                $struttura[$i]['stile'] =$arrayStili[$i-$numTempVecchio];
                $struttura[$i]['ordinamento'] =$arrayOrdinamento[$i-$numTempVecchio];
            }
        }

        //TODO NICO: fare per tutti!
        $ret = $this->utf8_encode_recursive($struttura);
        $response = new ResponseWS();
        $response->message = $message;
        $response->value = $ret;

        return $response;
    }

    function getCriteri($request) {
        global $dati_db,$database;
        //Controllo autorizzazioni: da mettere in ogni metodo
        if(!$this->isAuthenticated($request->value['token'], $request->value['dominio'])) {
            return $this->permessiNegatiResponse();
        }

        $query = "SELECT * FROM ".$dati_db['prefisso']."regole_pubblicazione_oggetti_criteri";
        if( !($result = $database->connessioneConReturn($query)) ) {
            $message = "errore";
        }
        $istanzeDB = $database->sqlArrayAss($result, MYSQL_ASSOC);
        if(!$istanzeDB) {
            $message = "errore";
        } else {
            $message = "ok";
        }

        //TODO NICO: fare per tutti!
        $ret = $this->utf8_encode_recursive($istanzeDB);
        $response = new ResponseWS();
        $response->message = $message;
        $response->value = $ret;

        return $response;
    }

    function getIstanze($request) {
        global $dati_db,$database;
        //Controllo autorizzazioni: da mettere in ogni metodo
        if(!$this->isAuthenticated($request->value['token'], $request->value['dominio'])) {
            return $this->permessiNegatiResponse();
        }

        $query = "SELECT * FROM ".$dati_db['prefisso'].$request->value['oggetto'];
        if($request->value['id_lingua'] != 0)
            $where = " WHERE id_lingua=".$request->value['id_lingua'];

        $query .= $where;

        if( !($result = $database->connessioneConReturn($query)) ) {
            $message = "errore";
        }
        $istanzeDB = $database->sqlArrayAss($result, MYSQL_ASSOC);
        if(!$istanzeDB) {
            $message = "errore";
        } else {
            $message = "ok";
        }

        //TODO NICO: fare per tutti!
        $ret = $this->utf8_encode_recursive($istanzeDB);
        $response = new ResponseWS();
        $response->message = $message;
        $response->value = $ret;

        return $response;
    }

    function getIstanzeDaCriterio($request) {
        global $dati_db,$database;
        //Controllo autorizzazioni: da mettere in ogni metodo
        if(!$this->isAuthenticated($request->value['token'], $request->value['dominio'])) {
            return $this->permessiNegatiResponse();
        }

        $sql = "SELECT * FROM ".$dati_db['prefisso']."regole_pubblicazione_oggetti_criteri WHERE id=".$request->value['id_criterio'];
        if( !($result = $database->connessioneConReturn($sql)) ) {
            die("non riesco a prendere il criterio in db: ".$sql);
        }
        $criterio=$database->sqlArray($result);
        if (!is_array($criterio)) {
            $response = new ResponseWS();
            return $this->permessiNegatiResponse("Errore nell'estrazione del criterio.");
        }

        $documento = new documento($request->value['id_oggetto'],"si");

        if($request->value['id_lingua'] != 0)
            $documento->id_lingua = $request->value['id_lingua'];

        $listaDocumenti = $documento->caricaDocumentiCriterio($criterio);

        //TODO NICO: fare per tutti!
        $ret = $this->utf8_encode_recursive($listaDocumenti);
        $response = new ResponseWS();
        $response->message = $message;
        $response->value = $ret;

        return $response;
    }

    function getIstanzeConWhere($request) {
        global $dati_db,$database;
        //Controllo autorizzazioni: da mettere in ogni metodo
        if(!$this->isAuthenticated($request->value['token'], $request->value['dominio'])) {
            return $this->permessiNegatiResponse();
        }

        $query = "SELECT * FROM ".$dati_db['prefisso'].$request->value['oggetto'];
        $where = " WHERE 1=1 ".$request->value['where'];

        //Concateno la where alla query
        $query .= $where;

        //        $f = fopen("debug.txt", 'a+');
        //        fwrite($f, date("d.m.y H:i:s").": ".$query."\n");

        if( !($result = $database->connessioneConReturn($query)) ) {
            $message = "errore";
        }
        $istanzeDB = $database->sqlArrayAss($result, MYSQL_ASSOC);
        if(!$istanzeDB) {
            $message = "vuoto";
        } else {
            $message = "ok";
        }

        //TODO NICO: fare per tutti!
        $ret = $this->utf8_encode_recursive($istanzeDB);
        $response = new ResponseWS();
        $response->message = $message;
        $response->value = $ret;

        return $response;
    }

    function getLingueIstallate($request) {
        global $dati_db,$database;
        //Controllo autorizzazioni: da mettere in ogni metodo
        if(!$this->isAuthenticated($request->value['token'], $request->value['dominio'])) {
            return $this->permessiNegatiResponse();
        }

        $query = "SELECT id,nome,pubblicata,prefisso_template,charset FROM ".$dati_db['prefisso']."linguaggi ORDER BY id";
        if( !($result = $database->connessioneConReturn($query)) ) {
            $message = "errore";
        }
        $istanzeDB = $database->sqlArrayAss($result, MYSQL_ASSOC);
        if(!$istanzeDB) {
            $message = "errore";
        } else {
            $message = "ok";
        }

        //TODO NICO: fare per tutti!
        $ret = $this->utf8_encode_recursive($istanzeDB);
        $response = new ResponseWS();
        $response->message = $message;
        $response->value = $ret;

        return $response;
    }

    function getSiteMap($request) {
        global $sezioni,$sezioneHome;

        //Controllo autorizzazioni: da mettere in ogni metodo
        if(!$this->isAuthenticated($request->value['token'], $request->value['dominio'])) {
            return $this->permessiNegatiResponse();
        }

        $ret = $this->parsingSiteMapNoLink($request->value['idSezione']);

        //TODO NICO: fare per tutti!
        $ret = $this->utf8_encode_recursive($ret);
        $response = new ResponseWS();
        $response->message = "ok";
        $response->value['nome'] = $ret;

        return $response;
    }

    function getNomeUtente($request) {
        global $dati_db,$database,$dati_db_utenti,$utentiCondivisi;
        //Controllo autorizzazioni: da mettere in ogni metodo
        if(!$this->isAuthenticated($request->value['token'], $request->value['dominio'])) {
            return $this->permessiNegatiResponse();
        }

        if ($utentiCondivisi) {
            $database = new database($dati_db_utenti['host'], $dati_db_utenti['user'], $dati_db_utenti['password'], $dati_db_utenti['database']);
        }

        $id = $request->value['id'];
        $campo = $request->value['campo'];
        $sql = "SELECT * FROM ".$dati_db['prefisso']."utenti WHERE id=$id";

        if ( !($result = $database->connessioneConReturn($sql)) ) {
            $message = "Errore durante il recupero del campo utente";
        } else {
            $message = "ok";
        }

        $riga = $database->sqlArray($result);
        if ($utentiCondivisi) {
            $database = new database($dati_db['host'], $dati_db['user'], $dati_db['password'], $dati_db['database']);
        }

        //TODO NICO: fare per tutti!
        $ret = $this->utf8_encode_recursive(array(array($riga[$campo])));
        $response = new ResponseWS();
        $response->message = $message;
        $response->value = $ret;

        return $response;
    }

    function getFileInfo($request) {
        global $dati_db,$database,$dati_db_utenti,$utentiCondivisi, $uploadPath;
        //Controllo autorizzazioni: da mettere in ogni metodo
        if(!$this->isAuthenticated($request->value['token'], $request->value['dominio'])) {
            return $this->permessiNegatiResponse();
        }
        $value = array();
        $oggetto = $request->value['oggetto'];
        $file = $request->value['file'];
        $value['grandezza'] = filesize("../".$uploadPath.$oggetto."/".$file);
        $value['filePath'] = $uploadPath.$oggetto."/".$file;
        $value['outMedia'] = $oggetto."/".$file;

        //TODO NICO: fare per tutti!
        $ret = $this->utf8_encode_recursive(array($value));
        $response = new ResponseWS();
        $response->message = "ok";
        $response->value = $ret;

        return $response;
    }

    function getGenericRecord($request) {
        global $dati_db,$database;
        //Controllo autorizzazioni: da mettere in ogni metodo
        if(!$this->isAuthenticated($request->value['token'], $request->value['dominio'])) {
            return $this->permessiNegatiResponse();
        }

        $query = "SELECT ".$request->value['campi']." FROM ".$dati_db['prefisso'].$request->value['tabella']." ".$request->value['where'];

        if( !($result = $database->connessioneConReturn($query)) ) {
            $message = "errore";
        }
        $istanzeDB = $database->sqlArrayAss($result, MYSQL_ASSOC);
        if(!$istanzeDB) {
            $message = "vuoto";
        } else {
            $message = "ok";
        }

        //TODO NICO: fare per tutti!
        $ret = $this->utf8_encode_recursive($istanzeDB);
        $response = new ResponseWS();
        $response->message = $message;
        $response->value = $ret;

        return $response;
    }

    function getConfigurazione($request) {
        global $dati_db,$database;
        //Controllo autorizzazioni: da mettere in ogni metodo
        if(!$this->isAuthenticated($request->value['token'], $request->value['dominio'])) {
            return $this->permessiNegatiResponse();
        }

        $query = "SELECT * FROM ".$dati_db['prefisso']."configurazione";
        $f = fopen("debug.txt", 'a+');
        fwrite($f, date("d.m.y H:i:s").": ".$query."\n");

        if( !($result = $database->connessioneConReturn($query)) ) {
            $message = "errore";
        }
        $istanzeDB = $database->sqlArrayAss($result, MYSQL_ASSOC);
        if(!$istanzeDB) {
            $message = "vuoto";
        } else {
            $message = "ok";
        }

        //TODO NICO: fare per tutti!
        $ret = $this->utf8_encode_recursive($istanzeDB);
        $response = new ResponseWS();
        $response->message = $message;
        $response->value = $ret;

        return $response;
    }

    function getTags($request) {
        global $dati_db,$database;
        //Controllo autorizzazioni: da mettere in ogni metodo
        if(!$this->isAuthenticated($request->value['token'], $request->value['dominio'])) {
            return $this->permessiNegatiResponse();
        }

        $query = "SELECT * FROM ".$dati_db['prefisso']."tags";

        if( !($result = $database->connessioneConReturn($query)) ) {
            $message = "errore";
        }
        $istanzeDB = $database->sqlArrayAss($result, MYSQL_ASSOC);
        if(!$istanzeDB) {
            $message = "vuoto";
        } else {
            $message = "ok";
        }

        //TODO NICO: fare per tutti!
        $ret = $this->utf8_encode_recursive($istanzeDB);
        $response = new ResponseWS();
        $response->message = $message;
        $response->value['tags'] = $ret;

        $query = "SELECT * FROM ".$dati_db['prefisso']."tags_categorie";

        if( !($result = $database->connessioneConReturn($query)) ) {
            $message = "errore";
        }
        $istanzeDB = $database->sqlArrayAss($result, MYSQL_ASSOC);
        if(!$istanzeDB) {
            $message = "vuoto";
        } else {
            $message = "ok";
        }

        //TODO NICO: fare per tutti!
        $ret = $this->utf8_encode_recursive($istanzeDB);
        $response->message .= ";".$message;
        $response->value['tags_categorie'] = $ret;

        return $response;
    }


    ////////////////////////////////////////////////////////////////////////////
    //Funzioni non esposte come Web Services (interne)
    ////////////////////////////////////////////////////////////////////////////
    function permessiNegatiResponse($message = "Non si hanno i permessi necessari all'operazione. Autorizzazione negata.") {
        $response = new ResponseWS();
        $response->message = $message;
        return $response;
    }

    // funzione di caricamento singolo oggetto, memorizza anche in buffer dell'oggetto la struttura personalizzata
    function caricaOggetto($tabella) {
        global $oggetti;

        foreach($oggetti as $oggetto) {
            if ($oggetto['tabella'] == $tabella) {
            // memorizzo le impostazioni sulla attuale struttura dell'oggetto
                $this->strutturaTipo = $oggetto['struttura_tipo'];
                $this->idOggetto = $oggetto['id'];
                $this->strutturaValori = $oggetto['struttura_valori'];
                $this->strutturaEtichette = $oggetto['struttura_etichette'];
                $this->strutturaDefault = $oggetto['struttura_default'];
                $this->strutturaProprieta = $oggetto['struttura_proprieta'];
                $this->strutturaMaxchar = $oggetto['struttura_maxchar'];
                $this->strutturaOrdinamento = $oggetto['struttura_ordinamento'];
                $this->tabellaOggetto = $oggetto['tabella'];
                $this->proprieta = $oggetto['proprieta'];
                $this->amministrazione = $oggetto['amministrazione'];
                $this->interfaccia = $oggetto['interfaccia'];
                $this->categoria = $oggetto['id_categoria'];
                $this->campo_default = $oggetto['campo_default'];
                $this->autorizzazione = $oggetto['richiesta_autorizzazione'];
            }
        }
    }

    function utf8_encode_recursive($input, $encode_keys=true) {
        if(is_array($input)) {
            $result = array();
            foreach($input as $k => $v) {
                $key = ($encode_keys)? utf8_encode($k) : $k;
                $result[$key] = $this->utf8_encode_recursive( $v, $encode_keys);
            }
        }
        else {
            $result = utf8_encode($input);
        }

        return $result;
    }

    // funzione di elaborazione sitemap senza link
    function parsingSiteMapNoLink($idSezione) {
        global $sezioni,$sezioneHome;

        
        
        // inizialmente ordino le sezioni per id_riferimenti decresecnti
        //$sezioniTemp = ordinArray($sezioni, 'id_riferimento', 'dec');
        // ora dall'id di partenza, torno indietro fino all'index
        $varCiclo = "continua";
        $idTemp = $idSezione;
        $strTemp = '';
        if ($idSezione != 0) {
            while ($varCiclo == "continua") {
                $sezioneTemp = $this->caricaSezioneGen($idTemp,$sezioni);
                $strTemp = " &#187; <b>".$sezioneTemp['nome']."</b>".$strTemp;
                if ($sezioneTemp['id_riferimento'] != 0) {
                    $idTemp = $sezioneTemp['id_riferimento'];
                } else {
                    $varCiclo = "fermati";
                }

            }
        }
        return $sezioneHome['nome'].$strTemp;

    }

    //funzione di ritorno unica sezione, dato l'array (sezione o categorie)
    function caricaSezioneGen($id,$arrayCategorie) {

        if (count($arrayCategorie) != 0) {
            foreach ($arrayCategorie as $sezione) {
                if ($sezione['id'] == $id) {
                    return $sezione;
                }
            }
        }

    }

}

ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
$server = new SoapServer($server_url."webservice/server.wsdl");
$server->setClass("ServerWS");
$server->handle();

// Chiudo la connessione al database
$database->sqlChiudi();

?>