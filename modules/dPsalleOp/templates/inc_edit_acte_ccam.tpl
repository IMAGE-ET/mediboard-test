
<script type="text/javascript">
  checkModificateurs = function(input) {
    var exclusive_modifiers = ['F', 'P', 'S', 'U'];
    var checkboxes = $$('input.modificateur');
    var nb_checked = 0;
    var exclusive_modifier = '';
    var exclusive_modifier_checked = false;
    checkboxes.each(function(checkbox) {
      if (checkbox.checked) {
        nb_checked++;
        if (exclusive_modifiers.indexOf(checkbox.get('code')) != -1) {
          exclusive_modifier = checkbox.get('code');
          exclusive_modifier_checked = true;
        }
      }
    });

    checkboxes.each(function(checkbox) {
      if (
          (!checkbox.checked && nb_checked == 4) ||
          (exclusive_modifiers.indexOf(exclusive_modifier) != -1 && exclusive_modifiers.indexOf(checkbox.get('code')) != -1 && !checkbox.checked && exclusive_modifier_checked)
         ) {
        checkbox.disabled = true;
      }
      else {
        checkbox.disabled = false;
      }
    });

    var container = input.up();
    if (input.checked == true && container.hasClassName('warning')) {
      container.removeClassName('warning');
      container.addClassName('error');
    }
    else if (input.checked == false && container.hasClassName('error')) {
      container.removeClassName('error');
      container.addClassName('warning');
    }
  }
</script>

