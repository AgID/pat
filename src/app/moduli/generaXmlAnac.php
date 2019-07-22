<script type="text/javascript">
var bandi = new Array();
var processed = '';
var i=0;
var dots=0;
<?php
$passo = 10;
$filename = mktime().'-'.$idEnte.'-'.forzaNumero($_POST['anno']).'.temp.xml';

$records = $opOgg->creaXML_3_0($filename);
$tmpRec = array();
foreach((array)$records as $r) {
	$tmpRec[] = $r['id'];
	if(count($tmpRec)>=$passo) {
		$elem = implode(',',$tmpRec);
		$tmpRec = array();
		?>
		bandi[bandi.length] = '<?php echo $elem; ?>';
		<?
	}
}
if(count($tmpRec)>0) {
	$elem = implode(',',$tmpRec);
	$tmpRec = array();
	?>
	bandi[bandi.length] = '<?php echo $elem; ?>';
	<?php 
}
?>
var totale = <? echo count($records); ?>;

jQuery(document).ready(function() {
	console.log(bandi);
	console.log(totale);

	if(bandi.length>0) {
		console.log('generaXmlAnac: '+0);
		generaXmlAnac(0);
	} else {
		spostaFileXml();
		console.log('generazione terminata. spostamento...');
	}
	setInterval(function(){
		jQuery('#sp-att-dots').html(jQuery('#sp-att-dots').html()+'.');
		dots++;
		if(dots > 10) {
			dots = 0;
			jQuery('#sp-att-dots').html('');
		}
	},700);

});

function chiamataTerminata(index) {
	if(index+1<bandi.length) {
		console.log('generaXmlAnac: '+(index+1));
		generaXmlAnac(index+1);
	} else {
		spostaFileXml();
		console.log('generazione terminata. spostamento...');
	}
}
function generaXmlAnac(index) {
	v = ((index+1)*<?echo $passo; ?>);
	if(v>totale) {
		v = totale;
	}
	jQuery('#sp-elab').html(v);
	
	jQuery.ajax({
		url: 'ajax.php',
		type: 'post',
		async: true,
		dataType: 'json',
		data: {
			'azione': 'generaXmlAnac', 
			'ids': bandi[index], 
			'processed': processed, 
			'file': '<?php echo $filename; ?>', 
			'anno': <?php echo forzaNumero($_POST['anno']); ?>, 
			'id_stazione': '<?php echo forzaNumero($_POST['id_stazione']); ?>'
		},
		success: function(data) {
			console.log('data.processed: '+data.processed);
			processed = data.processed;
			chiamataTerminata(index);
		},
		error: function(xhr, desc, err) {
			console.log(xhr);
			console.log("Details: " + desc + "\nError:" + err);
			alert('Errore nella generazione del file. Contattare l\'assistenza tecnica.');
		}
	});
}
function spostaFileXml() {
	jQuery.ajax({
		url: 'ajax.php',
		type: 'post',
		async: true,
		dataType: 'json',
		data: {
			'azione': 'spostaXmlAnac', 
			'file': '<?php echo $filename; ?>', 
			'anno': <?php echo forzaNumero($_POST['anno']); ?>, 
			'id_stazione': '<?php echo forzaNumero($_POST['id_stazione']); ?>'
		},
		success: function(data) {
			jQuery('#sp-attendere').hide();
			jQuery('#sp-att-dots').hide();
			jQuery('#div-fine').show();
			if(!data.esito) {
				alert('Errore nello spostamento del file. Contattare l\'assistenza tecnica.');
			}
		},
		error: function(xhr, desc, err) {
			console.log(xhr);
			console.log("Details: " + desc + "\nError:" + err);
			alert('Errore nella generazione del file. Contattare l\'assistenza tecnica.');
		}
	});
}
</script>
<?php 
//lognormale(count($records));
?>
<div class="alert alert-block" style="margin-top:20px;">
	<span class="shortcuts-iconpat iconpat-desktop-size-class iconpat-warning" style="display: block; float:left; padding: 0px 20px 0px 0px;"></span>
	<div style="padding-left: 80px;">
		<h4>Informazioni di sistema</h4>
		<p style="margin: 8px 0;color:#646464;">
			<span id="sp-attendere">Generazione del file XML per ANAC in corso. <strong>ATTENDERE IL CARICAMENTO DELL'OPERAZIONE SENZA ABBANDONARE LA PAGINA.</strong></span>
		</p>
		<p style="margin: 8px 0;color:#646464;">
			Elaborazione di <span id="sp-elab" style="font-weight: bold;">0</span> elementi su <span id="sp-totale" style="font-weight: bold;"><? echo count($records); ?></span> in corso<strong><span id="sp-att-dots"></span></strong>
		</p>
		<div id="div-fine" style="display: none;">
			<p style="margin: 8px 0;color:#646464;">
				<span id="sp-elab-end" style="font-weight: bold;">Elaborazione terminata. <a href="admin__pat.php?menu=pubblicazioni&amp;menusec=avcp">Aggiorna le informazioni sul file</a></span>
			</p>
		</div>
	</div>
</div>