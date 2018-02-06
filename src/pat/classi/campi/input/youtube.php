<script language="JavaScript">
		// Get the HTTP Object
		function getHTTPObject(){
		    if (window.ActiveXObject)
		        return new ActiveXObject("Microsoft.XMLHTTP");
		    else if (window.XMLHttpRequest)
		        return new XMLHttpRequest();
		    else {
		        alert("Il tuo browser non supporta AJAX: non è possibile quindi utilizzare la feature.");
		        return null;
		    }
		}
		
		var httpObject = getHTTPObject();
		
		function createVideoUploadForm<?echo $nome; ?>(){
			var resultDiv = GetE('div_yt_new<?echo $nome; ?>');
			if (httpObject.readyState == 1) {
		      resultDiv.innerHTML = '<div style="margin-top:10px;">Attendere...</div>'; 
		      var divTipoOp = GetE('divTipoOp<?echo $nome; ?>');
		      divTipoOp.style.display = 'none';
		    } else if (httpObject.readyState == 4 && httpObject.status == 200) {
		      if (httpObject.responseText) {
		        resultDiv.innerHTML = httpObject.responseText;
		        showModalDialog('ytUpload.php?postUrl='+GetE('postUrl<?echo $nome; ?>').value+'&tokenValue='+GetE('tokenValue<?echo $nome; ?>').value+'&nome=<?echo $nome; ?>','YouTube Upload','dialogWidth:460px;dialogHeight:280px,menubar=no,toolbar=no,status=no,scrollbars=yes,resizable=no');void(0);
		      }
		    } else if (httpObject.readyState == 4) {
		      alert('Risposta ricevuta invalida - Stato: ' + httpObject.status);
		    }
		}
		
		function sendVideoData<? echo $nome; ?>(titolo,descrizione,keywords,categoria){
			if(titolo == '') {
				alert('Il titolo per il video YouTube è obbligatorio.');
				return;
			}
			if(descrizione == '') {
				alert('La descrizione per il video YouTube è obbligatoria.');
				return;
			}
			if(keywords == '') {
				alert('Le keywords per il video YouTube sono obbligatorie: inserirne almeno una.');
				return;
			}
			var filePath = 'ajax.php';
			var params = 'azione=sendVideoData' +
			             '&titolo=' + titolo +
			             '&descrizione=' + descrizione +
			             '&categoria=' + categoria +
			             '&keywords=' + keywords +
			             '&nome=<? echo $nome; ?>';
			             
			if (httpObject != null) {
				httpObject.open('POST', filePath);
				httpObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); 
				
				httpObject.onreadystatechange = createVideoUploadForm<?echo $nome; ?>;
				
				httpObject.send(params);
		    }
		
		}
		
		function tipologiaVideoYT<?echo $nome; ?>() {
			if(GetE('tipo_video_yt<?echo $nome; ?>').value == 'link'){
				GetE('div_yt_link_<?echo $nome; ?>').style.display = 'block';
				GetE('div_yt_new<?echo $nome; ?>').style.display = 'none';
			} else {
				GetE('div_yt_link_<?echo $nome; ?>').style.display = 'none';
				GetE('div_yt_new<?echo $nome; ?>').style.display = 'block';
			}
		}
	</script>
<?

