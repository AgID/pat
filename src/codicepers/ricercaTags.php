<?
$tabella = 'tags';
$campo = 'nome';
$q = $qTags;
$qRicerca = explode(' ',$q);

$minChars = $_GET['minChars'];
if($minChars == 0) {
	$minChars = 3;
}

//$sql = "SELECT id,".$campo." FROM ".$dati_db['prefisso'].$tabella." WHERE ".$campo." LIKE '%".($q)."%' ORDER BY ".$campo;
$arrayCampiRicerca = array();
foreach((array)$qRicerca as $qr) {
	if(strlen(stripslashes($qr)) >= $minChars) {
		$arrayCampiRicerca[] = "(".$campo." = '".$qr."' OR ".$campo." LIKE '".$qr."%' OR ".$campo." LIKE '% ".$qr."%' ) ";
	}
}
$campiRicerca = implode(' OR ', $arrayCampiRicerca);
$sql = "SELECT id, ".$campo." FROM ".$dati_db['prefisso'].$tabella." WHERE 1=1 AND (";
$sql .= $campiRicerca;
$sql .=") ORDER BY ".$campo;

if (!($result = $database -> connessioneConReturn($sql))) {
	$composizione = array();
} else {
	$composizione = $database -> sqlArrayAss($result);
}
/*
$f = fopen('temp/testTags.txt', 'a+');
fwrite($f, $sql."\n\r");
fclose($f);
*/
$items = array();
$checkItems = array();
foreach((array)$composizione as $res) {
	//decodifica html
	/*
	if(strpos(html_entity_decode(trim(strtolower($res[$campo]))), stripslashes(strtolower($q))) !== false) {
		if(!in_array(html_entity_decode(trim(strtolower($res[$campo]))), $checkItems)) {
			$items[$res['id']] = html_entity_decode(trim($res[$campo]));
			$checkItems[] = html_entity_decode(trim(strtolower($res[$campo])));
		}
	}
	*/
	foreach((array)$qRicerca as $qr) {
		if(strpos(html_entity_decode(trim(strtolower($res[$campo]))), stripslashes(strtolower($qr))) !== false) {
			$items[$res['id']] = html_entity_decode(trim($res[$campo]));
			$checkItems[] = html_entity_decode(trim(strtolower($res[$campo])));
		}
	}
}
if(count($items) > 0) {
	$output .= "|<div class=\"".$classeTitolo."\">Argomenti del sito</div>|t\n";
}
foreach ((array)$items as $key=>$value) {
	$output .= "index.php?azione=cercatag&id_tag=".$key."|$value|e\n";
}
?>