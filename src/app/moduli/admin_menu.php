<!--INIZIO MENU PRINCIPALE-->
<div class="leftmenu">        
	<ul class="nav nav-tabs nav-stacked">
		<li class="nav-header">Amministrare <? echo $configurazione['denominazione_trasparenza']; ?></li>
		
		<? // VERIFICO QUALI FUNZIONI ELABORARE NEL MENU 
		foreach ($funzioniMenu as $menuTemp) { 
			if ($menuTemp['nomeMenu'] != '') {
				// verifico se questa è la sezione attiva
				$classeAct = '';
				if ($menuTemp['menu'] == $menu) {
					$classeAct = 'active';
				}
				
				// pubblico il pulsante
				if (is_array($menuTemp['sottoMenu']) AND count($menuTemp['sottoMenu'])) {
					// pulsante ad apertura
					echo "<li class=\"dropdown ".$classeAct."\"><a href=\"\"><span class=\"".$menuTemp['iconaPiccola']."\"></span> ".$menuTemp['nomeMenu']."</a>";
					if ($menuTemp['menu'] == $menu) {
						echo "<ul style=\"display:block;\">";
					} else {
						echo "<ul>";
					}
					foreach ($menuTemp['sottoMenu'] as $sottoSezTemp) { 
						// verifico se questa è la sezione attiva
						$classeActSec = '';
						if ($sottoSezTemp['menuSec'] == $menuSecondario) {
							$classeActSec = 'active';
						}
						echo "<li class=\"".$classeActSec."\"><a href=\"admin__pat.php?menu=".$menuTemp['menu']."&amp;menusec=".$sottoSezTemp['menuSec']."\">".$sottoSezTemp['nomeMenu']."</a></li>";
					}
					echo "</ul></li>";
				} else {
					// pulsante diretto
					echo "<li class=\"".$classeAct."\"><a href=\"admin__pat.php?menu=".$menuTemp['menu']."\"><span class=\"".$menuTemp['iconaPiccola']."\"></span> ".$menuTemp['nomeMenu']."</a></li>";
				}
			}
		}
		?>
	</ul>
</div>
<!--FINE MENU PRINCIPALE-->