{{** 
  * @param $urgence bool Urgence mode
  * @param $vueReduite bool Affichage compact
  * @param $operations array|COperation interventions à afficher
  *}}

<!-- Entêtes -->
<tr>
  {{if $urgence && $salle}}
  <th>Praticien</th>
  {{else}}
  <th>Heure</th>
  {{/if}}
  <th>Patient</th>
  {{if !$vueReduite}}
  <th>Interv</th>
  <th>Coté</th>
  <th>Durée</th>
  {{/if}}
</tr>

{{foreach from=$operations item=_operation}}
{{if $dPconfig.dPsalleOp.COperation.modif_salle}}
  {{assign var="rowspan" value=2}}
{{else}}
  {{assign var="rowspan" value=1}}
{{/if}}
<tbody class="hoverable">
<tr {{if $_operation->_id == $operation_id}}class="selected"{{/if}}>
  {{if $_operation->_deplacee}}
  <td class="text" rowspan="{{$rowspan}}" style="background-color:#ccf">
  {{elseif $_operation->entree_salle && $_operation->sortie_salle}}
  <td class="text" rowspan="{{$rowspan}}" style="background-image:url(images/icons/ray.gif); background-repeat:repeat;">
  {{elseif $_operation->entree_salle}}
  <td class="text" rowspan="{{$rowspan}}" style="background-color:#cfc">
  {{elseif $_operation->sortie_salle}}
  <td class="text" rowspan="{{$rowspan}}" style="background-color:#fcc">
  {{elseif $_operation->entree_bloc}}
  <td class="text" rowspan="{{$rowspan}}" style="background-color:#ffa">
  {{else}}
  <td class="text" rowspan="{{$rowspan}}">
  {{/if}}
    <a href="?m=dPsalleOp&amp;tab=vw_operations&amp;op={{$_operation->_id}}" title="Coder l'intervention">
		  {{if $urgence && $salle}}
		  {{$_operation->_ref_chir->_view}}
		  {{else}}
	      {{if $_operation->time_operation != "00:00:00"}}
	        {{$_operation->time_operation|date_format:$dPconfig.time}}
	      {{else}}
	        NP
	      {{/if}}
		    {{if $_operation->_ref_plageop->spec_id}}
		    - {{$_operation->_ref_chir->_view}}
		    {{/if}}
	    {{/if}}
    </a>
  </td>
 
  {{if $_operation->_deplacee && $_operation->salle_id != $salle->_id}}
  <td class="text" colspan="5">
    <div class="warning">
      <span class="{{if !$_operation->_ref_sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $_operation->_ref_sejour->septique}}septique{{/if}}">
    	{{$_operation->_ref_patient->_view}}
    	</span>
    	<br />
	    Intervention déplacée vers {{$_operation->_ref_salle->_view}}
    </div>
  </td>
  
  {{else}}
  <td class="text">
  	{{if $vueReduite}}
  	  <button style="float:right" class="print notext" onclick="printFeuilleBloc({{$_operation->_id}})">{{tr}}Imprimer{{/tr}}</button>
  	{{/if}}
    <a href="?m=dPsalleOp&amp;tab=vw_operations&amp;salle={{$salle->_id}}&amp;op={{$_operation->_id}}" title="Coder l'intervention">
    <span class="{{if !$_operation->_ref_sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $_operation->_ref_sejour->septique}}septique{{/if}}">
    {{$_operation->_ref_patient->_view}}
    </span>
    {{if $_operation->_ref_affectation && $_operation->_ref_affectation->_ref_lit->_id && $dPconfig.dPbloc.CPlageOp.chambre_operation == 1}}
      <div style="text-align: center; font-size: 0.8em; width: 1%; white-space: nowrap; border: none;">
        {{$_operation->_ref_affectation->_ref_lit->_ref_chambre->_ref_service->_view}}<br />
        {{$_operation->_ref_affectation->_ref_lit->_view}}
      </div>
    {{/if}}
    </a>
  </td>
  
	{{if !$vueReduite}}
  <td class="text">
		{{mb_ternary var=direction test=$urgence value=vw_edit_urgence other=vw_edit_planning}}
    <a href="?m=dPplanningOp&amp;tab={{$direction}}&amp;operation_id={{$_operation->_id}}" title="Modifier l'intervention">
      {{if $_operation->libelle}}
        {{$_operation->libelle}}
      {{else}}
        {{foreach from=$_operation->_ext_codes_ccam item=curr_code}}
          {{$curr_code->code}}
        {{/foreach}}
      {{/if}}
    </a>
  </td>
  <td>
    {{if $dPconfig.dPplanningOp.COperation.verif_cote && ($_operation->cote == "droit" || $_operation->cote == "gauche")}}
      <form name="editCoteOp{{$_operation->_id}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        {{mb_key object=$_operation}}
        {{mb_field defaultOption="&mdash; côté" object=$_operation field="cote_bloc" onchange="this.form.submit();"}}
      </form>
    {{else}}
    {{mb_value object=$_operation field="cote"}}
  {{/if}}
  </td>
  <td>{{$_operation->temp_operation|date_format:$dPconfig.time}}</td>
  {{/if}}
  {{/if}}
</tr>

{{if $dPconfig.dPsalleOp.COperation.modif_salle && !($_operation->_deplacee && $_operation->salle_id != $salle->_id) && !($tab == "vw_suivi_salles" && $m == "dPsalleOp")}}
<tr>
  <td colspan="5">
    <form name="changeSalle{{$_operation->_id}}" action="?m={{$m}}" method="post">
    <input type="hidden" name="dosql" value="do_planning_aed" />
    <input type="hidden" name="m" value="dPplanningOp" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="operation_id" value="{{$_operation->_id}}" />
    <select name="salle_id" onchange="this.form.submit();">

      {{foreach from=$listBlocs item=curr_bloc}}
      <optgroup label="{{$curr_bloc->nom}}">
        {{foreach from=$curr_bloc->_ref_salles item=curr_salle}}
        <option value="{{$curr_salle->_id}}" {{if $curr_salle->_id == $_operation->salle_id}}selected="selected"{{/if}}>
          {{$curr_salle->nom}}
        </option>
        {{foreachelse}}
        <option value="" disabled="disabled">{{tr}}CSalle.none{{/tr}}</option>
        {{/foreach}}
      </optgroup>
      {{/foreach}}
    </select>
    </form>
  </td>
</tr>
{{/if}}
</tbody>
{{/foreach}}
