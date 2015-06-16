{{mb_script module=ssr script=planning}}
{{mb_script module=ssr script=planification}}
{{mb_script module=ssr script=modal_validation}}
{{mb_script module=system script=alert}}

<script>

PlanningEvent.onDblClic = function(event) {
  var sejour_id = event.className.match(/CSejour-([0-9]+)/)[1];
  var match_equipement_id = event.className.match(/CEquipement-([0-9]+)/);
  var equipement_id = match_equipement_id ? match_equipement_id[1] : '';

  // refresh du planning du patient concerné
  Planification.refreshSejour(sejour_id, false);
  
  // refresh du planning de l'equipement concerné
  equipement_id ? PlanningEquipement.show(equipement_id, sejour_id) : PlanningEquipement.hide();
  
  $('planning-technicien').select('.elt_selected').invoke('removeClassName', 'elt_selected');
  event.addClassName('elt_selected');
};
	
Planification.onCompleteShowWeek = function(){
  updatePlanningKineBoard();
  BoardSejours.update();

  PlanningEquipement.hide();
  $('planning-sejour').update('');
};

updatePlanningKineBoard = function(){
  var url = new Url("ssr", "ajax_vw_planning_kine_board");
  url.addParam("kine_id", '{{$kine_id}}');
  url.addParam("height", '500');
  url.requestUpdate("planning-kine"); 
};

BoardSejours = {
	update: function(hide_noevents) {
	  var url = new Url("ssr", "ajax_board_sejours");
	  url.addParam("kine_id", '{{$kine_id}}');
	  url.addParam("hide_noevents", hide_noevents ? '1' : '0');
	  url.requestUpdate("board-sejours");
	},
  updateTab: function(mode) {
    var url = new Url("ssr", "ajax_board_sejours");
    url.addParam("mode", mode);
    url.requestUpdate("board-sejours-"+ mode);
  }
};


Main.add(function(){
  Planification.showWeek(null, true);
	updatePlanningKineBoard();
	BoardSejours.update();
	
  ViewPort.SetAvlHeight("board-sejours", 0.5);
  ViewPort.SetAvlHeight("subplannings" , 1);
});

</script>

{{if !$kine->code_intervenant_cdarr}}
  <div class="small-warning">{{tr}}CMediusers-code_intervenant_cdarr-none{{/tr}}</div>
{{/if}}

<!-- Affichage de la modale -->
<div id="modal_evenements" style="display: none;"></div>

<table class="main">
	<tr>
    <td id="week-changer"></td>
		<td class="viewport">
      <form name="selectKine" method="get" action="">
        <input type="hidden" name="m" value="ssr" />
        <input type="hidden" name="tab" value="vw_kine_board" />
        <select name="kine_id" onchange="this.form.submit();">
	      	{{mb_include module=mediusers template=inc_options_mediuser list=$kines selected=$kine_id}}
        </select>
      </form>
  	</td>
  </tr>
	
	<tr>
		<td style="width: 60%" rowspan="2">				
      <form name="editSelectedEvent" method="post" action="?">
        <input type="hidden" name="m" value="ssr" />
        <input type="hidden" name="dosql" value="do_modify_evenements_aed" />
        <input type="hidden" name="event_ids" value="" />
        <input type="hidden" name="del" value="0" />    
        <input type="hidden" name="realise" value="0" />
        <input type="hidden" name="annule" value="0" />
        <input type="hidden" name="_traitement" value="1" />
      </form>
      <div id="planning-kine"></div>
		</td>
    <td class="viewport" >
    	<div id="board-sejours"></div>
    </td>
	</tr>
	
	<tr>
		<td class="viewport">
			<div id="subplannings">
				<script type="text/javascript">
				Main.add(Control.Tabs.create.curry('tabs-subplannings', true));
				</script>
				<ul id="tabs-subplannings" class="control_tabs">
				  <li><a href="#planning-sejour">Planning Patient</a></li>
				  <li><a href="#planning-equipement">Planning Equipement</a></li>
				</ul>
				<hr class="control_tabs" />
			  <div style="display: none;" id="planning-sejour">
				  <div class="small-info">
				  	Double-cliquer sur un évenement pour voir le planning du patient concerné
					</div>
				</div>
	      <div style="display: none;" id="planning-equipement">
				</div>
      </div>
		</td>
	</tr>
</table>