{{assign var="view" value=$acte->_id}}
<form name="formEditFullActe-{{$view}}" action="?" method="post"
      onsubmit="return onSubmitFormAjax(this, {onComplete: function() { window.urlEditActe.modalObject.close() }});">

  <input type="hidden" name="m" value="salleOp" />
  <input type="hidden" name="dosql" value="do_acteccam_aed" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$acte}}

  <input type="hidden" name="_calcul_montant_base" value="1" />
  <input type="hidden" name="_edit_modificateurs" value="1"/>

  {{mb_field object=$acte field=object_id hidden=true}}
  {{mb_field object=$acte field=object_class hidden=true}}
  {{mb_field object=$acte field=code_acte hidden=true}}
  {{mb_field object=$acte field=code_activite hidden=true}}
  {{mb_field object=$acte field=code_phase hidden=true}}

  <table class="form" style="min-width: 400px;">
    <tr>
      <th class="title" colspan="10">
        {{mb_include module=system template=inc_object_idsante400 object=$acte}}
        {{mb_include module=system template=inc_object_history object=$acte}}
        {{$acte->_ref_code_ccam->code}} :
        <span style="font-weight: normal;">{{$acte->_ref_code_ccam->libelleLong}}</span>
        <br />
        <span style="font-weight: normal;">
          <span title="Activité de l'acte">Activité {{$activite->numero}} ({{$activite->type}})</span> &mdash;
          <span title="Phase de l'acte">Phase {{$phase->phase}}</span> &mdash;
          <span title="Tarif de base de l'activité">{{$acte->_tarif_base|currency}}</span>
        </span>
      </th>
    </tr>

    <!-- Date d'execution -->
    <tr>
      <th>{{mb_label object=$acte field=execution}}</th>
      <td>{{mb_field object=$acte field=execution form="formEditFullActe-$view" register=true}}</td>
    </tr>

    <!-- Executant -->
    <tr>
      <th>{{mb_label object=$acte field=executant_id}}</th>
      <td>
        {{mb_ternary var=listExecutants test=$acte->_anesth value=$listAnesths other=$listChirs}}
        <select name="executant_id" class="{{$acte->_props.executant_id}}" style="width: 15em;">
          <option value="">&mdash; Choisir un professionnel de santé</option>
          {{mb_include module=mediusers template=inc_options_mediuser selected=$acte->executant_id list=$listExecutants}}
        </select>
      </td>
    </tr>

    <!-- Extension documentaire -->
    {{if $acte->_anesth}}
      <tr>
        <th>{{mb_label object=$acte field=extension_documentaire}}</th>
        <td>
          {{mb_field object=$acte field=extension_documentaire emptyLabel="Choose"
          canNull=$conf.dPsalleOp.CActeCCAM.ext_documentaire_optionnelle|ternary:true:false style="width: 15em;"}}
        </td>
      </tr>
    {{/if}}


    <!-- Modificateurs -->
    <tr>
      <th>{{mb_label object=$acte field=modificateurs}}</th>
      <td class="text" colspan="10">
        {{assign var=nb_modificateurs value=$acte->modificateurs|strlen}}
        {{foreach from=$phase->_modificateurs item=_mod name=modificateurs}}
          <span class="circled {{if $_mod->_state == 'prechecked'}}ok{{elseif $_mod->_checked && in_array($_mod->_state, array('not_recommended', 'forbidden'))}}error{{elseif in_array($_mod->_state, array('not_recommended', 'forbidden'))}}warning{{/if}}">
            <input type="checkbox" class="modificateur" data-code="{{$_mod->code}}" name="modificateur_{{$_mod->code}}{{$_mod->_double}}" {{if $_mod->_checked}}checked="checked"{{elseif $nb_modificateurs == 4 || $_mod->_state == 'forbidden' || (intval($acte->_exclusive_modifiers) > 0 && in_array($_mod->code, array('F', 'U', 'P', 'S')))}}disabled="disabled"{{/if}} onchange="checkModificateurs(this);"/>
            <label for="modificateur_{{$_mod->code}}{{$_mod->_double}}">
              {{$_mod->code}}{{if $_mod->_double == 2}}{{$_mod->code}}{{/if}}
            </label>
          </span>
          <span>{{$_mod->libelle}}</span>
          <br />
          {{foreachelse}}
          <em>{{tr}}None{{/tr}}</em>
        {{/foreach}}
      </td>
    </tr>

    <!-- Dents -->
    {{if $phase->nb_dents}}
      <tr>
        <th>Dents concernées ({{$phase->nb_dents}} à cocher)</th>
        <td class="text" colspan="10">
          {{foreach from=$liste_dents item=_dent}}
            {{assign var=dent_ok value=true}}
            {{foreach from=$phase->dents_incomp item=_incomp}}
              {{if $_dent->localisation == $_incomp->localisation}}
                {{assign var=dent_ok value=false}}
              {{/if}}
            {{/foreach}}
            {{if $dent_ok}}
              <span style="border: 1px solid #abe; background-color: #eee; border-radius: 3px; margin: 1px; vertical-align: middle;">
                <input type="checkbox" name="dent_{{$_dent->localisation}}" {{if in_array($_dent->localisation, $acte->_dents)}}checked="checked"{{/if}} />
                <label for="dent_{{$_dent->localisation}}" title="Localisation : {{$_dent->localisation}}">{{$_dent->_libelle}}</label>
              </span>
            {{else}}
              <span style="border: 1px solid #abe; background-color: #fdd; border-radius: 3px; margin: 1px; vertical-align: middle; display: none;">
                {{$_dent->_libelle}}
              </span>
            {{/if}}
          {{/foreach}}
        </td>
      </tr>
    {{/if}}

    <!-- Remboursable -->
    <tr>
      <th>
        {{mb_label object=$acte field=rembourse}}<br />
        <small><em>({{tr}}CDatedCodeCCAM.remboursement.{{$code->remboursement}}{{/tr}})</em></small>
      </th>
      <td>
        {{assign var=disabled value=""}}
        {{if $code->remboursement == 1}}{{assign var=disabled value=0}}{{/if}}
        {{if $code->remboursement == 2}}{{assign var=disabled value=1}}{{/if}}

        {{assign var=default value="1"}}
        {{if $code->remboursement == 1}}{{assign var=default value=1}}{{/if}}
        {{if $code->remboursement == 2}}{{assign var=default value=0}}{{/if}}

        {{mb_field object=$acte field=rembourse disabled=$disabled default=$default}}
      </td>
    </tr>

    <!-- Acte gratuit -->
    <tr>
      <th>
        {{mb_label object=$acte field=gratuit}}
      </th>
      <td>
        {{mb_field object=$acte field=gratuit}}
      </td>
    </tr>

    <!-- Facturable -->
    <tr>
      <th>
        {{mb_label object=$acte field=facturable}}
      </th>
      <td>
        {{if $acte->_tarif_base == 0}}
          Non
          <input name="facturable" value="0" hidden="hidden" />
        {{else}}
          {{mb_field object=$acte field=facturable}}
        {{/if}}
      </td>
    </tr>

    <!-- Dépassement d'honoraire -->
    {{if $acte->facturable && $acte->_tarif_base != 0}}
      <tr>
        <th>{{mb_label object=$acte field=montant_depassement}}</th>
        <td>
          {{mb_field object=$acte field=montant_depassement}}
          {{mb_field object=$acte field=motif_depassement emptyLabel="CActeCCAM-motif_depassement" style="width: 15em;"}}
        </td>
      </tr>
    {{/if}}

    <!-- Code d'Association -->
    <tr>
      <th>{{mb_label object=$acte field=code_association}}</th>
      <td>
        {{mb_field object=$acte field=code_association emptyLabel="CActeCCAM.code_association." style="width: 15em;"}}
      </td>
    </tr>

    <!-- Commentaires -->
    <tr>
      <th>{{mb_label object=$acte field=commentaire}}</th>
      <td>{{mb_field object=$acte field=commentaire}}</td>
    </tr>

    <tr>
      <td class="button" colspan="10">
        <button type="button" class="save" onclick="this.form.onsubmit();">
          {{tr}}Save{{/tr}}
        </button>
        <button type="button" class="cancel" onclick="window.urlEditActe.modalObject.close();">
          {{tr}}Cancel{{/tr}}
        </button>
      </td>
    </tr>

  </table>
</form>