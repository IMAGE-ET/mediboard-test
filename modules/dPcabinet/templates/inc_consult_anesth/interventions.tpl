<script>
  selectOperation = function(operation_id) {
    var oForm = getForm("addOpFrm");
    $V(oForm.operation_id, operation_id);
  }
  selectSejour = function(sejour_id) {
    var oForm = getForm("addOpFrm");
    $V(oForm.sejour_id, sejour_id);
  }
  newOperation = function(chir_id, pat_id) {
    var url = new Url("dPplanningOp", "vw_edit_planning");
    url.addParam("chir_id", chir_id);
    url.addParam("pat_id", pat_id);
    url.addParam("operation_id", 0);
    url.addParam("sejour_id", 0);
    url.redirect();
  }

  {{if !$consult_anesth->libelle_interv && !$consult_anesth->sejour_id && !$consult_anesth->operation_id && ($nextSejourAndOperation.COperation->_id || $nextSejourAndOperation.CSejour->_id)}}
    {{if $conf.dPcabinet.CConsultAnesth.use_new_da}}
      Main.add(function () {
          GestionDA.edit();
        });
    {{else}}
      modalWindow = null;
      Main.add(function () {
        modalWindow = Modal.open($('evenement-chooser-modal'));
      });
    {{/if}}
  {{/if}}
</script>

<table class="form main" style="display: none;" id="evenement-chooser-modal">
  {{assign var=next_operation value=$nextSejourAndOperation.COperation}}
  {{assign var=next_sejour    value=$nextSejourAndOperation.CSejour   }}
  {{if $next_operation->_id}}
    <tr>
      <td colspan="2"> <div class="small-info">Une intervention à venir est présente pour ce patient</div></td>
    </tr>
    <tr>
      <td></td>
      <td><strong>{{$next_operation}}</strong></td>
    </tr>
    <tr>
      <th>{{mb_title object=$next_operation field=libelle}}</th>
      <td><strong>{{$next_operation->libelle}}</strong></td>
    </tr>
    <tr>
      <th>{{mb_title object=$next_operation field=cote}}</th>
      <td><strong>{{mb_value object=$next_operation field=cote}}</strong></td>
    </tr>
    <tr>
      <th>Prévue le </th>
      <td><strong>{{$next_operation->_datetime|date_format:$conf.date}}</strong></td>
    </tr>
    <tr>
      <th>Avec le Dr </th>
      <td><strong>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$next_operation->_ref_chir}}</strong></td>
    </tr>
    <tr>
      <td class="button" colspan="2"><button class="tick" onclick="selectOperation('{{$next_operation->_id}}');location.reload();">Associer au dossier d'anesthésie</button>
        <button class="cancel" onclick="modalWindow.close();">Ne pas associer</button></td>
    </tr>
        {{elseif $next_sejour->_id}}
    <tr>
      <td colspan="2"> <div class="small-info">Un séjour à venir est présent dans le système pour ce patient</div></td>
    </tr>
    <tr>
      <td></td>
      <td><strong>{{$next_sejour}}</strong></td>
    </tr>
    <tr>
      <th>{{mb_title object=$next_sejour field=libelle}}</th>
      <td><strong>{{$next_sejour->libelle}}</strong></td>
    </tr>
    <tr>
      <th>Avec le Dr </th>
      <td><strong>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$next_sejour->_ref_praticien}}</strong></td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button class="tick" onclick="selectSejour('{{$next_sejour->_id}}');">Associer au dossier d'anesthésie</button>
      <button class="cancel" onclick="modalWindow.close();">Ne pas associer</button></td>
    </tr>
  {{/if}}
</table>

<div id="dossiers_anesth_area">
  {{mb_include module=cabinet template=inc_consult_anesth/inc_multi_consult_anesth}}
</div>

{{assign var=operation value=$consult_anesth->_ref_operation}}

{{if $patient->_ref_sejours|@count && !$conf.dPcabinet.CConsultAnesth.use_new_da}}

