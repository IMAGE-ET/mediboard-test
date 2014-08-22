{{mb_default var=read_only value=false}}
{{mb_default var=show_ccam value=true}}
{{mb_default var=show_ngap value=true}}

<table class="main layout">
  {{if $show_ccam}}
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th class="title" colspan="20">{{tr}}CActeCCAM{{/tr}}</th>
        </tr>
        <tr>
          <th class="narrow">{{mb_title class=CActeCCAM field=code_acte}}</th>
          <th colspan="2" class="narrow">{{mb_title class=CActeCCAM field=code_activite}}</th>
          <th class="narrow">{{mb_title class=CActeCCAM field=executant_id}}</th>
          <th>{{mb_title class=CActeCCAM field=modificateurs}}</th>
          <th class="narrow">{{mb_title class=CActeCCAM field=execution}}</th>
          <th class="narrow">{{mb_title class=CActeCCAM field=montant_depassement}}</th>
          <th class="narrow">{{mb_title class=CActeCCAM field=motif_depassement}}</th>
          <th class="narrow">{{mb_title class=CActeCCAM field=code_association}}</th>
          <th>{{mb_title class=CActeCCAM field=_tarif}}</th>
          {{if !$read_only}}
          <th class="narrow">Actions</th>
          {{/if}}
        </tr>
        {{foreach from=$subject->_ext_codes_ccam item=_code key=_key}}
          {{foreach from=$_code->activites item=_activite}}
            {{foreach from=$_activite->phases item=_phase}}
              {{assign var="acte" value=$_phase->_connected_acte}}
              {{assign var="view" value=$acte->_id|default:$acte->_view}}
              {{assign var="key" value="$_key$view"}}
              <tr>
                <td {{if !$acte->_id}}class="error"{{/if}}>
                  <a href="#" onclick="CodeCCAM.show('{{$acte->code_acte}}', '{{$subject->_class}}')">
                    {{if $_code->type != 2}}
                      <strong>
                        {{mb_value object=$acte field=code_acte}}
                      </strong>
                    {{else}}
                      <em>{{mb_value object=$acte field=code_acte}}</em>
                    {{/if}}
                  </a>
                  {{if $_code->forfait}}
                    <br />
                    <small style="color: #f00">({{tr}}CDatedCodeCCAM.remboursement.{{$_code->forfait}}{{/tr}})</small>
                  {{/if}}
                </td>
                <td class="narrow">
                  <span class="circled {{if $acte->_id}}ok{{else}}error{{/if}}">
                    {{mb_value object=$acte field=code_activite}}
                  </span>
                </td>
                <td>
                  {{mb_value object=$acte field=_tarif_base}}
                </td>
                <td>
                  {{if $read_only}}
                    {{mb_value object=$acte field=executant_id}}
                  {{else}}
                    {{mb_field object=$acte field=executant_id options=$listPrats onchange="CCodageCCAM.syncCodageField(this, '$view');"}}
                  {{/if}}
                </td>
                <td class="greedyPane text">
                  {{assign var=nb_modificateurs value=$acte->modificateurs|strlen}}
                  {{foreach from=$_phase->_modificateurs item=_mod name=modificateurs}}
                    <span class="circled {{if $_mod->_state == 'prechecked'}}ok{{elseif $_mod->_checked && in_array($_mod->_state, array('not_recommended', 'forbidden'))}}error{{elseif in_array($_mod->_state, array('not_recommended', 'forbidden'))}}warning{{/if}}"
                          title="{{$_mod->libelle}} ({{$_mod->_montant}})">
                      {{if !$read_only}}
                        <input type="checkbox" name="modificateur_{{$_mod->code}}{{$_mod->_double}}" {{if $_mod->_checked}}checked="checked"{{elseif $nb_modificateurs == 4 || $_mod->_state == 'forbidden' || (intval($acte->_exclusive_modifiers) > 0 && in_array($_mod->code, array('F', 'U', 'P', 'S')))}}disabled="disabled"{{/if}}
                               data-acte="{{$view}}" data-code="{{$_mod->code}}" data-double="{{$_mod->_double}}" class="modificateur" onchange="CCodageCCAM.syncCodageField(this, '{{$view}}');" />
                      {{/if}}
                      <label for="modificateur_{{$_mod->code}}{{$_mod->_double}}">
                        {{$_mod->code}}
                      </label>
                    </span>
                    {{foreachelse}}
                    <em>{{tr}}None{{/tr}}</em>
                  {{/foreach}}
                </td>
                <td>
                    {{if $read_only}}
                      {{mb_value object=$acte field=execution}}
                    {{else}}
                      <form name="codageActeExecution-{{$view}}" action="?" method="post" onsubmit="return false;">
                        {{mb_field object=$acte field=execution form="codageActeExecution-$view" register=true onchange="CCodageCCAM.syncCodageField(this, '$view');"}}
                      </form>
                    {{/if}}
                </td>
                <td>
                  {{if $read_only}}
                    {{mb_value object=$acte field=montant_depassement}}
                  {{else}}
                    <form name="codageActeMontantDepassement-{{$view}}" action="?" method="post" onsubmit="return false;">
                      {{mb_field object=$acte field=montant_depassement onchange="CCodageCCAM.syncCodageField(this, '$view');"}}
                    </form>
                  {{/if}}
                </td>
                <td>
                  {{if $read_only}}
                    {{mb_value object=$acte field=motif_depassement}}
                  {{else}}
                    <form name="codageActeMotifDepassement-{{$view}}" action="?" method="post" onsubmit="return false;">
                      {{mb_field object=$acte field=motif_depassement emptyLabel="CActeCCAM-motif_depassement" onchange="CCodageCCAM.syncCodageField(this, '$view');"}}
                    </form>
                  {{/if}}
                </td>
                <td
                  {{if $acte->_id && ($acte->code_association != $acte->_guess_association)}}style="background-color: #fc9"{{/if}}>
                  {{if $read_only}}
                    {{mb_value object=$acte field=code_association}}
                  {{else}}
                    {{if $acte->_id}}
                      <form name="codageActeCodeAssociation-{{$view}}" action="?" method="post" onsubmit="return false;">
                        {{mb_field object=$acte field=code_association emptyLabel="CActeCCAM.code_association." onchange="CCodageCCAM.syncCodageField(this, '$view');"}}
                      </form>
                      {{if $acte->code_association != $acte->_guess_association}}
                        ({{$acte->_guess_association}})
                      {{/if}}
                    {{/if}}
                  {{/if}}
                </td>
                <td {{if $acte->_id && !$acte->facturable}}style="background-color: #fc9"{{/if}}>
                  {{mb_value object=$acte field=_tarif}}
                </td>
                {{if !$read_only}}
                <td class="button">
                  <form name="codageActe-{{$view}}" action="?" method="post"
                        onsubmit="return onSubmitFormAjax(this, {onComplete: function() {PMSI.loadActes({{$sejour->_id}})}});">
                    <input type="hidden" name="m" value="salleOp" />
                    <input type="hidden" name="dosql" value="do_acteccam_aed" />
                    <input type="hidden" name="del" value="0" />
                    {{mb_key object=$acte}}

                    <input type="hidden" name="_calcul_montant_base" value="1" />
                    <input type="hidden" name="_edit_modificateurs" value="1"/>

                    {{mb_field object=$acte field=object_id hidden=true value=$subject->_id}}
                    {{mb_field object=$acte field=object_class hidden=true value=$subject->_class}}
                    {{mb_field object=$acte field=code_acte hidden=true}}
                    {{mb_field object=$acte field=code_activite hidden=true}}
                    {{mb_field object=$acte field=code_phase hidden=true}}
                    {{mb_field object=$acte field=code_association hidden=true emptyLabel="None"}}
                    {{mb_field object=$acte field=executant_id hidden=true}}
                    {{mb_field object=$acte field=execution hidden=true}}
                    {{mb_field object=$acte field=montant_depassement hidden=true}}
                    {{mb_field object=$acte field=motif_depassement hidden=true emptyLabel="CActeCCAM-motif_depassement"}}

                    {{foreach from=$_phase->_modificateurs item=_mod name=modificateurs}}
                      <input type="checkbox" name="modificateur_{{$_mod->code}}{{$_mod->_double}}" {{if $_mod->_checked}}checked="checked"{{/if}} hidden="hidden" />
                    {{/foreach}}

                    {{if !$acte->_id}}
                      <button class="add notext compact" type="submit">
                        {{tr}}Add{{/tr}}
                      </button>
                    {{else}}
                      <button class="edit notext compact" type="button" onclick="CCodageCCAM.editActe({{$acte->_id}}, {{$sejour->_id}})">{{tr}}Edit{{/tr}}</button>
                      <button class="trash notext compact" type="button"
                              onclick="confirmDeletion(this.form,{typeName:'l\'acte',objName:'{{$acte->_view|smarty:nodefaults|JSAttribute}}', ajax: '1'},
                                {onComplete: function() {PMSI.loadActes({{$sejour->_id}})}});">
                        {{tr}}Delete{{/tr}}
                      </button>
                    {{/if}}
                  </form>
                </td>
                {{/if}}
              </tr>
            {{/foreach}}
          {{/foreach}}
        {{foreachelse}}
          <tr>
            <td class="empty" colspan="20">{{tr}}CActeCCAM.none{{/tr}}</td>
          </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
  {{/if}}
  {{if $show_ngap}}
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th class="title" colspan="20">Actes NGAP</th>
        </tr>
        <tr>
          <th class="category">{{mb_title class=CActeNGAP field=code}}</th>
          <th class="category">{{mb_title class=CActeNGAP field=executant_id}}</th>
          <th class="category">{{mb_title class=CActeNGAP field=quantite}}</th>
          <th class="category">{{mb_title class=CActeNGAP field=montant_base}}</th>
          <th class="category">{{mb_title class=CActeNGAP field=montant_depassement}}</th>
        </tr>
        {{foreach from=$subject->_ref_actes_ngap item=acte_ngap}}
          <tr>
            <td class="button">{{mb_value object=$acte_ngap field=code}}</td>
            <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$acte_ngap->_ref_executant}}</td>
            <td class="button">{{mb_value object=$acte_ngap field=quantite}}</td>
            <td class="button">{{mb_value object=$acte_ngap field=montant_base}}</td>
            <td class="button">{{mb_value object=$acte_ngap field=montant_depassement}}</td>
          </tr>
          {{foreachelse}}
          <tr>
            <td class="empty" colspan="20">{{tr}}CActeNGAP.none{{/tr}}</td>
          </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
  {{/if}}
</table>