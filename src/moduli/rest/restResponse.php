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
	 * moduli/rest/restResponse.php
	 * 
	 * @Descrizione
	 * Servizi di tipo rest per interoperabilità con PAT
	 *
	 */

class restResponse {
	
	var $tipoResponse;
	var $token;
	var $parametri;
	var $errore;
	
	public function __construct($request) {
		
		$parametri = explode('/rest.', $request);
		$parametri = $parametri[1];
		$parametri = explode('.', $parametri);
		$this->token = $parametri[0];
		$parametri = $parametri[1];
		$parametri = explode('/', $parametri);
		$this->parametri = $parametri;
		$this->tipoResponse = $parametri[0];
		$this->errore = false;
	}
	
	public function getToken() {
		return $this->token;
	}
	
	public function getTipoResponse() {
		return $this->tipoResponse;
	}
	
	public function getParametri() {
		return $this->parametri;
	}
	
	public function getParametro($index) {
		return $this->parametri[$index];
	}
	
	public function returnError($errore) {
		$return = array();
		$return['errore'] = 1;
		$return['messaggio'] = $errore;
		return $this->restResponse($return);
	}
	
	public function restResponse($return, $singolo = false) {
		switch($this->tipoResponse) {
			case 'json':
				header('Content-Type: application/json');
				if($singolo) {
					return $this->json_encode_objs($return, 1);
				} else {
					return $this->json_encode_objs($return);
				}
			break;
		}
	}
	
	public function exitApp() {
		global $database;
		$database->sqlChiudi();
		exit();
	}
	
	private function json_encode_objs($item, $livello = 0, $inserisciParentesi = true){
        if(!is_array($item) && !is_object($item)){
            return json_encode(utf8_encode(html_entity_decode($item)));
        }else{
            $pieces = array();
            foreach($item as $k=>$v){
            	if(is_int($k)) {
            		$pieces[] = $this->json_encode_objs($v, $livello + 1);
            	} else {
					if(!is_array($v) && !is_object($v)){
						$pieces[] = "\"$k\":".$this->json_encode_objs($v, $livello + 1);
					} else {
						$pieces[] = "\"$k\": [".$this->json_encode_objs($v, $livello + 1, false)."]";
					}
            	}
            }
            if($livello > 0) {
				if($inserisciParentesi) {
					return '{'.implode(',',$pieces).'}';
				} else {
					return implode(',',$pieces);
				}
            } else {
	            return '['.implode(',',$pieces).']';
            }
        }
    }
}
?>