<form name="addOpFrm" action="?m={{$m}}" method="post">
  <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="m" value="dPcabinet" />
  {{mb_key object=$consult_anesth}}

  {{if !$consult_anesth->operation_id}}
  <!-- Choix du séjour -->
  <select name="sejour_id" style="width: 20em;" onchange="submitOpConsult()">
    <option value="">Pas de séjour</option>
    {{foreach from=$patient->_ref_sejours item=curr_sejour}}
    <option value="{{$curr_sejour->_id}}"{{if $consult_anesth->sejour_id==$curr_sejour->_id}} selected="selected"{{/if}}>
      {{if $curr_sejour->annule}}ANNULE - {{/if}}Séjour du {{$curr_sejour->entree_prevue|date_format:"%d/%m/%Y"}} au {{$curr_sejour->sortie_prevue|date_format:"%d/%m/%Y"}}
    </option>
    {{/foreach}}
  </select>
  <br />

  {{else}}
  <!-- Choix de l'intervention -->
  {{mb_field object=$consult_anesth field="sejour_id" hidden=1}}
  {{/if}}
  <select name="operation_id" style="width: 20em;" onchange="$V(this.form.sejour_id, $(this.options[this.selectedIndex]).get('sejour_id'), false); submitOpConsult()">
    <option value="">Pas d'Intervention</option>
    {{foreach from=$patient->_ref_sejours item=curr_sejour}}
    <optgroup label="{{if $curr_sejour->annule}}ANNULE - {{/if}}Séjour du {{$curr_sejour->entree_prevue|date_format:"%d/%m/%Y"}} au {{$curr_sejour->sortie_prevue|date_format:"%d/%m/%Y"}}">
      {{foreach from=$curr_sejour->_ref_operations item=curr_op}}
      <option value="{{$curr_op->_id}}" data-sejour_id="{{$curr_op->sejour_id}}"
        {{if $consult_anesth->operation_id==$curr_op->_id}}
          selected
        {{elseif $curr_op->_ref_consult_anesth->_id && $curr_op->_ref_consult_anesth->_id != $consult_anesth->_id}}
          disabled
        {{/if}}>
        {{if $curr_op->annulee}}ANNULEE - {{/if}}Le {{$curr_op->_datetime|date_format:"%d/%m/%Y"}} &mdash; Dr {{$curr_op->_ref_chir->_view}}
      </option>
      {{/foreach}}
    </optgroup>
    {{/foreach}}
  </select>
  {{if !$app->user_prefs.simpleCabinet && !@$modules.ecap->mod_active && !$operation->_id}}
  <button class="new notext" type="button" onclick="newOperation({{$consult_anesth->_ref_consultation->_praticien_id}},{{$consult_anesth->_ref_consultation->patient_id}})">
    Nouvelle intervention
  </button>
  {{/if}}
  <br />

  {{assign var=sejour value=$consult_anesth->_ref_sejour}}
  {{if $sejour && $sejour->_id}}
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
  <br />
  <strong>{{mb_label object=$operation field="depassement"}} :</strong>
  {{mb_value object=$operation field="depassement"}}
  {{/if}}
</form>
{{/if}}

{{if $operation->_id}}
<hr />

<form name="editOpFrm" action="?m=dPcabinet" method="post" onsubmit="return onSubmitFormAjax(this);">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_planning_aed" />
  {{mb_key object=$operation}}

  <table class="layout main">
    {{if $conf.dPplanningOp.COperation.verif_cote && ($operation->cote == "droit" || $operation->cote == "gauche")}}
      <tr>
        <th>{{mb_label object=$operation field="cote_consult_anesth"}} :</th>
        <td>{{mb_field emptyLabel="Choose" object=$operation field="cote_consult_anesth" onchange="this.form.onsubmit();"}}</td>
      </tr>
    {{/if}}

    <tr>
      <th>{{mb_label object=$operation field="depassement_anesth"}} :</th>
      <td>
        {{mb_field object=$operation field="depassement_anesth" onchange="this.form.onsubmit();"}}
        <button type="button" class="notext submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>
{{elseif !$conf.dPcabinet.CConsultAnesth.use_new_da}}
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
          <select name="chir_id" class="{{$consult_anesth->_props.chir_id}}" style="width: 14em;" onchange="this.form.onsubmit();">
            <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
            {{mb_include module=mediusers template=inc_options_mediuser selected=$consult_anesth->chir_id list=$listChirs}}
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