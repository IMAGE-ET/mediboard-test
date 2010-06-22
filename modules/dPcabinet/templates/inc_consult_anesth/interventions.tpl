<script type="text/javascript">
  function selectOperation(operation_id) {
    oForm = document.addOpFrm;
    $V(oForm.operation_id, operation_id);
    submitOpConsult();
  }
  function selectSejour(sejour_id) {
    oForm = document.addOpFrm;
    $V(oForm.sejour_id, sejour_id);
    submitOpConsult();
  }
  {{if !$consult_anesth->libelle_interv && !$consult_anesth->sejour_id && !$consult_anesth->operation_id && ($nextSejourAndOperation.COperation->_id || $nextSejourAndOperation.CSejour->_id)}}
  modalWindow = null;
  Main.add(function () {
    modalWindow = modal($('evenement-chooser-modal'), {
      className: 'modal'
    });
  });
  {{/if}}
</script>

<div class="big-info"  style="display: none; text-align: center;" id="evenement-chooser-modal">
  {{if $nextSejourAndOperation.COperation->_id}}
    Une intervention est présente dans le système pour ce patient :
    <br />
    <strong>{{$nextSejourAndOperation.COperation->_view}} -
    {{$nextSejourAndOperation.COperation->_datetime|date_format:"%d/%m/%Y"}}</strong>
    <br />
    Ce patient vient-il pour cette intervention ?
    <br />
    <button class="tick" onclick="selectOperation('{{$nextSejourAndOperation.COperation->_id}}'); modalWindow.close();">Oui</button>
  {{elseif $nextSejourAndOperation.CSejour->_id}}
    Un dossier est présent dans le système pour ce patient :
    <br />
    <strong>{{$nextSejourAndOperation.CSejour->_view}}</strong>
    <br />
    Ce patient vient-il pour ce séjour ?
    <br />
    <button class="tick" onclick="selectSejour('{{$nextSejourAndOperation.CSejour->_id}}'); modalWindow.close();">Oui</button>
  {{/if}}
  <button class="cancel" onclick="modalWindow.close();">Non</button>
</div>

{{assign var=operation value=$consult_anesth->_ref_operation}}

{{if $patient->_ref_sejours|@count}}

<form name="addOpFrm" action="?m={{$m}}" method="post">
  <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="m" value="dPcabinet" />
  {{mb_key object=$consult_anesth}}

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
	<span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
  <strong>Séjour :</strong>
	  Dr {{$sejour->_ref_praticien->_view}} -
	  {{if $sejour->type!="ambu" && $sejour->type!="exte"}} {{$sejour->_duree_prevue}} jour(s) -{{/if}}
	  {{mb_value object=$sejour field=type}}
	</span>
  <br />
  {{/if}}
  
  {{if $operation->_id}}
	<span onmouseover="ObjectTooltip.createEx(this, '{{$operation->_guid}}', null, { view_tarif: true })">
	  <strong>Intervention :</strong>
	  le <strong>{{$operation->_datetime|date_format:"%a %d %b %Y"}}</strong>
	  par le <strong>Dr {{$operation->_ref_chir->_view}}</strong>
    {{if $operation->libelle}}
    <em>[{{$operation->libelle}}]</em>
    {{/if}}
	</span>
  {{/if}}
</form>
{{/if}}

{{if $operation->_id}}
<form name="editOpFrm" action="?m=dPcabinet" method="post" onsubmit="return onSubmitFormAjax(this);">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_planning_aed" />
  {{mb_field object=$operation field="operation_id" hidden=1 prop=""}}
  <br />
  {{if $dPconfig.dPplanningOp.COperation.verif_cote && ($operation->cote == "droit" || $operation->cote == "gauche")}}
  {{mb_label object=$curr_op field="cote_consult_anesth"}} :
  {{mb_field defaultOption="&mdash; choisir" object=$curr_op field="cote_consult_anesth" onchange="this.form.onsubmit();"}}
  <br />
  {{/if}}
  {{mb_label object=$operation field="depassement_anesth"}}
  {{mb_field object=$operation field="depassement_anesth" onchange="this.form.onsubmit();"}}
  <button type="button" class="notext submit">{{tr}}Save{{/tr}}</button>
</form>
{{else}}
<form name="opInfoFrm" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="m" value="dPcabinet" />
  {{mb_key object=$consult_anesth}}
  <table class="form">
    <tr>
      <th>{{mb_label object=$consult_anesth field="date_interv"}}</th>
      <td>{{mb_field object=$consult_anesth field="date_interv" form="opInfoFrm" register=true onchange="this.form.onsubmit()"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$consult_anesth field="chir_id"}}</th>
      <td>
        <select name="chir_id" class="{{$consult_anesth->_props.chir_id}}" style="max-width: 14em;" ="this.form.onsubmit();">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{foreach from=$listChirs item=curr_prat}}
          <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" {{if $consult_anesth->chir_id == $curr_prat->user_id}} selected="selected" {{/if}}>
          {{$curr_prat->_view}}
          </option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      <th>{{mb_label object=$consult_anesth field="libelle_interv"}}</th>
      <td>{{mb_field object=$consult_anesth field="libelle_interv" onchange="this.form.onsubmit()"}}</td>
    </tr>
  </table>
</form>
{{/if}}