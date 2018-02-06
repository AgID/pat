<?
			// inserisco il codice javascript necessario
			if($classe > 0) {
				$idPartenza = $classe;
			} else {
				$idPartenza = 0;
			}
			
			if($condizioneAgg > 0) {
				$categoriaLimite = $condizioneAgg;
			} else {
				$categoriaLimite = 0;
			}
			
			echo "<script type=\"text/javascript\">
			function immagineScelta".$nome."(valore) {
				immagineId = document.getElementById('".$nome."');
				immagineId.value = valore; 
				navigazione.close();
				UpdatePreview".$nome."();
				".$evento.";
			}
			function seleziona".$nome."() {
				navigazione = window.open('navigazione_immagini.php?id=".$idPartenza."&catLimite=".$categoriaLimite."&campo=".$nome."','immagini','height=600,width=520,toolbar=no,scrollbars=yes,status=yes');
                                
                                if(window.focus){
                                    navigazione.focus();
                                }
			}
			function UpdatePreview".$nome."() {
				UpdateImage".$nome."( document.getElementById('".$nome."Prev'), true ) ;
			}
			function UpdateImage".$nome."( e, skipId ) {
				//alert('devo aprire immagine: '+\"moduli/output_immagine.php?id=\"+document.getElementById('".$nome."').value);	
				e.src = \"moduli/output_immagine.php?id=\"+document.getElementById('".$nome."').value ;
			}
			</script>";
                        $valore = 0;
			if ($valoreVero != '') {
				$valore = $valoreVero;			
			} 
			// inserisco immagine di preview
			echo "<img src=\"moduli/output_immagine.php?id=".$valore."\" id=\"".$nome."Prev\" width=\"60\" border=\"1\" align=\"absmiddle\" hspace=\"12\" vspace=\"6\">";
			
			// inserisco il campo nascosto
			echo "<input onLoad=\"UpdatePreview".$nome."();\" onChange=\"UpdatePreview();\" onBlur=\"UpdatePreview".$nome."();\" style=\"display:none;\" id=\"".$nome."\" class=\"stileForm\" type=\"text\" name=\"".$nome."\" value=\"".$valore."\">";
			// inserisco pulsante di scelta immagine
			echo "
			        <a class=\"bottoneClassico\" title=\"Scegli il media\" href=\"javascript:seleziona".$nome."();\">
				<img src=\"grafica/admin_skin/classic/info_multimedia.gif\" alt=\"Scegli il media\" />Scegli media</a>
			";
?>
