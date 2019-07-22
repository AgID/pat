<?php
/*
 * Created on 30/nov/2015
 *
 */
?>
<div style="display:none;">

	<input type="hidden" name="indici_impatto_risposte" id="indici_impatto_risposte" value="<? echo $istanzaOggetto['indici_impatto_risposte']; ?>" />
	
	<?
	if($istanzaOggetto['indici_impatto_risposte'] != '') {
		$idr = explode('|', $istanzaOggetto['indici_impatto_risposte']);
	}
	?>

	<div id="cbox_content_<? echo $parametri['id_campo']; ?>" class="calcolo_indici_impatto_risposte">
		
		<table class="table table-bordered">
			<thead><tr>
				<th>Impatto organizzativo - Rispetto al totale del personale impiegato nel singolo servizio (unità organizzativa semplice) competente a svolgere il processo (o la fase di processo di competenza della p.a.) nell'ambito della singola
					p.a., quale percentuale di personale è impiegata nel processo? (se il processo coinvolge l'attività di più servizi nell'ambito della stessa p.a. occorre riferire la percentuale al personale impiegato nei servizi
					coinvolti)
				</th>
				<th></th>
			</tr></thead>
			<tr>
				<td>
					Fino a circa il 20%
				</td>
				<td><input type="radio" name="idr6" data-id="6" value="1" <? echo ($idr[0] == 1 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					Fino a circa il 40%
				</td>
				<td><input type="radio" name="idr6" data-id="6" value="2" <? echo ($idr[0] == 2 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					Fino a circa il 60%
				</td>
				<td><input type="radio" name="idr6" data-id="6" value="3" <? echo ($idr[0] == 3 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					Fino a circa l'80%
				</td>
				<td><input type="radio" name="idr6" data-id="6" value="4" <? echo ($idr[0] == 4 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					Fino a circa il 100%
				</td>
				<td><input type="radio" name="idr6" data-id="6" value="5" <? echo ($idr[0] == 5 ? 'checked' : ''); ?> /></td>
			</tr>
		</table>
		
		<table class="table table-bordered">
			<thead><tr>
				<th>Impatto economico - Nel corso degli ultimi 5 anni sono state pronunciate sentenze della Corte dei conti a carico di dipendenti (dirigenti e dipendenti) della p.a. di riferimento o sono state pronunciate sentenze di
					risarcimento del danno nei confronti della p.a. di riferimento per la medesima tipologia di evento o di tipologie analoghe?
				</th>
				<th></th>
			</tr></thead>
			<tr>
				<td>
					No
				</td>
				<td><input type="radio" name="idr7" data-id="7" value="0" <? echo ((isset($idr[1]) and $idr[1] == 0) ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					Sì
				</td>
				<td><input type="radio" name="idr7" data-id="7" value="5" <? echo ($idr[1] == 5 ? 'checked' : ''); ?> /></td>
			</tr>
		</table>
	
		<table class="table table-bordered">
			<thead><tr>
				<th>Impatto reputazionale - Nel corso degli ultimi 5 anni sono stati pubblicati su giornali o riviste articoli aventi ad oggetto il medesimo evento o eventi analoghi?</th>
				<th></th>
			</tr></thead>
			<tr>
				<td>
					No
				</td>
				<td><input type="radio" name="idr8" data-id="8" value="0" <? echo ((isset($idr[2]) and $idr[2] == 0) ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					Non ne abbiamo memoria
				</td>
				<td><input type="radio" name="idr8" data-id="8" value="1" <? echo ($idr[2] == 1 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					Sì, sulla stampa locale
				</td>
				<td><input type="radio" name="idr8" data-id="8" value="2" <? echo ($idr[2] == 2 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					Sì, sulla stampa nazionale
				</td>
				<td><input type="radio" name="idr8" data-id="8" value="3" <? echo ($idr[2] == 3 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					Sì, sulla stampa locale e nazionale
				</td>
				<td><input type="radio" name="idr8" data-id="8" value="4" <? echo ($idr[2] == 4 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					Sì, sulla stampa locale, nazionale e internazionale
				</td>
				<td><input type="radio" name="idr8" data-id="8" value="5" <? echo ($idr[2] == 5 ? 'checked' : ''); ?> /></td>
			</tr>
		</table>
		
		<table class="table table-bordered">
			<thead><tr>
				<th>Impatto organizzativo, economico e sull'immagine - A quale livello può collocarsi il rischio dell'evento (livello apicale, livello intermedio o livello basso) ovvero la posizione/il ruolo che
					l'eventuale soggetto riveste nell'organizzazione è elevata, media o bassa?
				</th>
				<th></th>
			</tr></thead>
			<tr>
				<td>
					A livello di addetto
				</td>
				<td><input type="radio" name="idr9" data-id="9" value="1" <? echo ($idr[3] == 1 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					A livello di collaboratore o funzionario
				</td>
				<td><input type="radio" name="idr9" data-id="9" value="2" <? echo ($idr[3] == 2 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					A livello di dirigente di ufficio non generale ovvero di posizione apicale o di posizione organizzativa
				</td>
				<td><input type="radio" name="idr9" data-id="9" value="3" <? echo ($idr[3] == 3 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					A livello di dirigente di ufficio generale
				</td>
				<td><input type="radio" name="idr9" data-id="9" value="4" <? echo ($idr[3] == 4 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					A livello di capo dipartimento/segretario generale
				</td>
				<td><input type="radio" name="idr9" data-id="9" value="5" <? echo ($idr[3] == 5 ? 'checked' : ''); ?> /></td>
			</tr>
		</table>
	
	</div>

</div>