<script>
  createDA = function(operation_id, consult_anesth_id, duplicate, sejour_id) {
    var form = getForm("createDossierAnesth");
    $V(form.operation_id, operation_id);
    $V(form.sejour_id, sejour_id);
    if (duplicate == 1) {
      $V(form.dosql, "do_duplicate_dossier_anesth_aed");
      $V(form.redirect, "1");
      $V(form._consult_anesth_id, consult_anesth_id);
    }
    form.submit();
  }
  deleteDA = function() {
    var form = getForm("deleteDossierAnesth");
    confirmDeletion(form, { typeName: 'le dossier d\'anesthésie' });
  }
  saveModif = function(form) {
    return onSubmitFormAjax(form, {
      onComplete: function() {
        GestionDA.url.refreshModal();
      }});
  }
  reloadDossierAnesthCurr = function() {
    var consultUrl = new Url("dPcabinet", "httpreq_vw_consult_anesth");
    consultUrl.addParam("selConsult", '{{$consult->_id}}');
    consultUrl.addParam("dossier_anesth_id", '{{$consult->_ref_consult_anesth->_id}}');
    consultUrl.requestUpdate('consultAnesth');
  }
</script>
<form name="createDossierAnesth" action="?m={{$m}}&tab=edit_consultation&selConsult={{$consult->_id}}" method="post">
  <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="redirect" value="?m={{$m}}&tab=edit_consultation&selConsult={{$consult->_id}}" />
  <input type="hidden" name="_consult_anesth_id" value="" />
  <input type="hidden" name="consultation_id" value="{{$consult->_id}}" />
  <input type="hidden" name="operation_id" value=""/>
  <input type="hidden" name="sejour_id" value=""/>
</form>

