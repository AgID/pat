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
 
require_once("./classi/RequestWS.php");
require_once("./classi/ResponseWS.php");

class ClientWS {

    var $soapClient;
    var $dominio;
    var $token;
    var $idServer;

    //Costruttore
    function  __construct($wsdl, $username = "", $password = "", $dominio = "") {
        global $dati_db,$database;
        ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
        try {
            // Workaround per un indirizzo non valido / non raggiungibile
            if(!@file_get_contents($wsdl)) {
                throw new SoapFault("Server", "Non trovato.");
            }
            $this->soapClient = new SoapClient($wsdl, array('encoding'=>'ISO-8859-1', 'trace'=>1, 'exceptions'=>TRUE, 'classmap'=>array('ResponseWS'=>"ResponseWS") ));
            $this->dominio = $dominio;
            $this->token = md5($username.$password.$dominio);

            $sql = "SELECT id FROM ".$dati_db['prefisso']."configurazione_webservice WHERE wsdl = '".$wsdl."'";
            if ( !($result = $database->connessioneConReturn($sql)) ) {
                    die('Errore durante il recupero del server webservice'.$sql);
            }
            $riga = $database->sqlArray($result);
            $this->idServer = $riga['id'];


        } catch (SoapFault $e) {
        	//invece di die ho messo un echo
        	echo("Errore nell'indirizzo del Webservice: contattare il supporto tecnico del portale.");
        }
    }

    function createRequest($parametri) {
        $request = new RequestWS();
        $request->value['token'] = $this->token;
        $request->value['dominio'] = $this->dominio;

        foreach ($parametri as $key => $value) {
            $request->value[$key] = $value;
        }
        return $request;
    }

    function getCache($parametri, $nomeMetodo) {
        global $dati_db,$database,$configurazioneWebservice;

        //per ora disabilitata
        return false;

        $sql="SELECT response FROM ".$dati_db['prefisso']."cache_webservice "
            ."WHERE idServer=$this->idServer "
            ."AND md5_parametri = '".md5(serialize($parametri))."' "
            ."AND metodo_chiamato = '".$nomeMetodo."' "
            ."AND time_to_sec(timediff(now() , last_request)) < ".$configurazioneWebservice[$this->idServer]['secondi_durata_cache']
            ;
            
            if ( !($result = $database->connessioneConReturn($sql)) ) {
                return false;
            } else {
                $riga = $database->sqlArray($result);
                $riga = unserialize($riga['response']);
                return $riga;
            }
    }

    function setCache($parametri, $nomeMetodo, $response) {
        global $dati_db,$database,$configurazioneWebservice;

        //per ora disabilitata
        return true;

        $sql="DELETE FROM ".$dati_db['prefisso']."cache_webservice "
            ."WHERE idServer=$this->idServer "
            ."AND md5_parametri = '".md5(serialize($parametri))."' "
            ."AND metodo_chiamato = '".$nomeMetodo."' ";
        if ( !($result = $database->connessioneConReturn($sql)) ) {
            //echo "cancellati $result record\n";
        }

        $sql="INSERT INTO ".$dati_db['prefisso']."cache_webservice (idServer, md5_parametri, metodo_chiamato, last_request, response) VALUES ("
            .$this->idServer.", "
            ."'".md5(serialize($parametri))."', "
            ."'".$nomeMetodo."', "
            ."now(), "
            ."'".addslashes(serialize($response))."')";
            if ( !($result = $database->connessioneConReturn($sql)) ) {
                return false;
            } else {
                return true;
            }
    }

