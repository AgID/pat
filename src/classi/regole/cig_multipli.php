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
	 * classi/regole/cig_multipli.php
	 * 
	 * @Descrizione
	 * Output campo form dedicato per cig multipli
	 *
	 */


echo "<div class=\"par\">";

echo "<table id=\"tabellaCig".$numRigaCig."\" class=\"tabellaCig\" style=\"margin-left: 240px;\"><tr>";

echo "<td style=\"padding: 2px 10px 2px 0px;\" class=\"control-group\">CIG<br /><br />";
echo "<input id=\"cig".$numRigaCig."\" name=\"cig".$numRigaCig."\" value=\"\" class=\"input-medium cig-multipli\" /></td>";
/*
echo "<td style=\"padding: 2px 10px 2px 0px;\" class=\"control-group\">Senza<br />importo<br />";
echo "<select id=\"senza_importo".$numRigaCig."\" name=\"senza_importo".$numRigaCig."\" class=\"input-mini cig-multipli\">";
echo "<option value=\"NO\">No</option>";
echo "<option value=\"SI\">Si</option>";
echo "</select></td>";
*/
echo "<td style=\"padding: 2px 10px 2px 0px;\" class=\"control-group\">Importo a base asta<br />(al netto dell'IVA)<br />";
echo "<div class=\"input-prepend\"><span class=\"add-on\"><span class=\"\">&euro;</span></span><input id=\"valore_base_asta".$numRigaCig."\" name=\"valore_base_asta".$numRigaCig."\" value=\"\" class=\"input-medium cig-multipli\" /></div></td>";
/*
echo "<td style=\"padding: 2px 10px 2px 0px;\" class=\"control-group\">Importo di aggiudicazione<br />(al netto dell'IVA)<br />";
echo "<div class=\"input-prepend\"><span class=\"add-on\"><span class=\"\">&euro;</span></span><input id=\"valore_importo_aggiudicazione".$numRigaCig."\" name=\"valore_importo_aggiudicazione".$numRigaCig."\" value=\"\" class=\"input-small cig-multipli\" /></div></td>";

echo "<td style=\"padding: 2px 10px 2px 0px;\" class=\"control-group\">Importo liquidato<br />(al netto dell'IVA)<br />";
echo "<div class=\"input-prepend\"><span class=\"add-on\"><span class=\"\">&euro;</span></span><input id=\"importo_liquidato".$numRigaCig."\" name=\"importo_liquidato".$numRigaCig."\" value=\"\" class=\"input-small cig-multipli\" /></div></td>";
*/
echo "<td style=\"padding: 2px 10px 2px 0px;\"><br /><br />";
if($numRigaCig == 1) {
	echo "<a href=\"javascript:aggiungiRigaCig();\">Aggiungi Lotto</a>";
} else if($numRigaCig > 1) {
	echo "<a href=\"javascript:rimuoviRigaCig(".$numRigaCig.");\">Rimuovi</a>";
}
echo "</td>";

echo "</tr></table>";

echo "</div>";
?>