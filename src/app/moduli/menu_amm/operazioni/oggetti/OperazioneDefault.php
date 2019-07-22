<?php
class OperazioneDefault {

	var $arrayInterno = array();
	
	public function __construct() { }
	
	public function preInsert($arrayParametri = array()) { }
	
	public function postInsert($arrayParametri = array()) { }
	
	public function preUpdate($arrayParametri = array()) { }
	
	public function postUpdate($arrayParametri = array()) { }
	
	public function preDelete($arrayParametri = array()) { }
	
	public function postDelete($arrayParametri = array()) { }
	
	public function preImport($arrayParametri = array()) { }
	
	public function postImport($arrayParametri = array()) { }

}
?>