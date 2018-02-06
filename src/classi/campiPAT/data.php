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
	 * classi/campiPAT/data.php
	 * 
	 * @Descrizione
	 * Output campo form con plugin calendario, sovrascrive lo standard ISWEB
	 *
	 */

$valoreVeroTxt = '';

// pubblico campo testuale
if ($valoreVero AND $valoreVero != '') {
	$valoreVeroTxt = visualizzaData($valoreVero,'d/m/Y');
}
echo "<div class=\"input-prepend\"><span class=\"add-on\"><span class=\"iconfa-calendar\"></span></span></div>";
echo "<input".$disabilitatoTxt." type=\"text\" name=\"".$nome."Vis\" id=\"".$nome."Vis\" value=\"".$valoreVeroTxt."\" class=\"".$disabilitatoClasse."input-small ".$classe."\" ".$stringaAttributi." />";
echo "<input type=\"hidden\" name=\"".$nome."\" id=\"".$nome."\" value=\"".$valoreVero."\" class=\"input-small ".$classe."\" />";
echo "<script type=\"text/javascript\">
	jQuery(document).ready(function(){jQuery('#".$nome."Vis').datepicker(jQuery.datepicker.regional['it']);	});
	jQuery(\"#".$nome."Vis\").change(function() {
		if (jQuery(\"#".$nome."Vis\").val() == '') {
			jQuery(\"#".$nome."\").val('');
		} else {
			jQuery(\"#".$nome."\").val(jQuery(\"#".$nome."Vis\").val());
		}
	});
</script>";
?>