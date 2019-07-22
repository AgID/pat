<?php
/*
 * Created on 30/nov/2015
 *
 */
?>
<div style="display:none;">

	<input type="hidden" name="indici_probabilita_risposte" id="indici_probabilita_risposte" value="<? echo $istanzaOggetto['indici_probabilita_risposte']; ?>" />
	
	<?
	if($istanzaOggetto['indici_probabilita_risposte'] != '') {
		$idr = explode('|', $istanzaOggetto['indici_probabilita_risposte']);
	}
	?>

	<div id="cbox_content_<? echo $parametri['id_campo']; ?>" class="calcolo_indici_probabilita_risposte">
		
		<table class="table table-bordered">
			<thead><tr>
				<th>Discrezionalità - Il processo è discrezionale?</th>
				<th></th>
			</tr></thead>
			<tr>
				<td>
					No, è del tutto vincolato
				</td>
				<td><input type="radio" name="idr0" data-id="0" value="1" <? echo ($idr[0] == 1 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					E' parzialmente vincolato dalla legge e da atti amministrativi (regolamenti, direttive, circolari)
				</td>
				<td><input type="radio" name="idr0" data-id="0" value="2" <? echo ($idr[0] == 2 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					E' parzialmente vincolato solo dalla legge
				</td>
				<td><input type="radio" name="idr0" data-id="0" value="3" <? echo ($idr[0] == 3 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					E' parzialmente vincolato solo da atti amministrativi (regolamenti, direttive, circolari)
				</td>
				<td><input type="radio" name="idr0" data-id="0" value="4" <? echo ($idr[0] == 4 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					E' altamente discrezionale
				</td>
				<td><input type="radio" name="idr0" data-id="0" value="5" <? echo ($idr[0] == 5 ? 'checked' : ''); ?> /></td>
			</tr>
		</table>
	
		<table class="table table-bordered">
			<thead><tr>
				<th>Rilevanza esterna - Il processo produce effetti diretti all'esterno dell'amministrazione di riferimento?</th>
				<th></th>
			</tr></thead>
			<tr>
				<td>
					No, ha come destinatario finale un ufficio interno
				</td>
				<td><input type="radio" name="idr1" data-id="1" value="2" <? echo ($idr[1] == 2 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					Sì, il risultato del processo è rivolto direttamente ad utenti esterni alla p.a. di riferimento
				</td>
				<td><input type="radio" name="idr1" data-id="1" value="5" <? echo ($idr[1] == 5 ? 'checked' : ''); ?> /></td>
			</tr>
		</table>
		
		<table class="table table-bordered">
			<thead><tr>
				<th>Complessità del processo - Si tratta di un processo complesso che comporta il coinvolgimento di più amministrazioni (esclusi i controlli) in fasi successive per il conseguimento del risultato?</th>
				<th></th>
			</tr></thead>
			<tr>
				<td>
					No, il processo coinvolge una sola p.a.
				</td>
				<td><input type="radio" name="idr2" data-id="2" value="1" <? echo ($idr[2] == 1 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					Sì, il processo coinvolge più di 3 amministrazioni
				</td>
				<td><input type="radio" name="idr2" data-id="2" value="3" <? echo ($idr[2] == 3 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					Sì, il processo coinvolge più di 5 amministrazioni
				</td>
				<td><input type="radio" name="idr2" data-id="2" value="5" <? echo ($idr[2] == 5 ? 'checked' : ''); ?> /></td>
			</tr>
		</table>
		
		<table class="table table-bordered">
			<thead><tr>
				<th>Valore economico - Qual è l'impatto economico del processo?</th>
				<th></th>
			</tr></thead>
			<tr>
				<td>
					Ha rilevanza esclusivamente interna
				</td>
				<td><input type="radio" name="idr3" data-id="3" value="1" <? echo ($idr[3] == 1 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					Comporta l'attribuzione di vantaggi a soggetti esterni, ma di non particolare rilievo economico (es.: concessione di borsa di studio per studenti)
				</td>
				<td><input type="radio" name="idr3" data-id="3" value="3" <? echo ($idr[3] == 3 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					Comporta l'attribuzione di considerevoli vantaggi a soggetti esterni (es.: affidamento di appalto)
				</td>
				<td><input type="radio" name="idr3" data-id="3" value="5" <? echo ($idr[3] == 5 ? 'checked' : ''); ?> /></td>
			</tr>
		</table>
		
		<table class="table table-bordered">
			<thead><tr>
				<th>Frazionabilità del processo - Il risultato finale del processo può essere raggiunto anche effettuando una pluralità di operazioni di entità economica ridotta che, considerate complessivamente, alla fine assicurano lo stesso risultato (es.: pluralità di affidamenti ridotti)?</th>
				<th></th>
			</tr></thead>
			<tr>
				<td>
					No
				</td>
				<td><input type="radio" name="idr4" data-id="4" value="1" <? echo ($idr[4] == 1 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					Sì
				</td>
				<td><input type="radio" name="idr4" data-id="4" value="5" <? echo ($idr[5] == 5 ? 'checked' : ''); ?> /></td>
			</tr>
		</table>
		
		<table class="table table-bordered">
			<thead><tr>
				<th>Controlli - Anche sulla base dell'esperienza pregressa, il tipo di controllo applicato sul processo è adeguato a neutralizzare il rischio?</th>
				<th></th>
			</tr></thead>
			<tr>
				<td>
					Sì, costituisce un efficace strumento di neutralizzazione
				</td>
				<td><input type="radio" name="idr5" data-id="5" value="1" <? echo ($idr[5] == 1 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					Sì, è molto efficace
				</td>
				<td><input type="radio" name="idr5" data-id="5" value="2" <? echo ($idr[5] == 2 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					Sì, per una percentuale approssimativa del 50%
				</td>
				<td><input type="radio" name="idr5" data-id="5" value="3" <? echo ($idr[5] == 3 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					Sì, ma in minima parte
				</td>
				<td><input type="radio" name="idr5" data-id="5" value="4" <? echo ($idr[5] == 4 ? 'checked' : ''); ?> /></td>
			</tr>
			<tr>
				<td>
					No, il rischio rimane indifferente
				</td>
				<td><input type="radio" name="idr5" data-id="5" value="5" <? echo ($idr[5] == 5 ? 'checked' : ''); ?> /></td>
			</tr>
		</table>
	
	</div>

</div>