<table class="tbl">
  <tr id="didac_tr_associer_dossier">
    <th>Dossier d'anesthésie</th>
    <th style="width:45%">Intervention</th>
  </tr>
  {{if $consult->_refs_dossiers_anesth|@count == 1 && !$consult->_ref_consult_anesth->operation_id}}
    {{foreach from=$patient->_ref_sejours item=curr_sejour}}
      {{foreach from=$curr_sejour->_ref_operations item=curr_op}}
        <tr>
          <td class="button">
            <form name="addInterv-{{$curr_op->_id}}" action="?m={{$m}}" method="post">
              <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="m" value="dPcabinet" />
              {{mb_key object=$consult->_ref_consult_anesth}}
              <input type="hidden" name="operation_id" value="{{$curr_op->_id}}"/>
              <button type="submit" class="link">Associer à cette intervention</button>
            </form>
          </td>
          <td>
          <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_op->_guid}}', null, { view_tarif: true })">
            Le <strong>{{$curr_op->_datetime|date_format:"%a %d %b %Y"}}</strong>
            {{if $curr_op->cote}}Coté: {{$curr_op->cote}}{{/if}}<br/>
            {{if $curr_op->libelle}}
              <strong>{{$curr_op->libelle}}</strong>
            {{/if}}
            par le <strong>Dr {{$curr_op->_ref_chir->_view}}</strong>
          </span><br/>
            {{assign var=sejour value=$curr_op->_ref_sejour}}
            <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_sejour->_guid}}')">
              <strong>Séjour du {{$curr_sejour->entree_prevue|date_format:"%d/%m/%Y"}} au {{$curr_sejour->sortie_prevue|date_format:"%d/%m/%Y"}}</strong>
              {{if $curr_sejour->type!="ambu" && $curr_sejour->type!="exte"}} {{$curr_sejour->_duree_prevue}} jour(s){{/if}} {{mb_value object=$curr_sejour field=type}}
            </span>
          </td>
        </tr>
      {{/foreach}}
    {{foreachelse}}
      <tr>
        <td class="button">
          {{assign var=consult_anesth value=$consult->_ref_consult_anesth}}

          {{if !$consult_anesth->date_interv && !$consult_anesth->chir_id && !$consult_anesth->libelle_interv}}
            <button type="button" class="edit" onclick="$('no_interv').show();">Renseigner l'intervention (Date, opérateur, libellé)</button>
            <div style="display:none;" id="no_interv">
          {{else}}
            <div>
          {{/if}}
            <form name="opInfoFrm" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this)">
              <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="m" value="dPcabinet" />
              {{mb_key object=$consult_anesth}}
              <table class="form">
                <tr>
                  <td style="text-align: right"><strong>{{mb_label object=$consult_anesth field="date_interv"}}</strong></td>
                  <td>{{mb_field object=$consult_anesth field="date_interv" form="opInfoFrm" register=true onchange="this.form.onsubmit()"}}</td>
                </tr>
                <tr>
                  <td style="text-align: right"><strong>{{mb_label object=$consult_anesth field="chir_id"}}</strong></td>
                  <td>
                    <select name="chir_id" class="{{$consult_anesth->_props.chir_id}}" style="width: 14em;" onchange="this.form.onsubmit();">
                      <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                      {{mb_include module=mediusers template=inc_options_mediuser selected=$consult_anesth->chir_id list=$listChirs}}
                    </select>
                  </td>
                </tr>
                <tr>
                  <td style="text-align: right"><strong>{{mb_label object=$consult_anesth field="libelle_interv"}}</strong></td>
                  <td>{{mb_field object=$consult_anesth field="libelle_interv" onchange="this.form.onsubmit()"}}</td>
                </tr>
                <tr>
                  <td colspan="2" class="button">
                    <button type="button" class="save" onclick="reloadDossierAnesthCurr();Control.Modal.close();">{{tr}}Validate{{/tr}}</button>
                  </td>
                </tr>
              </table>
            </form>
          </div>
        </td>
        <td>
          <div class="warning" style="float:left;"> Pas d'intervention </div>
          <button class="new" type="button" onclick="showSejourButtons();" style="float:right;">Nouvelle hospitalisation</button>
        </td>
      </tr>
    {{/foreach}}
    <tr>
      <td colspan="2" class="button">
        <form name="deleteDossierAnesth" action="?m={{$m}}" method="post">
          {{mb_class object=$consult->_ref_consult_anesth}}
          {{mb_key   object=$consult->_ref_consult_anesth}}
          <input type="hidden" name="del" value="1" />
          <button class="trash" type="button" onclick="deleteDA();">Supprimer le dossier d'anesthésie</button>
        </form>
      </td>
    </tr>
  {{else}}
    {{foreach from=$consult->_refs_dossiers_anesth item=consult_anesth name=consults_anesth}}
      {{assign var="operation" value=$consult_anesth->_ref_operation}}
      <tr>
        <td class="button">
          <table class="form">
            <tr>
              <td>
                {{if $consult->_refs_dossiers_anesth|@count > 1}}
                  <button class="search" onclick="reloadDossierAnesth('{{$consult_anesth->_id}}');" style="">Afficher</button>
                {{/if}}
              </td>
              <td {{if !$conf.dPcabinet.CConsultAnesth.check_close}}colspan="2" {{/if}} style="text-align:center;">
                <span onmouseover="ObjectTooltip.createEx(this, '{{$consult_anesth->_guid}}')">
                  {{$consult_anesth->_view}}
                </span>
              </td>
              {{if $conf.dPcabinet.CConsultAnesth.check_close}}
                <td><strong>IPAQSS:</strong>{{mb_include module=cabinet template=inc_check_ipaqss}}</td>
              {{/if}}
              <td>
                <span style="float:right;">
                  {{mb_include module=system template=inc_object_history object=$consult_anesth}}
                </span>
              </td>
            </tr>
          </table>
          {{if $smarty.foreach.consults_anesth.last && $ops_sans_dossier_anesth|@count != 0}}
            <button class="down" id="didac_button_duplicate" onclick="createDA('{{$first_operation->_id}}','{{$consult_anesth->_id}}', 1, '{{$first_operation->sejour_id}}');" style="position:relative;top:15px;">Dupliquer</button>
          {{/if}}
        </td>
        <td>
          {{if $consult_anesth->operation_id}}
            <form name="addInterv-{{$operation->_id}}" action="?m={{$m}}" method="post" onsubmit="return saveModif(this);">
              <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
              <input type="hidden" name="del" value="{{if $smarty.foreach.consults_anesth.first && $consult->_refs_dossiers_anesth|@count == 1}}0{{else}}1{{/if}}" />
              <input type="hidden" name="m" value="dPcabinet" />
              {{mb_key object=$consult_anesth}}
              <input type="hidden" name="operation_id" value=""/>
              <button type="button" class="unlink notext" onclick="return saveModif(this.form);" style="float:right;"
                      title="Supprimer le {{if $consult_anesth->operation_id}}lien à l'intervention{{else}}dossier d'anesthésie{{/if}}">
              </button>
            </form>
            <span onmouseover="ObjectTooltip.createEx(this, '{{$operation->_guid}}', null, { view_tarif: true })">
              Le <strong>{{$operation->_datetime|date_format:"%a %d %b %Y"}}</strong>
              {{if $operation->cote}}Coté: {{$operation->cote}}{{/if}}<br/>
              {{if $operation->libelle}}
                <strong>{{$operation->libelle}}</strong>
              {{/if}}
               par le <strong>Dr {{$operation->_ref_chir->_view}}</strong>
            </span><br/>
            {{assign var=sejour value=$consult_anesth->_ref_operation->_ref_sejour}}
            <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
              <strong>Séjour du {{$sejour->entree_prevue|date_format:"%d/%m/%Y"}} au {{$sejour->sortie_prevue|date_format:"%d/%m/%Y"}}</strong>
                  {{if $sejour->type!="ambu" && $sejour->type!="exte"}} {{$sejour->_duree_prevue}} jour(s){{/if}}
                  {{mb_value object=$sejour field=type}}
            </span>
          {{else}}
            <div class="warning">Pas d'intervention pour ce dossier d'anesthésie</div>
          {{/if}}
        </td>
      </tr>
    {{/foreach}}
    {{foreach from=$ops_sans_dossier_anesth item=operation}}
      <tr>
        <td class="button">
          <button class="link" onclick="createDA('{{$operation->_id}}', 0, 0, '{{$operation->sejour_id}}');">Nouveau dossier vierge</button>
        </td>
        <td>
          <span onmouseover="ObjectTooltip.createEx(this, '{{$operation->_guid}}', null, { view_tarif: true })">
            Le <strong>{{$operation->_datetime|date_format:"%a %d %b %Y"}}</strong>
            {{if $operation->cote}}Coté: {{$operation->cote}}{{/if}}<br/>
            {{if $operation->libelle}}
              <strong>{{$operation->libelle}}</strong>
            {{/if}}
            par le <strong>Dr {{$operation->_ref_chir->_view}}</strong>
          </span><br/>
          {{assign var=sejour value=$operation->_ref_sejour}}
          <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
              <strong>Séjour du {{$sejour->entree_prevue|date_format:"%d/%m/%Y"}} au {{$sejour->sortie_prevue|date_format:"%d/%m/%Y"}}</strong>
            {{if $sejour->type!="ambu" && $sejour->type!="exte"}} {{$sejour->_duree_prevue}} jour(s){{/if}} {{mb_value object=$sejour field=type}}
            </span>
        </td>
      </tr>
    {{/foreach}}

  {{/if}}
</table>