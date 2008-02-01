<!-- Entêtes -->
<tr>
  {{if @$urgence}}
  <th>Praticien</th>
  {{else}}
  <th>Heure</th>
  {{/if}}
  <th>Patient</th>
  {{if !$vueReduite}}
  <th>Actes</th>
  <th>Coté</th>
  <th>Durée</th>
  {{/if}}
</tr>

{{foreach from=$operations item=_operation}}
<tr {{if $_operation->_id == $operation_id}}class="selected"{{/if}}>
  {{if $_operation->entree_salle && $_operation->sortie_salle}}
  <td style="background-image:url(images/icons/ray.gif); background-repeat:repeat;">
  {{elseif $_operation->entree_salle}}
  <td style="background-color:#cfc">
  {{elseif $_operation->sortie_salle}}
  <td style="background-color:#fcc">
  {{elseif $_operation->entree_bloc}}
  <td style="background-color:#ffa">
  {{else}}
  <td class="text">
  {{/if}}
    <a href="?m=dPsalleOp&amp;tab=vw_operations&amp;salle={{$salle}}&amp;op={{$_operation->_id}}" title="Coder l'intervention">
		  {{if @$urgence}}
		  {{$_operation->_ref_chir->_view}}
		  {{else}}
	      {{if $_operation->time_operation != "00:00:00"}}
	      {{$_operation->time_operation|date_format:"%Hh%M"}}
	      {{else}}
	      NP
	      {{/if}}
	    {{/if}}
    </a>
  </td>
  
  <td class="text">
    <a href="?m=dPsalleOp&amp;tab=vw_operations&amp;salle={{$salle}}&amp;op={{$_operation->_id}}" title="Coder l'intervention">
      {{$_operation->_ref_sejour->_ref_patient->_view}}
    </a>
  {{if $vueReduite}}
    <button style="float:right" class="print notext" onclick="printFeuilleBloc({{$_operation->_id}})">{{tr}}Imprimer{{/tr}}</button>
  {{/if}}
  </td>
  
  {{if !$vueReduite}}
  <td>
    <a href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$_operation->_id}}" title="Modifier l'intervention">
      {{foreach from=$_operation->_ext_codes_ccam item=curr_code}}
      {{$curr_code->code}}<br />
      {{/foreach}}
    </a>
  </td>
  <td>{{tr}}COperation.cote.{{$_operation->cote}}{{/tr}}</td>
  <td>{{$_operation->temp_operation|date_format:"%Hh%M"}}</td>
  {{/if}}
</tr>
{{/foreach}}
