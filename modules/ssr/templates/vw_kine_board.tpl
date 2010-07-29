{{mb_include_script module="ssr" script="planning"}}
{{mb_include_script module="ssr" script="planification"}}
{{mb_include_script module="ssr" script="modal_validation"}}

<script type="text/javascript">
	
onSelect = function(oDiv, css_class){
  var sejour_id = css_class.match(/CSejour-([0-9]+)/)[1];
	var _equipement_id = css_class.match(/CEquipement-([0-9]+)/);
  var equipement_id = _equipement_id ? _equipement_id[1] : '';

  // refresh du planning du patient concernÚ
	Planification.refreshSejour(sejour_id, false);
	
	// refresh du planning de l'equipement concernÚ
	equipement_id ? PlanningEquipement.show(equipement_id, sejour_id) : PlanningEquipement.hide();
	
	$('planning-technicien').select('.elt_selected').invoke('removeClassName', 'elt_selected');
  oDiv.addClassName('elt_selected');
}

updatePlanningKineBoard = function(){
  var url = new Url("ssr", "ajax_vw_planning_kine_board");
  url.addParam("kine_id", '{{$kine_id}}');
	url.requestUpdate("planning-kine");	
}

Main.add(function(){
  Planification.showWeek(null, true);
	updatePlanningKineBoard();
	updateBoardSejours('{{$kine_id}}');
});

updateBoardSejours = function(kine_id) {
  new Url("ssr", "ajax_board_sejours") .
    addParam("kine_id", kine_id) .
		requestUpdate("board-sejours");
}

onCompleteShowWeek = function(){
  //PlanningTechnicien.show(); 
  updatePlanningKineBoard();
  updateBoardSejours('{{$kine_id}}')

	PlanningEquipement.hide();
  $('planning-sejour').update('');
}
</script>

{{if !$kine->code_intervenant_cdarr}}
  <div class="small-warning">{{tr}}CMediusers-code_intervenant_cdarr-none{{/tr}}</div>
{{/if}}

<!-- Affichage de la modale -->
<div id="modal_evenements" style="display: none;"></div>

<table class="main">
	<tr>
    <td id="week-changer"></td>
		<td>
      <form name="selectKine" method="get" action="">
        <input type="hidden" name="m" value="ssr" />
        <input type="hidden" name="tab" value="vw_kine_board" />
        <select name="kine_id" onchange="this.form.submit();">
          {{foreach from=$kines item=_kine}}
            <option value="{{$_kine->_id}}" class="mediuser"
                    style="border-color: #{{$_kine->_ref_function->color}}"
                    {{if $kine_id == $_kine->_id}}selected="selected"{{/if}}>{{$_kine->_view}}</option>
          {{/foreach}}
        </select>
      </form>
  	</td>
  </tr>
	
	<tr>
		<td style="width: 60%" rowspan="2">				
      <form name="editSelectedEvent" method="post" action="?">
        <input type="hidden" name="m" value="ssr" />
        <input type="hidden" name="dosql" value="do_modify_evenements_aed" />
        <input type="hidden" name="token_elts" value="" />
        <input type="hidden" name="del" value="0" />    
        <input type="hidden" name="realise" value="0" />
      </form>
      <div id="planning-kine"></div>
		</td>
    <td id="board-sejours" style="height: 320px;">
    </td>
	</tr>
	
	<tr>
		<td>
			<script type="text/javascript">
			Main.add(function () {
			  Control.Tabs.create('tabs-subplannings', true);
			});
			</script>
			
			<ul id="tabs-subplannings" class="control_tabs">
			  <li><a href="#planning-sejour">Planning Patient</a></li>
			  <li><a href="#planning-equipement">Planning Equipement</a></li>
			</ul>
			<hr class="control_tabs" />
		  <div style="display: none;" id="planning-sejour">
			  <div class="small-info">
			  	Double-cliquer sur un Úvenement pour voir le planning du patient concernÚ
				</div>
			</div>
      <div style="display: none;" id="planning-equipement">
			</div>
		</td>
	</tr>
</table>