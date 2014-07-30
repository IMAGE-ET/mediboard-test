<script>

  changeCodageMode = function() {
    var form = getForm("formCodageRules-{{$codage->_id}}");
    if($V(form._association_mode)) {
      $V(form.association_mode, "user_choice");
    }
    else {
      $V(form.association_mode, "auto");
    }
    form.onsubmit();
  };

  syncCodageField = function(element, view) {
    var acteForm = getForm('codageActe-' + view);
    var fieldName = element.name;
    var fieldValue = $V(element);
    $V(acteForm[fieldName], fieldValue);
    if($V(acteForm.acte_id)) {
      acteForm.onsubmit();
    }
  };

  Main.add(function(){
    Control.Tabs.create('rules-tab', true);
  });

</script>

{{assign var="subject" value=$codage->_ref_codable}}

<h1>Actes du Dr {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$codage->_ref_praticien}}</h1>

<table class="tbl" style="min-width: 400px;">
  <tr>
    <th>{{mb_title class=CActeCCAM field=code_acte}}</th>
    <th>{{mb_title class=CActeCCAM field=code_activite}}</th>
    <th>{{mb_title class=CActeCCAM field=modificateurs}}</th>
    <th>{{mb_title class=CActeCCAM field=execution}}</th>
    <th>{{mb_title class=CActeCCAM field=montant_depassement}}</th>
    <th>{{mb_title class=CActeCCAM field=motif_depassement}}</th>
    <th>{{mb_title class=CActeCCAM field=code_association}}</th>
    <th>{{mb_title class=CActeCCAM field=_tarif}}</th>
    <th>Actions</th>
  </tr>
  {{foreach from=$subject->_ext_codes_ccam item=_code key=_key}}
  {{foreach from=$_code->activites item=_activite}}
  {{foreach from=$_activite->phases item=_phase}}
    {{assign var="acte" value=$_phase->_connected_acte}}
    {{assign var="view" value=$acte->_id|default:$acte->_view}}
    {{assign var="key" value="$_key$view"}}
    {{if (!$acte->_id && $codage->_ref_praticien->_is_anesth) || ($acte->executant_id == $codage->praticien_id)}}
      <tr>
        <td {{if !$acte->_id}}class="error"{{/if}}
            onclick="CodeCCAM.show('{{$acte->code_acte}}', '{{$subject->_class}}')"
            style="cursor: help;">
          {{if $_code->type != 2}}
            <strong>
              {{mb_value object=$acte field=code_acte}}
            </strong>
          {{else}}
            <em>{{mb_value object=$acte field=code_acte}}</em>
          {{/if}}
          {{if $_code->forfait}}
            <br />
            <small style="color: #f00">({{tr}}CDatedCodeCCAM.remboursement.{{$_code->forfait}}{{/tr}})</small>
          {{/if}}
        </td>
        <td>
            {{mb_value object=$acte field=code_activite}} : {{mb_value object=$acte field=_tarif_base}}
        </td>
        <td>
          {{foreach from=$_phase->_modificateurs item=_mod name=modificateurs}}
            <span style="border: 1px solid #abe; border-radius: 3px; margin: 1px; vertical-align: middle;">
              <input type="checkbox" name="modificateur_{{$_mod->code}}{{$_mod->_double}}" {{if $_mod->_checked}}checked="checked"{{/if}}
                     onchange="syncCodageField(this, '{{$view}}');" />
              <label for="modificateur_{{$_mod->code}}{{$_mod->_double}}" title="{{$_mod->libelle}}">
                {{$_mod->code}}{{if $_mod->_double == 2}}{{$_mod->code}}{{/if}}
              </label>
            </span>

            {{foreachelse}}
            <em>{{tr}}None{{/tr}}</em>
          {{/foreach}}
        </td>
        <td>
          <form name="codageActeExecution-{{$view}}" action="?" method="post" onsubmit="return false;">
            {{mb_field object=$acte field=execution form="codageActeExecution-$view" register=true onchange="syncCodageField(this, '$view');"}}
          </form>
        </td>
        <td>
          <form name="codageActeMontantDepassement-{{$view}}" action="?" method="post" onsubmit="return false;">
            {{mb_field object=$acte field=montant_depassement onchange="syncCodageField(this, '$view');"}}
          </form>
        </td>
        <td>
          <form name="codageActeMotifDepassement-{{$view}}" action="?" method="post" onsubmit="return false;">
            {{mb_field object=$acte field=motif_depassement emptyLabel="CActeCCAM-motif_depassement" onchange="syncCodageField(this, '$view');"}}
          </form>
        </td>
        <td
          {{if $acte->_id && ($acte->code_association != $acte->_guess_association)}}style="background-color: #fc9"{{/if}}>
          {{if $acte->_id}}
          <form name="codageActeCodeAssociation-{{$view}}" action="?" method="post" onsubmit="return false;">
            {{mb_field object=$acte field=code_association emptyLabel="CActeCCAM.code_association." onchange="syncCodageField(this, '$view');"}}
          </form>
          {{if $acte->code_association != $acte->_guess_association}}
            ({{$acte->_guess_association}})
          {{/if}}
          {{/if}}
        </td>
        <td {{if $acte->_id && !$acte->facturable}}style="background-color: #fc9"{{/if}}>
          {{mb_value object=$acte field=_tarif}}
        </td>
        <td class="text">
          <form name="codageActe-{{$view}}" action="?" method="post"
          onsubmit="return onSubmitFormAjax(this, {onComplete: function() {window.urlCodage.refreshModal()}});">
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
            {{mb_field object=$acte field=executant_id hidden=true value=$codage->praticien_id}}
            {{mb_field object=$acte field=execution hidden=true}}
            {{mb_field object=$acte field=montant_depassement hidden=true}}
            {{mb_field object=$acte field=motif_depassement hidden=true emptyLabel="CActeCCAM-motif_depassement"}}

            {{foreach from=$_phase->_modificateurs item=_mod name=modificateurs}}
              <input type="checkbox" name="modificateur_{{$_mod->code}}{{$_mod->_double}}" {{if $_mod->_checked}}checked="checked"{{/if}} hidden="hidden" />
            {{/foreach}}

            {{if !$acte->_id}}
              <button class="add notext" type="submit">{{tr}}Add{{/tr}}
              </button>
            {{else}}
              <button class="edit notext" type="button" onclick="ActesCCAM.edit({{$acte->_id}})">{{tr}}Edit{{/tr}}</button>
              <button class="trash notext" type="button"
                      onclick="confirmDeletion(this.form,{typeName:'l\'acte',objName:'{{$acte->_view|smarty:nodefaults|JSAttribute}}', ajax: '1'},
                        {onComplete: function() {window.urlCodage.refreshModal()}});">
                {{tr}}Delete{{/tr}}
              </button>
            {{/if}}
          </form>
        </td>
      </tr>
    {{/if}}
  {{/foreach}}
  {{/foreach}}
  {{/foreach}}