// nominativo
if ($valoreVero == '') {
	if ($classeStr == '') {
		$styleStr = "style=\"width:100px !important;margin:0px 6px 0px 0px;\"";
	} else {
		$styleStr = "";
	}
	?>
		<div style="margin:10px;padding:5px;border:1px solid #333333;background-color: #F2F2F2;">
	<?
		include('./classi/campi/input/youtubeNew.php');
	?>
		</div>
	<?
} else { ?>
	
	<script language="JavaScript">
		function visualizzaDettagli<?echo $nome;?>(){
			if(GetE('dettagli<?echo $nome;?>').style.display == 'block') {
				GetE('dettagli<?echo $nome;?>').style.display = 'none';
				GetE('btnAggiorna<? echo $nome; ?>').style.display = 'none';
			} else {
				GetE('dettagli<?echo $nome;?>').style.display = 'block'
				GetE('btnAggiorna<? echo $nome; ?>').style.display = 'block';
			}
		}
		
		// Get the HTTP Object
		function getHTTPObject(){
		    if (window.ActiveXObject)
		        return new ActiveXObject("Microsoft.XMLHTTP");
		    else if (window.XMLHttpRequest)
		        return new XMLHttpRequest();
		    else {
		        alert("Il tuo browser non supporta AJAX: non è possibile quindi utilizzare la feature.");
		        return null;
		    }
		}
		
		var httpObject = getHTTPObject();
		
		function updateVideoUploadForm<?echo $nome; ?>(){
			var resultDiv = GetE('div_yt_log<?echo $nome; ?>');
		    var bottoneAggiorna = GetE('btnAggiorna<? echo $nome; ?>'); 
		    var bottoneElimina = GetE('btnElimina<? echo $nome; ?>'); 
			if (httpObject.readyState == 1) {
			  resultDiv.style.display = 'block';
			  bottoneAggiorna.style.display = 'none';
			  bottoneElimina.style.display = 'none';
		      resultDiv.innerHTML = '<div style="text-align:center;">Aggiornamento in corso: attendere...</div>';
		    } else if (httpObject.readyState == 4 && httpObject.status == 200) {
		      if (httpObject.responseText) {
		        resultDiv.innerHTML = '<div style="text-align:center;">Aggiornamento completato' + httpObject.responseText +'</div>';
		        bottoneAggiorna.style.display = 'block';
			    bottoneElimina.style.display = 'block';
		      }
		    } else if (httpObject.readyState == 4) {
		      	alert('Risposta ricevuta invalida - Stato: ' + httpObject.status);
		    }
		}
		
		function updateVideoData<? echo $nome; ?>(titolo,descrizione,keywords,categoria){
			var idVideo = '<?echo $valoreVero; ?>';
			if(titolo == '') {
				alert('Il titolo per il video YouTube è obbligatorio.');
				return;
			}
			if(descrizione == '') {
				alert('La descrizione per il video YouTube è obbligatoria.');
				return;
			}
			if(keywords == '') {
				alert('Le keywords per il video YouTube sono obbligatorie: inserirne almeno una.');
				return;
			}
			var filePath = 'ajax.php';
			var params = 'azione=updateVideoData' +
			             '&titolo=' + titolo +
			             '&descrizione=' + descrizione +
			             '&categoria=' + categoria +
			             '&keywords=' + keywords +
			             '&id=' + idVideo;
			             
			if (httpObject != null) {
				httpObject.open('POST', filePath);
				httpObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); 
				
				httpObject.onreadystatechange = updateVideoUploadForm<?echo $nome; ?>;
				
				httpObject.send(params);
		    }
		}
		
		function deleteVideoUploadForm<?echo $nome; ?>(){
			var resultDiv = GetE('div_yt_<?echo $nome; ?>');
		    var bottoneAggiorna = GetE('btnAggiorna<? echo $nome; ?>'); 
		    var bottoneElimina = GetE('btnElimina<? echo $nome; ?>');
			if (httpObject.readyState == 1) {
			  bottoneAggiorna.style.display = 'none';
			  bottoneElimina.style.display = 'none';
			  var logDiv = GetE('div_yt_log<?echo $nome; ?>');
		      logDiv.innerHTML = '<div style="text-align:center;">Eliminazione in corso: attendere...</div>';
		      logDiv.style.display = 'block';
		    } else if (httpObject.readyState == 4 && httpObject.status == 200) {
		      if (httpObject.responseText) {
		        resultDiv.innerHTML = httpObject.responseText;
		        
		      }
		    } else if (httpObject.readyState == 4) {
		      	alert('Risposta ricevuta invalida - Stato: ' + httpObject.status);
		    }
		}
		
		function deleteVideoData<? echo $nome; ?>(){
			var idVideo = '<?echo $valoreVero; ?>';
			if(confirm('Il video verrà eliminato da YouTube: continuare?')) {
				var filePath = 'ajax.php';
				var params = 'azione=deleteVideoData' +
							 '&nome=<? echo $nome; ?>' +
				             '&id=' + idVideo;
				             
				if (httpObject != null) {
					httpObject.open('POST', filePath);
					httpObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); 
					
					httpObject.onreadystatechange = deleteVideoUploadForm<?echo $nome; ?>;
					httpObject.send(params);
			    }
			} else {
				return;
			}
		}
		
		function deleteInternalVideoUploadForm<?echo $nome; ?>(){
			var resultDiv = GetE('div_yt_<?echo $nome; ?>');
		    var bottoneElimina = GetE('btnEliminaEsterno<? echo $nome; ?>');
			if (httpObject.readyState == 1) {
			  resultDiv.style.display = 'block';
			  bottoneElimina.style.display = 'none';
		    } else if (httpObject.readyState == 4 && httpObject.status == 200) {
		      if (httpObject.responseText) {
		        resultDiv.innerHTML = httpObject.responseText;
		        //var videoDiv = GetE('divVideo<?echo $nome; ?>');
		        //videoDiv.style.display = 'none';
		      }
		    } else if (httpObject.readyState == 4) {
		      	alert('Risposta ricevuta invalida - Stato: ' + httpObject.status);
		    }
		}
		
		function deleteInternalVideoData<? echo $nome; ?>(){
			var idVideo = '<?echo $valoreVero; ?>';
			if(confirm('Il video verrà eliminato dal sistema: continuare?')) {
				var filePath = 'ajax.php';
				var params = 'azione=deleteInternalVideoData' +
							 '&id=' + idVideo + 
							 '&nome=<? echo $nome; ?>';
				             
				if (httpObject != null) {
					httpObject.open('POST', filePath);
					httpObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); 
					
					httpObject.onreadystatechange = deleteInternalVideoUploadForm<?echo $nome; ?>;
					httpObject.send(params);
			    }
			} else {
				return;
			}
		}
	</script>
	