    function getOggetti($parametri = array()) {
        try {
            $cache = $this->getCache($parametri, "getOggetti");
            if(!$cache) {
                $return = $this->soapClient->getOggetti($this->createRequest($parametri));
                $this->setCache($parametri, "getOggetti", $return);
            } else {
                $return = $cache;
            }
            //TODO: patch da sistemare...
            if(!is_array($return->value[0])) {
                $value = $return->value;
                unset($return->value);
                $return->value[0] = $value;
            }

            return $return;

        } catch (SoapFault $fault) {
            print_r($this->soapClient->__getLastResponse());
            trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}\n" .
                "faultstring: {$fault->faultstring})", E_USER_ERROR);

        }
    }

    function getCategorieOggetto($parametri = array()) {
        try {
            $cache = $this->getCache($parametri, "getCategorieOggetto");
            if(!$cache) {
                $return = $this->soapClient->getCategorieOggetto($this->createRequest($parametri));
                $this->setCache($parametri, "getCategorieOggetto", $return);
            } else {
                $return = $cache;
            }
            
            //TODO: patch da sistemare...
            if(!is_array($return->value[0])) {
                $value = $return->value;
                unset($return->value);
                $return->value[0] = $value;
            }
            return $return;

        } catch (SoapFault $fault) {
            trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}\n" .
                "faultstring: {$fault->faultstring})", E_USER_ERROR);
        }
    }

    function getCriteri($parametri = array()) {
        try {
            $cache = $this->getCache($parametri, "getCriteri");
            if(!$cache) {
                $return = $this->soapClient->getCriteri($this->createRequest($parametri));
                $this->setCache($parametri, "getCriteri", $return);
            } else {
                $return = $cache;
            }
            return $return;

        } catch (SoapFault $fault) {
            trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}\n" .
                "faultstring: {$fault->faultstring})", E_USER_ERROR);
        }
    }

    function getIstanze($parametri = array()) {
        try {
            $cache = $this->getCache($parametri, "getIstanze");
            if(!$cache) {
                $return = $this->soapClient->getIstanze($this->createRequest($parametri));
                $this->setCache($parametri, "getIstanze", $return);
            } else {
                $return = $cache;
            }
            return $return;

        } catch (SoapFault $fault) {
            trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}\n" .
                "faultstring: {$fault->faultstring})", E_USER_ERROR);
        }
    }

    function getIstanzeDaCriterio($parametri = array()) {
        try {
            $cache = $this->getCache($parametri, "getIstanzeDaCriterio");
            if(!$cache) {
                $return = $this->soapClient->getIstanzeDaCriterio($this->createRequest($parametri));
                $this->setCache($parametri, "getIstanzeDaCriterio", $return);
            } else {
                $return = $cache;
            }
            return $return;
            
        } catch (SoapFault $fault) {
            trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}\n" .
                "faultstring: {$fault->faultstring})", E_USER_ERROR);
        }
    }

    function getIstanzeConWhere($parametri = array()) {
        try {
            $cache = $this->getCache($parametri, "getIstanzeConWhere");
            if(!$cache and $this->soapClient) {
                $return = $this->soapClient->getIstanzeConWhere($this->createRequest($parametri));
                $this->setCache($parametri, "getIstanzeConWhere", $return);
            } else {
                $return = $cache;
            }
//            Perchè è stato messo utf8_decode???
//            foreach ($return->value as $riga) {
//                foreach ($riga as $colonna) {
//                    if(is_string($colonna)){
//                        $colonna = utf8_decode($colonna);
//                    }
//                }
//            }
            return $return;

        } catch (SoapFault $fault) {
            print_r($this->soapClient->__getLastResponse());
            trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}\n" .
                "faultstring: {$fault->faultstring})", E_USER_ERROR);
        }
    }

    function getStrutturaOggetto($parametri = array()) {
        try {
            $cache = $this->getCache($parametri, "getStrutturaOggetto");
            if(!$cache) {
                $return = $this->soapClient->getStrutturaOggetto($this->createRequest($parametri));
                $this->setCache($parametri, "getStrutturaOggetto", $return);
            } else {
                $return = $cache;
            }
            return $return;

        } catch (SoapFault $fault) {
            trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}\n" .
                "faultstring: {$fault->faultstring})", E_USER_ERROR);
        }
    }

    function getLingueIstallate($parametri = array()) {
        try {
            $cache = $this->getCache($parametri, "getLingueIstallate");
            if(!$cache) {
                $return = $this->soapClient->getLingueIstallate($this->createRequest($parametri));
                $this->setCache($parametri, "getLingueIstallate", $return);
            } else {
                $return = $cache;
            }
            return $return;

        } catch (SoapFault $fault) {
            trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}\n" .
                "faultstring: {$fault->faultstring})", E_USER_ERROR);
        }
    }

    function getSiteMap($parametri = array()) {
        try {
            $cache = $this->getCache($parametri, "getSiteMap");
            if(!$cache) {
                $return = $this->soapClient->getSiteMap($this->createRequest($parametri));
                $this->setCache($parametri, "getSiteMap", $return);
            } else {
                $return = $cache;
            }
            return $return;

        } catch (SoapFault $fault) {
            print_r($this->soapClient->__getLastResponse());
            trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}\n" .
                "faultstring: {$fault->faultstring})", E_USER_ERROR);
        }
    }

    function getNomeUtente($parametri = array()) {
        try {
            $cache = $this->getCache($parametri, "getNomeUtente");
            if(!$cache) {
                $return = $this->soapClient->getNomeUtente($this->createRequest($parametri));
                $this->setCache($parametri, "getNomeUtente", $return);
            } else {
                $return = $cache;
            }
            return $return;

        } catch (SoapFault $fault) {
            trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}\n" .
                "faultstring: {$fault->faultstring})", E_USER_ERROR);
        }
    }

    function getFileInfo($parametri = array()) {
        try {
            $cache = $this->getCache($parametri, "getFileInfo");
            if(!$cache) {
                $return = $this->soapClient->getFileInfo($this->createRequest($parametri));
                $this->setCache($parametri, "getFileInfo", $return);
            } else {
                $return = $cache;
            }
            return $return;

        } catch (SoapFault $fault) {
            trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}\n" .
                "faultstring: {$fault->faultstring})", E_USER_ERROR);
        }
    }

    function getGenericRecord($parametri = array()) {
        try {
            $cache = $this->getCache($parametri, "getGenericRecord");
            if(!$cache) {
                $return = $this->soapClient->getGenericRecord($this->createRequest($parametri));
                $this->setCache($parametri, "getGenericRecord", $return);
            } else {
                $return = $cache;
            }
            return $return;

        } catch (SoapFault $fault) {
            trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}\n" .
                "faultstring: {$fault->faultstring})", E_USER_ERROR);
        }
    }

    function getConfigurazione($parametri = array()) {
        try {
            $cache = $this->getCache($parametri, "getConfigurazione");
            if(!$cache) {
                $return = $this->soapClient->getConfigurazione($this->createRequest($parametri));
                $this->setCache($parametri, "getConfigurazione", $return);
            } else {
                $return = $cache;
            }
            return $return;

        } catch (SoapFault $fault) {
            print_r($this->soapClient->__getLastResponse());
            trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}\n" .
                "faultstring: {$fault->faultstring})", E_USER_ERROR);
        }
    }

    function getTags($parametri = array()) {
        try {
            $cache = $this->getCache($parametri, "getTags");
            if(!$cache) {
                $return = $this->soapClient->getTags($this->createRequest($parametri));
                $this->setCache($parametri, "getTags", $return);
            } else {
                $return = $cache;
            }
            return $return;

        } catch (SoapFault $fault) {
            trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}\n" .
                "faultstring: {$fault->faultstring})", E_USER_ERROR);
        }
    }
}
?>
