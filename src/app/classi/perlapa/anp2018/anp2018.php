<?php
class inserimentoIncarico_Dipendente {
	var $username; // string
	var $password; // string
	var $any; // <anyXML>
}
class inserimentoIncarico_DipendenteResponse {
	var $inserimentoIncarico_DipendenteResult; // inserimentoIncarico_DipendenteResult
}
class inserimentoIncarico_DipendenteResult {
	var $any; // <anyXML>
}
class variazioneIncarico_Dipendente {
	var $username; // string
	var $password; // string
	var $any; // <anyXML>
}
class variazioneIncarico_DipendenteResponse {
	var $variazioneIncarico_DipendenteResult; // variazioneIncarico_DipendenteResult
}
class variazioneIncarico_DipendenteResult {
	var $any; // <anyXML>
}
class inserimentoIncarico_Consulente {
	var $username; // string
	var $password; // string
	var $any; // <anyXML>
}
class inserimentoIncarico_ConsulenteResponse {
	var $inserimentoIncarico_ConsulenteResult; // inserimentoIncarico_ConsulenteResult
}
class inserimentoIncarico_ConsulenteResult {
	var $any; // <anyXML>
}
class variazioneIncarico_Consulente {
	var $username; // string
	var $password; // string
	var $any; // <anyXML>
}
class variazioneIncarico_ConsulenteResponse {
	var $variazioneIncarico_ConsulenteResult; // variazioneIncarico_ConsulenteResult
}
class variazioneIncarico_ConsulenteResult {
	var $any; // <anyXML>
}
class cancellazioneIncarico {
	var $username; // string
	var $password; // string
	var $any; // <anyXML>
}
class cancellazioneIncaricoResponse {
	var $cancellazioneIncaricoResult; // cancellazioneIncaricoResult
}
class cancellazioneIncaricoResult {
	var $any; // <anyXML>
}

class anp {
	var $soapClient;
	private static $classmap = array (
			'inserimentoIncarico_Dipendente' => 'inserimentoIncarico_Dipendente',
			'inserimentoIncarico_DipendenteResponse' => 'inserimentoIncarico_DipendenteResponse',
			'inserimentoIncarico_DipendenteResult' => 'inserimentoIncarico_DipendenteResult',
			'variazioneIncarico_Dipendente' => 'variazioneIncarico_Dipendente',
			'variazioneIncarico_DipendenteResponse' => 'variazioneIncarico_DipendenteResponse',
			'variazioneIncarico_DipendenteResult' => 'variazioneIncarico_DipendenteResult',
			'inserimentoIncarico_Consulente' => 'inserimentoIncarico_Consulente',
			'inserimentoIncarico_ConsulenteResponse' => 'inserimentoIncarico_ConsulenteResponse',
			'inserimentoIncarico_ConsulenteResult' => 'inserimentoIncarico_ConsulenteResult',
			'variazioneIncarico_Consulente' => 'variazioneIncarico_Consulente',
			'variazioneIncarico_ConsulenteResponse' => 'variazioneIncarico_ConsulenteResponse',
			'variazioneIncarico_ConsulenteResult' => 'variazioneIncarico_ConsulenteResult',
			'cancellazioneIncarico' => 'cancellazioneIncarico',
			'cancellazioneIncaricoResponse' => 'cancellazioneIncaricoResponse',
			'cancellazioneIncaricoResult' => 'cancellazioneIncaricoResult' 
	)
	;
	function __construct($url = 'https://servizi.perlapa.gov.it/wsanp2018/ws/anp2018.asmx?WSDL') {
		$this->soapClient = new SoapClient ( $url, array (
				"classmap" => self::$classmap,
				"trace" => true,
				"exceptions" => true 
		) );
	}
	function inserimentoIncarico_Dipendente($inserimentoIncarico_Dipendente) {
		$inserimentoIncarico_DipendenteResponse = $this->soapClient->inserimentoIncarico_Dipendente ( $inserimentoIncarico_Dipendente );
		return $inserimentoIncarico_DipendenteResponse;
	}
	function variazioneIncarico_Dipendente($variazioneIncarico_Dipendente) {
		$variazioneIncarico_DipendenteResponse = $this->soapClient->variazioneIncarico_Dipendente ( $variazioneIncarico_Dipendente );
		return $variazioneIncarico_DipendenteResponse;
	}
	function inserimentoIncarico_Consulente($inserimentoIncarico_Consulente) {
		$inserimentoIncarico_ConsulenteResponse = $this->soapClient->inserimentoIncarico_Consulente ( $inserimentoIncarico_Consulente );
		return $inserimentoIncarico_ConsulenteResponse;
	}
	function variazioneIncarico_Consulente($variazioneIncarico_Consulente) {
		$variazioneIncarico_ConsulenteResponse = $this->soapClient->variazioneIncarico_Consulente ( $variazioneIncarico_Consulente );
		return $variazioneIncarico_ConsulenteResponse;
	}
	function cancellazioneIncarico($cancellazioneIncarico) {
		$cancellazioneIncaricoResponse = $this->soapClient->cancellazioneIncarico ( $cancellazioneIncarico );
		return $cancellazioneIncaricoResponse;
	}
}

?>