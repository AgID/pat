
	<div id="divTipoOp<?echo $nome; ?>">
		<table class="txtBluBold10" width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="29" height="19"> 
	            	<div align="center"><img src="grafica/learning/freccetta.gif" width="4" height="6" align="absmiddle" hspace="6"></div>
	          	</td>
	          	<td width="200" height="28">Tipo di operazione</td>
	          	<td height="19" colspan="2">
					<?
						creaOggettoFormPers("select", "tipo_video_yt".$nome, "link,upload", "upload" ,"Inserisci link diretto,Upload nuovo video","onChange=\"tipologiaVideoYT".$nome."();\""); 
					?>	
				</td>
			</tr>
		</table>
	</div>
	<div id="div_yt_link_<?echo $nome; ?>" style="display:none;" >
		<div style="margin-left:12px;">Inserisci l'ID del video YouTube</div>
		<table class="txtBluBold10" width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="29" height="19"> 
	            	<div align="center"><img src="grafica/learning/freccetta.gif" width="4" height="6" align="absmiddle" hspace="6"></div>
	          	</td>
	          	<td width="200" height="28">ID video</td>
	          	<td height="19" colspan="2">
					<input type="text" <?echo $classeStr; ?> name="<? echo $nome; ?>" id="<? echo $nome; ?>"  />
				</td>
			</tr>
		</table>
	</div>
	<div id="div_yt_new<?echo $nome; ?>">
		<div style="margin-left:12px;">Dati per il video su YouTube</div>
		<table class="txtBluBold10" width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="29" height="19"> 
	            	<div align="center"><img src="grafica/learning/freccetta.gif" width="4" height="6" align="absmiddle" hspace="6"></div>
	          	</td>
	          	<td width="200" height="28">Titolo</td>
	          	<td height="19" colspan="2">
					<input type="text" <?echo $classeStr; ?> name="titolo<? echo $nome; ?>" id="titolo<? echo $nome; ?>"  />
				</td>
			</tr>
			<tr>
				<td width="29" height="19"> 
	            	<div align="center"><img src="grafica/learning/freccetta.gif" width="4" height="6" align="absmiddle" hspace="6"></div>
	          	</td>
	          	<td width="200" height="28">Descrizione</td>
	          	<td height="19" colspan="2">
					<input type="text" <?echo $classeStr; ?> name="descrizione<? echo $nome; ?>" id="descrizione<? echo $nome; ?>" />
				</td>
			</tr>
			<tr>
				<td width="29" height="19"> 
	            	<div align="center"><img src="grafica/learning/freccetta.gif" width="4" height="6" align="absmiddle" hspace="6"></div>
	          	</td>
	          	<td width="200" height="28">Categoria</td>
	          	<td height="19" colspan="2">
	          		<?
						creaOggettoFormPers("select", "categoria".$nome, 
							"Autos,Music,Animals,Sports,Travel,Games,Comedy,People,News,Entertainment,Education,Howto,Nonprofit,Tech", "" ,
							"Auto e veicoli,Musica,Animali,Sport,Viaggi ed eventi,Giochi,Umorismo,Persone e blog,Notizie e politica,Intrattenimento,Istruzione,Fai da te e stile,Non profit e attivismo,Scienze e tecnologie"
							,""); 
					?>
				</td>
			</tr>
			<tr>
				<td width="29" height="19"> 
	            	<div align="center"><img src="grafica/learning/freccetta.gif" width="4" height="6" align="absmiddle" hspace="6"></div>
	          	</td>
	          	<td width="200" height="28">Tags (separati da virgole)</td>
	          	<td height="19" colspan="2">
					<input type="text" <?echo $classeStr; ?> name="keywords<? echo $nome; ?>" id="keywords<? echo $nome; ?>" />
				</td>
			</tr>
			<tr>
				<td width="29" height="19"></td>
		        <td width="160" height="19"></td>
		        <td height="19"> 
					<div>
					<a id="bottoneSalva" href="javascript:sendVideoData<? echo $nome; ?>(GetE('titolo<? echo $nome; ?>').value,GetE('descrizione<? echo $nome; ?>').value,GetE('keywords<? echo $nome; ?>').value,GetE('categoria<? echo $nome; ?>').value);">
						<img src="grafica/admin_skin/<? echo $datiUser['admin_skin']; ?>/up.gif" class="nobordo" alt="Invia i dati a YouTube">
						Invia i dati a YouTube
					</a>
					</div>
				</td>
			</tr>
		</table>
	</div>