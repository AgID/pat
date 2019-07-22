<?php
if($campo['campo'] == 'data_richiesta') {
	$campoData = 'data_richiesta';
} else if($campo['campo'] == 'data_riesame') {
	$campoData = 'data_riesame';
}
if($istanzaOggetto[$campoData] > 0) {
	switch($istanzaOggetto['stato_pratica']) {
		case 'in corso':
			if($campoData == 'data_richiesta' and $istanzaOggetto['stato_pratica'] == 'in corso') {
				$datetime1 = new DateTime(date('Y-m-d', $istanzaOggetto[$campoData]));
				$datetime1->add(new DateInterval('P30D'));
				$datetime2 = new DateTime(date('Y-m-d'));
				$diff = $datetime2->diff($datetime1);
				if($diff->format('%R%a') >= 0) {
					if($diff->format('%R%a') >= 10) {
						$color = '#33CC55';
					} else {
						$color = '#EEAA00';
					} 
					$outputScreen .= "&nbsp;&nbsp;<span class=\"intTooltip\"><a href=\"#\" data-placement=\"top\" data-rel=\"tooltip\" data-original-title=\"".$diff->format('%a')." giorni per rispondere\" class=\"btn\"><span class=\"iconfa-info-sign\" style=\"color: ".$color.";\"></span></a></span>";
				} else {
					$outputScreen .= "&nbsp;&nbsp;<span class=\"intTooltip\"><a href=\"#\" data-placement=\"top\" data-rel=\"tooltip\" data-original-title=\"ATTENZIONE: non hai risposto entro i 30 giorni previsti\" class=\"btn\"><span class=\"iconfa-warning-sign\" style=\"color: #FF5511;\"></span></a></span>";
				}
			}
			break;
		case 'richiesto riesame':
			if($campoData == 'data_riesame' and $istanzaOggetto['stato_pratica'] == 'richiesto riesame') {
				$datetime1 = new DateTime(date('Y-m-d', $istanzaOggetto[$campoData]));
				$datetime1->add(new DateInterval('P20D'));
				$datetime2 = new DateTime(date('Y-m-d'));
				$diff = $datetime2->diff($datetime1);
				if($diff->format('%R%a') > 0) {
					if($diff->format('%R%a') >= 10) {
						$color = '#33CC55';
					} else {
						$color = '#EEAA00';
					}
					$outputScreen .= "&nbsp;&nbsp;<span class=\"intTooltip\"><a href=\"#\" data-placement=\"top\" data-rel=\"tooltip\" data-original-title=\"".$diff->format('%a')." giorni per rispondere\" class=\"btn\"><span class=\"iconfa-info-sign\" style=\"color: ".$color.";\"></span></a></span>";
				} else {
					//per ora non serve
					//$outputScreen .= "&nbsp;&nbsp;<span class=\"intTooltip\"><a href=\"#\" data-placement=\"top\" data-rel=\"tooltip\" data-original-title=\"ATTENZIONE: non hai risposto entro i 20 giorni previsti\" class=\"btn\"><span class=\"iconfa-warning-sign\" style=\"color: #FF5511;\"></span></a></span>";
				}
			}
			break;
	}
}
?>