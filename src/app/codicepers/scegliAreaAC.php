<?php
/*
 * Created on 27/nov/2015
 */

//costruisco il contenuto della modale
?>
<div id="contenutoAreePredefinite" style="display:none;">

	<div id="cbox_content_<? echo $parametri['id_campo']; ?>">
		<table class="table table-bordered">
			<thead><tr>
				<th>Aree predefinite</th>
				<th></th>
			</tr></thead>
			<tr>
				<td id="areaAC1">
					Acquisizione e progressione del personale
				</td>
				<td><a class="scegli_area_ac btn btn-rounded" data-id="1">Scegli</a></td>
			</tr>
			<tr>
				<td id="areaAC2">
					Affidamento di lavori, servizi e forniture
				</td>
				<td><a class="scegli_area_ac btn btn-rounded" data-id="2">Scegli</a></td>
			</tr>
			<tr>
				<td id="areaAC3">
					Provvedimenti ampliativi della sfera giuridica dei destinatari privi di effetto economico diretto ed immediato per il destinatario
				</td>
				<td><a class="scegli_area_ac btn btn-rounded" data-id="3">Scegli</a></td>
			</tr>
			<tr>
				<td id="areaAC4">
					Provvedimenti ampliativi della sfera giuridica dei destinatari con effetto economico diretto ed immediato per il destinatario
				</td>
				<td><a class="scegli_area_ac btn btn-rounded" data-id="4">Scegli</a></td>
			</tr>
		</table>
	</div>

</div>