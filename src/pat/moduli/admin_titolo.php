<div class="pageheader">
	<form action="" method="post" class="searchbar">
		<input id="edit-search-block-form" type="text" name="keyword" placeholder="Cerca una informazione" />
	</form>
	<div class="pageicon"><span class="<? echo $funzioneMenu['iconaGrande']; ?>"></span></div>
	<div class="pagetitle">
		<h5><? 
			if (!is_array($funzioneSottoMenu) or !count($funzioneSottoMenu)) {
				echo $funzioneMenu['descrizione']; 
			} else { 
				echo $funzioneSottoMenu['descrizione']; 
			} ?>
		</h5>
		
		<h1><? 
			if (!is_array($funzioneSottoMenu) or !count($funzioneSottoMenu)) {
				echo $funzioneMenu['nomePagina']; 
			} else { 
				echo $funzioneSottoMenu['nomePagina']; 
			} ?>
		</h1>
		
	</div>
</div><!--pageheader-->

<script type="text/javascript">
<!--
var jSearch = jQuery.noConflict();
jSearch(document).ready(function($) {
	var cache = {};
	
	function monkeyPatchAutocomplete() {
		var oldFn = $.ui.autocomplete.prototype._renderItem;
		$.ui.autocomplete.prototype._renderItem = function( ul, item) {
 
			//var t = String(item.value).replace(new RegExp(this.term, "gi"),"<span class='ui-state-highlight'>$&</span>");
			var t = String(item.value).replace(new RegExp(this.term, "gi"),"<span style='background-color: #FFEE00; color: #333333;'><strong>$&</strong></span>");
			return $( "<li></li>" ).data( "item.autocomplete", item ).append( "<a>" + t + "</a>" ).appendTo( ul );
		};
	}

	monkeyPatchAutocomplete();

	$.widget( "custom.catcomplete", $.ui.autocomplete, {
		_renderMenu: function( ul, items ) {
			var that = this;
			$.each( items, function( index, item ) {
				if ( item.objName == 1 ) {
					ul.append( "<li class=\"titoloRicercaAdmin\">" + item.label + "</li>" );
				} else if ( item.objName == 2 ) {
					ul.append( "<li><a href=\""+item.url+"\"><b>" + item.label + "</b></a></li>" );
				} else {
					if (item.label != '' && item.url != '') {
						that._renderItemData( ul, item );
					}
				}
			});
		}
	});
	
	$("#edit-search-block-form").catcomplete({
		source: function( request, response ) {
			var term = request.term;
			if ( term in cache ) {
				response(cache[term]);
				return;
			}
			$.getJSON( "ajaxPers.php?azione=cercaAutoAdmin", request, function( data, status, xhr ) {
				cache[term] = data;
				response(data);
			});
		},
		search: function() {
			if (this.value.length < 3) {
				return false;
			}
			$(this).addClass("wait");
		},
		select: function(event, ui) {
			if(ui.item.url) {
				location.href = ui.item.url;
			}
			return false;
		},
		position: { 
			my : "right top", 
			at: "right bottom" 
		},
		response: function(event, ui){
			$(this).removeClass("wait");
		}
	});
	
	$( "#infoRicerca" ).tooltip({
		items: "a",
		content: function() {
			var element = $( this );
			if ( element.is( "a" ) ) {
				return $('#tip1').html();
			}
		}
	});
	
});
-->
</script>