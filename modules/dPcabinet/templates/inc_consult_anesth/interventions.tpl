
<form name="addOpFrm" action="?m={{$m}}" method="post">
  <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="m" value="dPcabinet" />
  {{mb_field object=$consult_anesth field="consultation_anesth_id" hidden=1 spec=""}}
  <select name="operation_id" onchange="submitOpConsult()">
    <option value="">Pas d'Intervention</option>
    {{foreach from=$patient->_ref_sejours item=curr_sejour}}
    <optgroup label="Séjour du {{$curr_sejour->entree_prevue|date_format:"%d/%m/%Y"}} au {{$curr_sejour->sortie_prevue|date_format:"%d/%m/%Y"}}">
      {{foreach from=$curr_sejour->_ref_operations item=curr_op}}
      <option value="{{$curr_op->operation_id}}"{{if $consult_anesth->operation_id==$curr_op->operation_id}} selected="selected"{{/if}}>
        Le {{$curr_op->_ref_plageop->date|date_format:"%d/%m/%Y"}} &mdash; Dr. {{$curr_op->_ref_chir->_view}}
      </option>
      {{/foreach}}
    </optgroup>
    {{/foreach}}
  </select>

  <br />

  {{if $consult_anesth->operation_id}}
    <strong>Séjour :</strong>
    {{if $consult_anesth->_ref_operation->_ref_sejour->type!="ambu" && $consult_anesth->_ref_operation->_ref_sejour->type!="exte"}} {{$consult_anesth->_ref_operation->_ref_sejour->_duree_prevue}} jour(s) {{/if}}
    {{tr}}CSejour.type.{{$consult_anesth->_ref_operation->_ref_sejour->type}}{{/tr}}
    <br />
    <strong>Intervention :</strong>
    le <strong>{{$consult_anesth->_ref_operation->_datetime|date_format:"%a %d %b %Y"}}</strong>
    par le <strong>Dr. {{$consult_anesth->_ref_operation->_ref_chir->_view}}</strong> (coté {{tr}}COperation.cote.{{$consult_anesth->_ref_operation->cote}}{{/tr}})<br />
    <ul>
      {{if $consult_anesth->_ref_operation->libelle}}
      <li><em>[{{$consult_anesth->_ref_operation->libelle}}]</em></li>
      {{/if}}
      {{foreach from=$consult_anesth->_ref_operation->_ext_codes_ccam item=curr_code}}
      <li><em>{{$curr_code->libelleLong}}</em> ({{$curr_code->code}})</li>
      {{/foreach}}
    </ul>
  {{/if}}

  {{mb_label object=$consult_anesth field="position"}}
  {{mb_field object=$consult_anesth field="position" defaultOption="&mdash; Veuillez Choisir" onchange="submitFormAjax(this.form, 'systemMsg')"}}
</form>