</table>

<ul id="rules-tab" class="control_tabs">
  <li><a href="#questionRules">Informations médicales</a></li>
  <li><a href="#concreteRules">Règles de codage</a></li>
</ul>

<hr class="control_tabs" />

<div style="display: none;" id="questionRules">
  <table class="tbl">
    <tr>
      <th class="title" colspan="2">Les actes que vous codez répondent-ils à un des critères suivants ?</th>
    </tr>
    <tr>
      <th>
        <input type="radio" name="_association_question" value="EA"
               {{if $codage->association_rule == "EA"}}checked="checked"{{/if}}
               onchange="this.form.onsubmit()"/>
      </th>
      <td>
        Les actes portent sur :
        <ul>
          <li>des membres différents ou</li>
          <li>le tronc et un membre ou</li>
          <li>la tête et un membre.</li>
        </ul>
      </td>
    </tr>
    <tr>
      <th>
        <input type="radio" name="_association_question" value="EB"
               {{if $codage->association_rule == "EB"}}checked="checked"{{/if}}
               onchange="this.form.onsubmit()"/>
      </th>
      <td>
        Les actes visent à traiter des lésions traumatiques multiples et récentes
      </td>
    </tr>
    <tr>
      <th>
        <input type="radio" name="_association_question" value="EC"
               {{if $codage->association_rule == "EC"}}checked="checked"{{/if}}
               onchange="this.form.onsubmit()"/>
      </th>
      <td>
        Les actes décrivent une intervention de carcinologie ORL comprenant :
        <ul>
          <li>une exérèse et</li>
          <li>un curage et</li>
          <li>une reconstruction.</li>
        </ul>
      </td>
    </tr>
    <tr>
      <th>
        <input type="radio" name="_association_question" value="EC"
               {{if $codage->association_rule == "EC"}}checked="checked"{{/if}}
               onchange="this.form.onsubmit()"/>
      </th>
      <td>
        Les actes sont des actes d'imagerie portant su plusieurs régions anatomiques.
      </td>
    </tr>
  </table>
</div>
<div style="display: none;" id="concreteRules">
  <form name="formCodageRules-{{$codage->_id}}" action="?" method="post"
        onsubmit="return onSubmitFormAjax(this, {onComplete: function() {window.urlCodage.refreshModal()}});">
    <input type="hidden" name="m" value="ccam" />
    <input type="hidden" name="dosql" value="do_codageccam_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="codage_ccam_id" value="{{$codage->_id}}" />
    <input type="hidden" name="association_mode" value="{{$codage->association_mode}}" />
    <table class="tbl">
      <tr>
        <th colspan="2">
          <input type="checkbox" name="_association_mode" value="manuel"
                 {{if $codage->association_mode == "user_choice"}}checked="checked"{{/if}}
                 onchange="changeCodageMode();"/>
          Manuel
        </th>
        <th class="title" colspan="20">
          Règles d'association
        </th>
      </tr>
      {{assign var=association_rules value="CCodageCCAM"|static:"association_rules"}}
      {{foreach from=$codage->_possible_rules key=_rulename item=_rule}}
        {{if $_rule || 1}}
          <tr>
            <td {{if $_rulename == $codage->association_rule}}class="ok"{{/if}}>
              <input type="radio" name="association_rule" value="{{$_rulename}}"
                     {{if $_rulename == $codage->association_rule}}checked="checked"{{/if}}
                {{if $codage->association_mode == "auto"}}disabled="disabled"{{/if}}
                     onchange="this.form.onsubmit()"/>
            </td>
            <td class="{{if $_rule}}ok{{else}}error{{/if}}">
              {{$_rulename}} {{if $association_rules.$_rulename == 'ask'}}(manuel){{/if}}
            </td>
            <td class="text greedyPane">
              {{tr}}CActeCCAM-regle-association-{{$_rulename}}{{/tr}}
            </td>
          </tr>
        {{/if}}
      {{/foreach}}
    </table>
  </form>
</div>