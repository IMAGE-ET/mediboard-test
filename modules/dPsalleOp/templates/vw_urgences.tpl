<script type="text/javascript">
Main.add(function () {
  Calendar.regField(getForm("changeDate").date, null, {noView: true});
});
</script>

{{assign var=systeme_materiel value=$conf.dPbloc.CPlageOp.systeme_materiel}}

<table class="tbl main">
  <tr>
    <th class="title" colspan="10">
      Hors plage du {{$date|date_format:$conf.longdate}}
      <form action="?" name="changeDate" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
    </th>
  </tr>
  <tr>
    <th>{{tr}}CSejour-patient_id{{/tr}}</th>
    <th>{{tr}}COperation-chir_id{{/tr}}</th>
    <th>{{tr}}COperation-anesth_id{{/tr}}</th>
    <th>{{tr}}COperation-time_operation{{/tr}}</th>
    <th>{{tr}}CSalle{{/tr}}</th>
    <th>{{tr}}COperation{{/tr}}</th>
    <th>{{tr}}COperation-cote{{/tr}}</th>
    <th>{{tr}}COperation-rques{{/tr}}</th>
  </tr>
  {{foreach from=$urgences item=_op}}
    {{assign var=sejour value=$_op->_ref_sejour}}
    {{assign var=patient value=$sejour->_ref_patient}}
    {{assign var=consult_anesth value=$_op->_ref_consult_anesth}}
    {{assign var=anesth value=$_op->_ref_anesth}}

    <tr>
      <td>
        <span class="{{if !$sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $sejour->septique}}septique{{/if}}"
              onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
          {{$patient}}
        </span>
      </td>
      <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_op->_ref_chir}}</td>
      <td>
        <form name="editPlageFrm{{$_op->_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$_op->_id}}" />
          <select name="anesth_id" style="width: 15em;" onchange="this.form.submit()">
            <option value="">&mdash; Anesthésiste</option>
            {{foreach from=$anesths item=_anesth}}
              <option value="{{$_anesth->_id}}" {{if $_anesth->_id == $anesth->_id}}selected="selected"{{/if}}>{{$_anesth}}</option>
            {{/foreach}}
          </select>
        </form>
      </td>
      {{if $_op->annulee}}
      <td colspan="3" class="cancelled">
        Annulée
      </td>
      {{else}}
      <td class="text">
        {{if !$_op->annulee}}
          <form name="editTimeFrm{{$_op->_id}}" action="?m={{$m}}" method="post">
            <input type="hidden" name="m" value="dPplanningOp" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_planning_aed" />
            <input type="hidden" name="operation_id" value="{{$_op->_id}}" />
            {{assign var=_op_id value=$_op->_id}}
            {{mb_field object=$_op field=time_operation form="editTimeFrm$_op_id" register=true onchange="onSubmitFormAjax(this.form)"}}
          </form>
        {{else}}
          {{mb_value object=$_op field=time_operation}}
        {{/if}}
      </td>
      <td class="text">
        {{if !$_op->annulee}}
          <form name="editSalleFrm{{$_op->_id}}" action="?m={{$m}}" method="post">
            <input type="hidden" name="m" value="dPplanningOp" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_planning_aed" />
            <input type="hidden" name="operation_id" value="{{$_op->_id}}" />
            <select  style="width: 15em;" name="salle_id" onchange="onSubmitFormAjax(this.form)">
              <option value="">&mdash; {{tr}}CSalle.select{{/tr}}</option>
              {{foreach from=$listBlocs item=_bloc}}
              <optgroup label="{{$_bloc}}">
                {{foreach from=$_bloc->_ref_salles item=_salle}}
                <option value="{{$_salle->_id}}" {{if $_salle->_id == $_op->salle_id}}selected="selected"{{/if}}>
                  {{$_salle}}
                </option>
                {{foreachelse}}
                <option value="" disabled="disabled">{{tr}}CSalle.none{{/tr}}</option>
                {{/foreach}}
              </optgroup>
              {{/foreach}}
            </select>
          </form>
          {{if $_op->_alternate_plages|@count}}
          <form name="editPlageFrm{{$_op->_id}}" action="?m={{$m}}" method="post">
            <input type="hidden" name="m" value="dPplanningOp" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_planning_aed" />
            <input type="hidden" name="operation_id" value="{{$_op->_id}}" />
            <input type="hidden" name="date" value="" />
            <input type="hidden" name="time_op" value="" />
            <input type="hidden" name="salle_id" value="" />
            <input type="hidden" name="horaire_voulu" value="{{$_op->time_operation}}" />
            <select name="plageop_id" style="width: 15em;" onchange="this.form.submit()">
              <option value="">&mdash; Replacer cette intervention</option>
              {{foreach from=$_op->_alternate_plages item=_plage}}
              <option value="{{$_plage->_id}}">{{$_plage->_ref_salle}} - {{mb_value object=$_plage field=debut}} à {{mb_value object=$_plage field=fin}} - {{$_plage}}</option>
              {{/foreach}}
            </select>
          </form>
          {{/if}}
        {{else}}
          {{mb_value object=$_op field=salle_id}}
        {{/if}}
        {{if $systeme_materiel == "expert"}}
          {{mb_include module=dPbloc template=inc_button_besoins_ressources type=operation_id usage=1 object_id=$_op->_id}}
        {{/if}}
      </td>
      {{/if}}
      <td class="text">
        <span style="float: right;">
          {{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
          {{assign var=sejour_id value=$sejour->_id}}
          {{assign var=antecedents value=$dossier_medical->_ref_antecedents_by_type}}
          {{mb_include module=soins template=inc_vw_antecedent_allergie nodebug=true}}
          {{if $dossier_medical->_id && $dossier_medical->_count_allergies}}
            <script type="text/javascript">
              ObjectTooltip.modes.allergies = {
                module: "patients",
                action: "ajax_vw_allergies",
                sClass: "tooltip"
              };

            </script>
            <img src="images/icons/warning.png" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}', 'allergies');" />
          {{/if}}
          {{if $_op->_is_urgence}}
            <img src="images/icons/attente_fourth_part.png" title="Intervention en urgence" />
          {{/if}}
        </span>
        <a href="?m=dPplanningOp&amp;tab=vw_edit_urgence&amp;operation_id={{$_op->_id}}">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_op->_guid}}');">
        {{if $_op->libelle}}
          <em>[{{$_op->libelle}}]</em><br />
        {{/if}}
        {{foreach from=$_op->_ext_codes_ccam item=_code}}
          <strong>{{$_code->code}}</strong> : {{$_code->libelleLong}}<br />
        {{/foreach}}
        </span>
        </a>
      </td>
      <td>{{tr}}COperation.cote.{{$_op->cote}}{{/tr}}</td>
      <td class="text">
        <a href="?m=dPplanningOp&amp;tab=vw_edit_urgence&amp;operation_id={{$_op->_id}}">
        {{$_op->rques|nl2br}}
        </a>
      </td>
    </tr>
  {{foreachelse}}
    <tr><td colspan="10" class="empty">{{tr}}COperation.none{{/tr}}</td></tr>
  {{/foreach}}
</table>