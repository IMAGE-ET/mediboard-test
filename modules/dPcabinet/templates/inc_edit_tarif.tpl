<script>
  Main.add(function () {
    Tarif.updateTotal();
    Tarif.chir_id     = '{{$prat->user_id}}';
    Tarif.function_id = '{{$prat->function_id}}';
    Tarif.group_id    = '{{$prat->_ref_function->group_id}}';
    {{if $user->_is_praticien || ($user->_is_secretaire && $tarif->_id)}}
      Tarif.updateOwner();
    {{/if}}
  });
  choicePrat = function(prat_id) {
    var form = getForm("editFrm");
    if (prat_id != "") {
      form.function_id.value = "";
    }
    else {
      form.function_id.value = '{{$prat->function_id}}';
    }
  }
</script>

<form name="editFrm" action="#" method="post" onsubmit="return Tarif.submit(this);">
  {{mb_class object=$tarif}}
  {{mb_key   object=$tarif}}

  <table class="main form">
    {{mb_include module=system template=inc_form_table_header object=$tarif}}

    {{if $user->_is_praticien || ($user->_is_secretaire && $tarif->_id)}}
      <tr>
        <th>{{mb_label object=$tarif field="_type"}}</th>
        <td>
          <input type="hidden" name="function_id" value="{{$prat->function_id}}" />
          <input type="hidden" name="chir_id" value="{{$prat->user_id}}" />
          <input type="hidden" name="group_id" value="{{$prat->_ref_function->group_id}}" />

          <select name="_type" onchange="Tarif.updateOwner();">
            <option value="chir"     {{if $tarif->chir_id}}     selected="selected" {{/if}}>Tarif personnel</option>
            <option value="function" {{if $tarif->function_id}} selected="selected" {{/if}}>Tarif de cabinet</option>
            {{if $conf.dPcabinet.Tarifs.show_tarifs_etab}}
              <option value="group" {{if $tarif->group_id}}    selected="selected" {{/if}}>Tarif d'établissement</option>
            {{/if}}
          </select>
        </td>
      </tr>

    {{else}}
      <tr>
        <th>{{mb_label object=$tarif field=chir_id}}</th>
        <td>
          <input  type="hidden" name="function_id" value="" />
          <select name="chir_id" onchange="choicePrat(this.value);">
            <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
            {{mb_include module=mediusers template=inc_options_mediuser list=$listPrat selected=$prat->_id}}
          </select>
        </td>
      </tr>
    {{/if}}

    <tr>
      <th>{{mb_label object=$tarif field="description"}}</th>
      <td>{{mb_field object=$tarif field="description"}}</td>
    </tr>
    {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
      <tr>
        <th>{{mb_label object=$tarif field=codes_ccam}}</th>
        <td>
          {{if !$tarif->_id}}
            {{mb_field object=$tarif field=codes_ccam hidden=hidden}}
          {{/if}}
          {{foreach from=$tarif->_codes_ccam item=_code_ccam}}
            <span onmouseover="ObjectTooltip.createDOM(this, 'DetailCCAM-{{$_code_ccam}}');">{{$_code_ccam}}</span>
            <div id="DetailCCAM-{{$_code_ccam}}" style="display: none">
              {{mb_include module=system template=CMbObject_view object=$tarif->_new_actes.$_code_ccam}}
            </div>
            <br/>
          {{foreachelse}}
            <div class="empty">{{tr}}None{{/tr}}</div>
          {{/foreach}}
        </td>
      </tr>

      <tr>
        <th>{{mb_label object=$tarif field=codes_ngap}}</th>
        <td>
          {{if !$tarif->_id}}
            {{mb_field object=$tarif field=codes_ngap hidden=hidden}}
          {{/if}}
          {{foreach from=$tarif->_codes_ngap item=_code_ngap}}
            <span onmouseover="ObjectTooltip.createDOM(this, 'DetailNGAP-{{$_code_ngap}}');">{{$_code_ngap}}</span>
            <br/>
            <div id="DetailNGAP-{{$_code_ngap}}" style="display: none">
              {{mb_include module=system template=CMbObject_view object=$tarif->_new_actes.$_code_ngap}}
            </div>
            {{foreachelse}}
            <div class="empty">{{tr}}None{{/tr}}</div>
          {{/foreach}}
        </td>
      </tr>

      <tr>
        <th>{{mb_label object=$tarif field=secteur1}}</th>
        <td>
          {{if count($tarif->_new_actes)}}
            {{mb_field object=$tarif field=secteur1 hidden=1}}
            {{mb_value object=$tarif field=secteur1}}
          {{else}}
            {{mb_field object=$tarif field=secteur1 onchange="Tarif.updateTotal();"}}
            <input type="hidden" name="_tarif" />
          {{/if}}
        </td>
      </tr>

      <tr>
        <th>{{mb_label object=$tarif field=secteur2}}</th>
        <td>
          {{if count($tarif->_new_actes)}}
            <div id="force-recompute"  class="info" style="float: right; display: none;" onmouseover="ObjectTooltip.createDOM(this, 'force-recompute-info')">
              {{tr}}Info{{/tr}}
            </div>
            <div id="force-recompute-info" class="small-info" style="display: none;">
              {{tr}}CTarif-_secteur1_uptodate-force{{/tr}}
            </div>
            {{mb_field object=$tarif field=secteur2 onchange="Tarif.updateTotal(); Tarif.forceRecompute();"}}
          {{else}}
            {{mb_field object=$tarif field=secteur2 onchange="Tarif.updateTotal();"}}
          {{/if}}
        </td>
      </tr>

      <tr>
        <th>{{mb_label object=$tarif field=secteur3}}</th>
        <td>{{mb_field object=$tarif field=secteur3 onchange="Tarif.updateTotal();"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$tarif field=taux_tva}}</th>
        <td>
          {{assign var=taux_tva value="|"|explode:$conf.dPcabinet.CConsultation.default_taux_tva}}
          <select name="taux_tva" onchange="Tarif.updateTotal();">
            {{foreach from=$taux_tva item=taux}}
            <option value="{{$taux}}" {{if $tarif->taux_tva == $taux}}selected="selected"{{/if}}>{{tr}}CTarif.taux_tva.{{$taux}}{{/tr}}</option>
            {{/foreach}}
          </select>
        </td>
      </tr>
      <tr>
        <th>{{mb_label object=$tarif field=_du_tva}}</th>
        <td>{{mb_field object=$tarif field=_du_tva readonly=readonly}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$tarif field=_somme}}</th>
        <td>
          {{if count($tarif->_new_actes)}}
            {{mb_field object=$tarif field=_somme readonly=1}}
          {{else}}
            {{mb_field object=$tarif field=_somme onchange="Tarif.updateSecteur2();"}}
          {{/if}}
        </td>
      </tr>

    {{elseif @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed == "1"}}
      <tr>
        <th>{{mb_label object=$tarif field=codes_tarmed}}</th>
        <td>
          {{if !$tarif->_id}}
            {{mb_field object=$tarif field=codes_tarmed hidden=hidden}}
          {{/if}}
          {{foreach from=$tarif->_codes_tarmed item=_code_tarmed}}
            <span onmouseover="ObjectTooltip.createDOM(this, 'DetailTarmed-{{$_code_tarmed}}');">{{$_code_tarmed}}</span>
            <br/>
            <div id="DetailTarmed-{{$_code_tarmed}}" style="display: none">
              {{mb_include module=system template=CMbObject_view object=$tarif->_new_actes.$_code_tarmed}}
            </div>
            {{foreachelse}}
            <div class="empty">{{tr}}None{{/tr}}</div>
          {{/foreach}}
        </td>
      </tr>
      <tr>
        <th>{{mb_label object=$tarif field=codes_caisse}}</th>
        <td>
          {{if !$tarif->_id}}
            {{mb_field object=$tarif field=codes_caisse hidden=hidden}}
          {{/if}}
          {{foreach from=$tarif->_codes_caisse item=_code_caisse}}
            <span onmouseover="ObjectTooltip.createDOM(this, 'DetailCaisse-{{$_code_caisse}}');">{{$_code_caisse}}</span>
            <br/>
            <div id="DetailTarmed-{{$_code_caisse}}" style="display: none">
              {{mb_include module=system template=CMbObject_view object=$tarif->_new_actes.$_code_caisse}}
            </div>
            {{foreachelse}}
            <div class="empty">{{tr}}None{{/tr}}</div>
          {{/foreach}}
        </td>
      </tr>
      <tr>
        <th>Total</th>
        <td>
          {{if $conf.ref_pays == 1 || !$tarif->_id}}
            {{mb_field object=$tarif field=secteur1 onchange="Tarif.updateTotal();"}}
            <input type="hidden" name="secteur2" />
            <input type="hidden" name="_tarif" />
            <input type="hidden" name="_somme" />
          {{else}}
            {{$tarif->secteur1}} Pts
          {{/if}}
        </td>
      </tr>
    {{/if}}

    <tr>
      <td class="button" colspan="2">
        <button id="vw_edit_tarif_button_save" class="save" type="button" onclick="return Tarif.submit(this.form);">{{tr}}Save{{/tr}}</button>
        {{if $tarif->_id}}
          {{*{{if count($tarif->_new_actes) && !$tarif->_has_mto && $conf.dPccam.CCodeCCAM.use_cotation_ccam}}
            <input type="hidden" name="_add_mto" value="0" />
            <button class="add" type="button" onclick="$V(this.form._add_mto, '1');Tarif.recalcul(this.form);">
              {{tr}}Add{{/tr}} MTO
            </button>
          {{/if}}*}}

          {{if count($tarif->_new_actes)}}
            <input type="hidden" name="_update_montants" value="0" />
            <button id="vw_edit_tarif_button_recalculer" class="change" type="button" onclick="$V(this.form._update_montants, '1');Tarif.recalcul(this.form);">
              {{tr}}Recompute{{/tr}}
            </button>
          {{/if}}

          {{if $conf.ref_pays == "2"}}
            <button class="edit" type="button" onclick="Code.edit('{{$tarif->_id}}');"> Gestion codes </button>
          {{/if}}
          <button class="trash" type="button" onclick="confirmDeletion(this.form, { typeName: 'le tarif', objName: this.form.description.value } )">
            {{tr}}Delete{{/tr}}
          </button>
        {{/if}}
      </td>
    </tr>
  <tr>
    <td colspan="2">
      {{if $tarif->_id}}
        {{if $tarif->_precode_ready}}
          <div class="small-success">
            {{tr}}CTarif-_precode_ready-OK-{{$conf.ref_pays}}{{/tr}}
          </div>
        {{else}}
          <div class="small-warning">
            {{tr}}CTarif-_precode_ready-KO-{{$conf.ref_pays}}{{/tr}}
          </div>
        {{/if}}

        {{if !$tarif->_secteur1_uptodate}}
          <div class="small-warning">
            {{tr}}CTarif-_secteur1_uptodate-KO-{{$conf.ref_pays}}{{/tr}}
          </div>
        {{/if}}

      {{else}}
        <div class="big-info">
          Pour créer un tarif contenant des codes {{if $conf.ref_pays == 1}}CCAM et NGAP{{else}}Tarmed et Prestation{{/if}}, effectuer une cotation réelle
          pendant une consultation en trois étapes :
          <ul>
            <li><em>Ajouter</em> des actes dans le volet <strong>{{tr}}CCodable-actes{{/tr}}</strong></li>
            <li><em>Valider</em> la cotation dans le volet <strong>Docs. et Règlements</strong></li>
            <li><em>Cliquer</em> <strong>Nouveau tarif</strong> dans cette même section</li>
          </ul>
        </div>
      {{/if}}
    </td>
  </tr>
  </table>
</form>