<?	
	$entry = getYouTubeVideoEntry($valoreVero, true);
	if($entry instanceof Zend_Gdata_YouTube_VideoEntry) {
		if ($entry->getEditLink() !== null) {
			$proprietario = true;
			$readOnly = '';
		} else {
			$proprietario = false;
			$readOnly = ' readonly="readonly" ';
		}
	} else {
		//video non più disponibile
		$proprietario = false;
		$readOnly = ' readonly="readonly" ';
	}

    if (count($entry->mediaGroup->thumbnail) > 0) {
        $thumbnailUrl = htmlspecialchars(
            $entry->mediaGroup->thumbnail[0]->url);
    }
    
	?>
	<div id="div_yt_<?echo $nome; ?>" style="margin:10px;padding:5px;border:1px solid #333333;background-color: #F2F2F2;">
		<div id="divVideo<?echo $nome; ?>">
			<table class="txtBluBold10" width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="29" height="19"> 
		            	<div align="center"><img src="grafica/learning/freccetta.gif" width="4" height="6" align="absmiddle" hspace="6"></div>
		          	</td>
		          	<td width="200" height="28">Anteprima</td>
		          	<td height="19">
		          		<?
							if($entry instanceof Zend_Gdata_YouTube_VideoEntry) {
								$alt = utf8_decode(stripslashes(htmlspecialchars($entry->getVideoTitle())));
							} else {
								$alt = "Video non disponibile.";
							}
						?>
						<img src="<?echo $thumbnailUrl?>" onclick="visualizzaDettagli<?echo $nome;?>('<?echo $valoreVero;?>');" alt="<?echo $alt;?>"/>
						<input type="hidden" name="<?echo $nome; ?>" id="<?echo $nome; ?>" value="<?echo $valoreVero; ?>" />		
					</td>
				</tr>
			</table>
			<?
				if($entry instanceof Zend_Gdata_YouTube_VideoEntry and $entry->videoState->name and $entry->videoState->name != 'processing' ) {
			?>
				<table class="txtBluBold10" width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="29" height="19"> 
			            	<div align="center"><img src="grafica/learning/freccetta.gif" width="4" height="6" align="absmiddle" hspace="6"></div>
			          	</td>
			          	<td width="200" height="28">Stato</td>
			          	<td height="19">
			          		ATTENZIONE: il video non è stato accettato e non sarà disponibile in navigazione. (State: <? echo $entry->videoState->name; ?>)		
						</td>
					</tr>
				</table>
			<? } ?>	
			<table id="dettagli<?echo $nome;?>" class="txtBluBold10" width="100%" border="0" cellspacing="0" cellpadding="0" style="display:none;">
				<tr>
					<td width="29" height="19"> 
		            	<div align="center"><img src="grafica/learning/freccetta.gif" width="4" height="6" align="absmiddle" hspace="6"></div>
		          	</td>
		          	<td width="200" height="28">Titolo</td>
		          	<td height="19">
		          	<? if($proprietario) { ?>
		          		<input type="text" <?echo $classeStr . $readOnly ; ?> name="titolo<? echo $nome; ?>" id="titolo<? echo $nome; ?>" value="<?echo utf8_decode(stripslashes(htmlspecialchars($entry->getVideoTitle()))); ?>"/>		
					<? } else {
						if($entry instanceof Zend_Gdata_YouTube_VideoEntry) {
							echo utf8_decode(stripslashes(htmlspecialchars($entry->getVideoTitle())));
						} else {
							echo "Video non disponibile.";
						}
					} ?>
					</td>
				</tr>
				<tr>
					<td width="29" height="19"> 
		            	<div align="center"><img src="grafica/learning/freccetta.gif" width="4" height="6" align="absmiddle" hspace="6"></div>
		          	</td>
		          	<td width="200" height="28">Descrizione</td>
		          	<td height="19" >
		          	<? if($proprietario) { ?>
		          		<input type="text" <?echo $classeStr . $readOnly ; ?> name="descrizione<? echo $nome; ?>" id="descrizione<? echo $nome; ?>" value="<?echo utf8_decode(stripslashes(htmlspecialchars($entry->getVideoDescription()))); ?>" />
					<? } else {
						if($entry instanceof Zend_Gdata_YouTube_VideoEntry) {
							echo utf8_decode(stripslashes(htmlspecialchars($entry->getVideoDescription())));
						} else {
							echo "Video non disponibile.";
						}
					} ?>
					</td>
				</tr>
				<tr>
					<td width="29" height="19"> 
		            	<div align="center"><img src="grafica/learning/freccetta.gif" width="4" height="6" align="absmiddle" hspace="6"></div>
		          	</td>
		          	<td width="200" height="28">Categoria</td>
		          	<td height="19" colspan="2">
		          		<?
		          		if($proprietario) {
							creaOggettoFormPers("select", "categoria".$nome, 
								"Autos,Music,Animals,Sports,Travel,Games,Comedy,People,News,Entertainment,Education,Howto,Nonprofit,Tech", $entry->getVideoCategory() ,
								"Auto e veicoli,Musica,Animali,Sport,Viaggi ed eventi,Giochi,Umorismo,Persone e blog,Notizie e politica,Intrattenimento,Istruzione,Fai da te e stile,Non profit e attivismo,Scienze e tecnologie"
								,""); 
		          		} else {
		          			if($entry instanceof Zend_Gdata_YouTube_VideoEntry) {
			          			echo $entry->getVideoCategory();
							} else {
								echo "Video non disponibile.";
							}
		          		}
						?>
					</td>
				</tr>
				<tr>
					<td width="29" height="19"> 
		            	<div align="center"><img src="grafica/learning/freccetta.gif" width="4" height="6" align="absmiddle" hspace="6"></div>
		          	</td>
		          	<td width="200" height="28">Tags (separati da virgole)</td>
		          	<td height="19">
		          	<?  
		          		$keywords = '';
		          		if($entry instanceof Zend_Gdata_YouTube_VideoEntry) {
			          		foreach ( (array)$entry->getVideoTags() as $key => $value ) {
			          			if($keywords == '')
									$keywords = $value;
								else
									$keywords .= ", ".$value;
							}
						}
		          	?>
		          	<? if($proprietario) { ?>
		          		<input type="text" <?echo $classeStr . $readOnly ; ?> name="keywords<? echo $nome; ?>" id="keywords<? echo $nome; ?>" value="<?echo utf8_decode(stripslashes($keywords)); ?>"/>
					<? } else {
						echo utf8_decode(stripslashes(htmlspecialchars($keywords)));
					} ?>
					</td>
				</tr>
				<tr>
					<td width="29" height="19"> 
		            	<div align="center"><img src="grafica/learning/freccetta.gif" width="4" height="6" align="absmiddle" hspace="6"></div>
		          	</td>
		          	<td width="200" height="28">Durata</td>
		          	<td height="19">
						<?
						if($entry instanceof Zend_Gdata_YouTube_VideoEntry) {
							$durata = htmlspecialchars($entry->getVideoDuration());
							$min = intval($durata / 60);
							$sec = intval($durata % 60);
							if($sec < 10) {
								$sec = "0".$sec;
							}
							$durata = $min.":".$sec;
						} else {
							$durata = '';
						}
						echo $durata; 
						?>		
					</td>
				</tr>
				<tr>
					<td width="29" height="19"> 
		            	<div align="center"><img src="grafica/learning/freccetta.gif" width="4" height="6" align="absmiddle" hspace="6"></div>
		          	</td>
		          	<td width="200" height="28">Visualizzazioni</td>
		          	<td height="19">
						<?
						if($entry instanceof Zend_Gdata_YouTube_VideoEntry) {
							echo htmlspecialchars($entry->getVideoViewCount()); 
						} else {
							echo "";
						}
						?>		
					</td>
				</tr>
			</table>
			<table>
				<tr>
			        <td width="160" height="19">
			        	<div id="btnAggiorna<? echo $nome; ?>" style="display:none;">
			        		<? if($proprietario) { ?>
								<a id="bottoneSalva" href="javascript:updateVideoData<? echo $nome; ?>(GetE('titolo<? echo $nome; ?>').value,GetE('descrizione<? echo $nome; ?>').value,GetE('keywords<? echo $nome; ?>').value,GetE('categoria<? echo $nome; ?>').value);">
									<img src="grafica/admin_skin/<? echo $datiUser['admin_skin']; ?>/up.gif" class="nobordo" alt="Aggiorna i valori su YouTube">
									Aggiorna i valori su YouTube
								</a>
							<? } ?>
						</div>
					</td>
			        <td width="160" height="19"> 
			        	<div id="btnElimina<? echo $nome; ?>">
			        		<? if($proprietario) { ?>
								<a id="bottoneSalva" href="javascript:deleteVideoData<? echo $nome; ?>();">
									<img src="grafica/admin_skin/<? echo $datiUser['admin_skin']; ?>/cestino.gif" class="nobordo" alt="Elimina il video da YouTube">
									Elimina il video da YouTube
								</a>
							<? }?>
						</div>
					</td>
					<td width="160" height="19">
						<div id="btnEliminaEsterno<? echo $nome; ?>">
			        		<? if(!$proprietario) { ?>
								<a id="bottoneSalva" href="javascript:deleteInternalVideoData<? echo $nome; ?>();">
									<img src="grafica/admin_skin/<? echo $datiUser['admin_skin']; ?>/cestino.gif" class="nobordo" alt="Rimuovi il video dal sistema">
									Rimuovi il video dal sistema
								</a>
							<? }?>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<div id="div_yt_log<?echo $nome; ?>" style="margin:10px;padding:5px;border:1px solid #333333;background-color: #F2F2F2;display:none;">
		
		</div>
	</div>	
	<?
}
?>
