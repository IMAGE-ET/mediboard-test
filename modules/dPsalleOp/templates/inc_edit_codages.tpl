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
  }

</script>

<h1>Actes du Dr {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$codage->_ref_praticien}}</h1>

<table class="tbl" style="min-width: 400px;">
  <tr>
    <th>Acte</th>
    <th>Activité</th>
    <th>Base</th>
    <th>DH</th>
    <th>Modifs</th>
    <th>Asso</th>
    <th>Tarif</th>
  </tr>
  {{foreach from=$codage->_ref_actes_ccam item=_acte}}
    <tr>
      <td>
        {{mb_value object=$_acte field=code_acte}}
      </td>
      <td>
        {{mb_value object=$_acte field=code_activite}}
      </td>
      <td>
        {{mb_value object=$_acte field=_tarif_base}}
      </td>
      <td>
        {{mb_value object=$_acte field=montant_depassement}}
      </td>
      <td>
        {{mb_value object=$_acte field=modificateurs}}
      </td>
      <td>
        {{mb_value object=$_acte field=code_association}}
      </td>
      <td>
        {{mb_value object=$_acte field=_tarif}}
      </td>
    </tr>
  {{/foreach}}
</table>

<form name="formCodageRules-{{$codage->_id}}" action="?" method="post"
      onsubmit="return onSubmitFormAjax(this, {onComplete: function() {window.urlCodage.refreshModal()}});">
  <input type="hidden" name="m" value="ccam" />
  <input type="hidden" name="dosql" value="do_codageccam_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="codage_ccam_id" value="{{$codage->_id}}" />
  <input type="hidden" name="association_mode" value="{{$codage->association_mode}}" />
  <table class="tbl" style="min-width: 400px;">
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
    {{/foreach}}
  </table>
</form>