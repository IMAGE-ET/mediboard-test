<script type="text/javascript">
	
Main.add(function(){
	PlanSoins.init({
		composition_dossier: {{$composition_dossier|@json}}, 
		date: "{{$date}}", 
		manual_planif: "{{$manual_planif}}",
	  bornes_composition_dossier:  {{$bornes_composition_dossier|@json}},
		nb_postes: {{$bornes_composition_dossier|@count}}
	});

  $("plan_soin").show();
	PlanSoins.moveDossierSoin($('plan_soin'));
});

</script>
<table class="tbl" id="plan_soin" style="display: none;">
	<tr>
	  <th class="title" colspan="100">
	  	<button type="button" class="tick" onclick="PlanSoins.applyAdministrations();" id="button_administration" style="float: right">
     </button>
		  <button class="hslip notext" type="button" style="float:left" onclick="$('categories').toggle();"></button>	
	    <button class="change" style="float: left" onclick="updatePlanSoinsPatients();">{{tr}}Search{{/tr}}</button>
			<form name="mode_dossier_soin" action="?" method="get" style="font-size: 0.8em;">
        <label>
          <input type="radio" name="mode_dossier" value="administration" {{if $mode_dossier == "administration" || $mode_dossier == ""}}checked="checked"{{/if}} 
                 onclick="PlanSoins.viewDossierSoin($('plan_soin'));"/>Administration
        </label>
        <label>
          <input type="radio" name="mode_dossier" value="planification" {{if $mode_dossier == "planification"}}checked="checked"{{/if}} 
                 onclick="PlanSoins.viewDossierSoin($('plan_soin'));" />Planification
        </label>
     </form>
	</th>
</tr>
   <tr>
      <th rowspan="2" class="title">Patient</th>
      <th rowspan="2" class="title">Libellé</th>
      <th rowspan="2" class="title">Posologie</th>
      
      {{foreach from=$count_composition_dossier key=_date item=_hours_by_moment}}
        {{foreach from=$_hours_by_moment key=moment_journee item=_count}}
          <th class="{{$_date}}-{{$moment_journee}} title" colspan="{{$_count}}">
                       
            <a href="#1" onclick="PlanSoins.showBefore()" style="float: left" onmousedown="periodicalBefore = new PeriodicalExecuter(PlanSoins.showBefore, 0.2);" onmouseup="periodicalBefore.stop();">
              <img src="images/icons/prev.png" alt="&lt;"/>
            </a>        
            <a href="#1" onclick="PlanSoins.showAfter()" style="float: right" onmousedown="periodicalAfter = new PeriodicalExecuter(PlanSoins.showAfter, 0.2);" onmouseup="periodicalAfter.stop();">
              <img src="images/icons/next.png" alt="&gt;" />
            </a>     
            <strong>
              <a href="#1" onclick="PlanSoins.selColonne('{{$_date}}-{{$moment_journee}}')">
              	{{if $composition_dossier|@count == 1}}
			            {{assign var=view_poste value="Journée"}}
			          {{else}}
									{{assign var=tab_poste value='-'|explode:$moment_journee}}
									{{assign var=num_poste value=$tab_poste|@end}}
	                {{assign var=libelle_poste value="Libelle poste $num_poste"}}
	                {{assign var=view_poste value=$configs.$libelle_poste}}
								{{/if}}
								
								{{assign var=key_borne value="$_date-$moment_journee"}} 
                {{assign var=bornes_poste value=$bornes_composition_dossier.$key_borne}}

                {{$view_poste}} du 
								{{if $bornes_poste.min|iso_date != $bornes_poste.max|iso_date}}
                  {{$bornes_poste.min|date_format:"%d/%m"}} au {{$bornes_poste.max|date_format:"%d/%m"}}
                {{else}}
                  {{$_date|date_format:"%d/%m"}}
                {{/if}}
              </a>
            </strong>
          </th>
        {{/foreach}} 
      {{/foreach}}
      <th colspan="2" class="title">Sign.</th>
    </tr>
		<tr>
      <th></th>
      {{if $manual_planif}}
        <th>x</th>
      {{/if}}
      {{foreach from=$tabHours key=_date item=_hours_by_moment}}
        {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
          {{foreach from=$_dates key=_date_reelle item=_hours}}
            {{foreach from=$_hours key=_heure_reelle item=_hour}}
              <th class="{{$_date}}-{{$moment_journee}}" 
                  style='width: 50px; text-align: center; 
                {{if array_key_exists("$_date $_hour:00:00", $operations)}}border-right: 3px solid black;{{/if}}'>
                <a href="#1" onclick="PlanSoins.selColonne('{{$_date_reelle}}-{{$_hour}}');">{{$_hour}}h</a>
                {{if array_key_exists("$_date $_hour:00:00", $operations)}}
                  {{assign var=_hour_op value="$_date $_hour:00:00"}}
                  <a style="color: white; font-weight: bold; font-style: normal;" href="#" title="Intervention à {{$operations.$_hour_op|date_format:'%Hh%M'}}">Interv.</a>
                {{/if}}
              </th>   
            {{/foreach}}
          {{/foreach}}
        {{/foreach}} 
      {{/foreach}}
      <th></th>
      <th>Dr</th>
      <th>Ph</th>
    </tr>

  {{assign var=first_iteration value=1}}
	{{foreach from=$prescriptions item=prescription name="foreach_presc"}}
    {{foreach from=$prescription->_ref_lines_elt_for_plan key=name_chap item=_elements_by_chap}}
		  {{foreach from=$_elements_by_chap item=elements_cat key=name_cat name="foreach_chap"}}
        {{assign var=categorie value=$categories.$name_chap.$name_cat}}
        {{foreach from=$elements_cat item=_element name="foreach_cat"}}
          {{foreach from=$_element key=unite_prise item=element name="foreach_elt"}}       
            {{include file="../../dPprescription/templates/inc_vw_line_dossier_soin.tpl" 
						          chapitre=$element->_chapitre
                      line=$element
                      nodebug=true
                      first_foreach=foreach_cat
                      last_foreach=foreach_elt
                      global_foreach=foreach_chap
                      nb_line=$_element|@count
                      dosql=do_prescription_line_element_aed
											show_patient=true
                      update_plan_soin=1}}
              {{assign var=first_iteration value=0}}
	        {{/foreach}}
        {{/foreach}}
      {{/foreach}}
		{{/foreach}}
	{{foreachelse}}
	<tr>
		<td class="empty" colspan="100">
			{{tr}}Aucune prescription{{/tr}}
		</td>
	</tr>
	{{/foreach}}
</table>