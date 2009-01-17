<form name="addOpFrm" action="?m={{$m}}" method="post">
  <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="m" value="dPcabinet" />
  {{mb_field object=$consult_anesth field="consultation_anesth_id" hidden=1 prop=""}}
  
  
  {{if !$consult_anesth->operation_id}}
  <!-- Choix du séjour -->
  <select name="sejour_id" style="max-width: 250px;" onchange="submitOpConsult()">
    <option value="">Pas de séjour</option>
    {{foreach from=$patient->_ref_sejours item=curr_sejour}}
    <option value="{{$curr_sejour->_id}}"{{if $consult_anesth->sejour_id==$curr_sejour->_id}} selected="selected"{{/if}}>
      Séjour du {{$curr_sejour->entree_prevue|date_format:"%d/%m/%Y"}} au {{$curr_sejour->sortie_prevue|date_format:"%d/%m/%Y"}}
    </option>
    {{/foreach}}
  </select>
  <br />

  {{else}}
  <!-- Choix de l'intervention -->
  {{mb_field object=$consult_anesth field="sejour_id" hidden=1}}
  {{/if}}
  <select name="operation_id" style="max-width: 250px;" onchange="submitOpConsult()">
    <option value="">Pas d'Intervention</option>
    {{foreach from=$patient->_ref_sejours item=curr_sejour}}
    <optgroup label="Séjour du {{$curr_sejour->entree_prevue|date_format:"%d/%m/%Y"}} au {{$curr_sejour->sortie_prevue|date_format:"%d/%m/%Y"}}"
    {{if $consult_anesth->sejour_id!=$curr_sejour->_id && $consult_anesth->sejour_id}}disabled="disabled"{{/if}}>
      {{foreach from=$curr_sejour->_ref_operations item=curr_op}}
      <option value="{{$curr_op->operation_id}}"{{if $consult_anesth->operation_id==$curr_op->_id}} selected="selected"{{/if}}>
        Le {{$curr_op->_datetime|date_format:"%d/%m/%Y"}} &mdash; Dr {{$curr_op->_ref_chir->_view}}
      </option>
      {{/foreach}}
    </optgroup>
    {{/foreach}}
  </select>
  <br />

  {{assign var=sejour value=$consult_anesth->_ref_sejour}}
  {{if $sejour->_id}}
	<span class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
  <strong>Séjour :</strong>
	  {{if $sejour->type!="ambu" && $sejour->type!="exte"}} {{$sejour->_duree_prevue}} jour(s) {{/if}}
	  {{mb_value object=$sejour field=type}}
	</span>
  <br />
  {{/if}}
  
  {{assign var=operation value=$consult_anesth->_ref_operation}}
  {{if $operation->_id}}
	<span class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$operation->_guid}}', null, { view_tarif: true })">
	  <strong>Intervention :</strong>
	  le <strong>{{$operation->_datetime|date_format:"%a %d %b %Y"}}</strong>
	  par le <strong>Dr {{$operation->_ref_chir->_view}}</strong>
    {{if $operation->libelle}}
    <em>[{{$operation->libelle}}]</em>
    {{/if}}
	</span>
  {{/if}}
</form>
<br />
{{if $operation->_id}}
<form name="editOpFrm" action="?m=dPcabinet" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_planning_aed" />
  {{mb_field object=$operation field="operation_id" hidden=1 prop=""}}
  {{mb_label object=$operation field="depassement_anesth"}}
  {{mb_field object=$operation field="depassement_anesth" onchange="this.form.onsubmit()"}}
  <button type="button" class="notext submit">{{tr}}Save{{/tr}}</button>
</form>
{{/if}}