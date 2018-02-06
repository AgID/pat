jQuery.noConflict();

jQuery(document).ready(function(){
	
	// effetto sottomenu nel menu a sinistra
	jQuery('.leftmenu .dropdown > a').click(function(){
		if(!jQuery(this).next().is(':visible'))
			jQuery(this).next().slideDown('fast');
		else
			jQuery(this).next().slideUp('fast');	
		return false;
	});
	
	if(jQuery.uniform) 
	   jQuery('input:checkbox, input:radio, .uniform-file').uniform();
		
	if(jQuery('.widgettitle .close').length > 0) {
		  jQuery('.widgettitle .close').click(function(){
					 jQuery(this).parents('.widgetbox').fadeOut(function(){
								jQuery(this).remove();
					 });
		  });
	}
	
	
   // Aggiungi barra menu per telefonini e tablet
   jQuery('<div class="topbar"><a class="barmenu">'+
		    '</a></div>').insertBefore('.mainwrapper');
	
	jQuery('.topbar .barmenu').click(function() {
		  
		  var lwidth = '260px';
		  if(jQuery(window).width() < 340) {
					 lwidth = '240px';
		  }
		  
		  if(!jQuery(this).hasClass('open')) {
					 jQuery('.rightpanel, .headerinner, .topbar').css({marginLeft: lwidth},'fast');
					 jQuery('.logo, .leftpanel').css({marginLeft: 0},'fast');
					 jQuery(this).addClass('open');
		  } else {
					 jQuery('.rightpanel, .headerinner, .topbar').css({marginLeft: 0},'fast');
					 jQuery('.logo, .leftpanel').css({marginLeft: '-'+lwidth},'fast');
					 jQuery(this).removeClass('open');
		  }
	});
	
	// mostra/nascondi menu di sinistra
	jQuery(window).resize(function(){
		  if(!jQuery('.topbar').is(':visible')) {
		         jQuery('.rightpanel, .headerinner').css({marginLeft: '260px'});
					jQuery('.logo, .leftpanel').css({marginLeft: 0});
		  } else {
		         jQuery('.rightpanel, .headerinner').css({marginLeft: 0});
					jQuery('.logo, .leftpanel').css({marginLeft: '-260px'});
		  }
   });
	
	// menu a discesa per la foto profilo
	jQuery('.userloggedinfo img').click(function(){
		  if(jQuery(window).width() < 480) {
					 var dm = jQuery('.userloggedinfo .userinfo');
					 if(dm.is(':visible')) {
								dm.hide();
					 } else {
								dm.show();
					 }
		  }
   });
			  
	
});

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

function setOggettiWebservice(){
    if(httpObject.readyState == 4 ){
        document.getElementById('div_oggetto_ws').innerHTML = httpObject.responseText;
    }
}

function getOggettiWebservice(){
    if (httpObject != null) {
        var call = "./ajax.php?azione=getOggettiWebservice&idServer="+document.getElementById('server_ws').selectedIndex;
        httpObject.open("GET", call, true);
        httpObject.onreadystatechange = setOggettiWebservice;
        httpObject.send(null);
    }

}

function showAttendere(){
    var fo = new FlashObject("grafica/admin/loading.swf", "fotester", "32", "32", "8", "#FFFFFF");
    fo.write("contentDivAttendere");
    document.getElementById("divAttendere").style.display = "block";
}
function ripristinaBackup(redirect){
    if(confirm("ATTENZIONE: I dati presenti attualmente verranno ripristinati con quelli del backup selezionato. Procedere?")){
        showAttendere();
        location = redirect;
    } else {
        return;
    }
}

function refreshPagina() {
        //opener.location.reload();
         opener.location = 'index.php?id_sezione=<? echo $idSezione; ?>';
}
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
function apriFinestraHelp(pagina) { 
	//strAttr = "status:no;dialogWidth:540px;dialogHeight:420px;scroll:1";' '
	//help = showModalDialog(pagina, null, strAttr);
	//window.open(pagina,'','')"; 
}
function visualizzAdmin(oggetto,out) {
	//alert('devo operare su admin: sono '+out);
	//alert('Apro amministrazione: '+oggetto);
	if (document.getElementById(oggetto)) {
		var visualizzata = document.getElementById(oggetto).style.display; 
		if (visualizzata == "block" || out != "in" ) {
			document.getElementById(oggetto).style.display = "none";  
		} else {
			document.getElementById(oggetto).style.display = "block";
			document.getElementById(oggetto).style.zIndex=9999;  
			// devo aumentare anche il livello dell'elemento che lo contiete

			//alert('Il livello di questo pannello è: '+document.getElementById(oggetto).style.zIndex);
		}
		
	}
}    
// il controllo degli eventi js viene sempre inserito anche se non utilizzato nelle funzioni normali accessibili
function addEvent(obj, type, fn ) {
	//alert ('aggiungo evento su: '+fn);
	if ( obj.attachEvent ) {
		obj['e'+type+fn] = fn;
		obj[type+fn] = function(){obj['e'+type+fn]( window.event );}
		obj.attachEvent( 'on'+type, obj[type+fn] );
	} else {
		obj.addEventListener( type, fn, false );
	}
}
function removeEvent( obj, type, fn ) {
	if ( obj.detachEvent ) {
		obj.detachEvent( 'on'+type, obj[type+fn] );
		obj[type+fn] = null;
	} else {
		obj.removeEventListener( type, fn, false );
	}
}

function lista_push(lista, testo, valore) {
	// inserisco una nuova voce in un select
	lista.options[lista.options.length] = new Option(testo, valore);
}