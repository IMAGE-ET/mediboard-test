<table class="tbl">
  <tr>
    <th class="category">{{tr}}Delete{{/tr}}</th>
    <th class="category">{{mb_title class=CActeCCAM field=executant_id}}</th>
    <th class="category">{{mb_title class=CActeCCAM field=code_acte}}</th>
    <th class="category">{{mb_title class=CActeCCAM field=code_activite}}</th>
    <th class="category">{{mb_title class=CActeCCAM field=code_phase}}</th>
    <th class="category">{{mb_title class=CActeCCAM field=modificateurs}}</th>
    <th class="category">{{mb_title class=CActeCCAM field=code_association}}</th>
    <th class="category">{{mb_title class=CActeCCAM field=montant_depassement}}</th>
    <th class="category">{{mb_title class=CActeCCAM field=_rembex}}</th>
  </tr>
  {{foreach from=$curr_op->_ref_actes_ccam item=curr_acte}}
  <!-- Couleur de l'acte -->
  {{if $curr_acte->code_association == $curr_acte->_guess_association}}
    {{assign var=bg_color value=9f9}}
  {{else}}
    {{assign var=bg_color value=fc9}}
  {{/if}}
  <tr>
    <td class="button">
      <form name="formDelActe-{{$curr_acte->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPsalleOp" />
      <input type="hidden" name="dosql" value="do_acteccam_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="acte_id" value="{{$curr_acte->acte_id}}" />
      <button class="trash notext" type="button" onclick="confirmDeletion(this.form, {typeName:'l\'acte',objName:'{{$curr_acte->code_acte|smarty:nodefaults|JSAttribute}}'})">
        {{tr}}Ajouter{{/tr}}
      </button>
      </form>
    </td>
    <td class="text">{{$curr_acte->_ref_executant->_view}}</td>
    <td class="button">{{mb_value object=$curr_acte field=code_acte}}</td>
    <td class="button">{{mb_value object=$curr_acte field=code_activite}}</td>
    <td class="button">{{mb_value object=$curr_acte field=code_phase}}</td>
    <td class="button">{{mb_value object=$curr_acte field=modificateurs}}</td>
    <td class="button" style="background-color: #{{$bg_color}};">
      <form name="formAssoActe-{{$curr_acte->_id}}" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, { onComplete: function() {reloadListActes({{$curr_op->_id}});} })">
      <input type="hidden" name="m" value="dPsalleOp" />
      <input type="hidden" name="dosql" value="do_acteccam_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="acte_id" value="{{$curr_acte->acte_id}}" />
      <select name="code_association" onchange="this.form.onsubmit()">
        <option value="" {{if !$curr_acte->code_association}}selected="selected"{{/if}}
        style="border-left: 4px solid #{{if !$curr_acte->_guess_association}}9f9{{else}}fc9{{/if}};">
          -
        </option>
        <option value="1" {{if $curr_acte->code_association == 1}}selected="selected"{{/if}}
        style="border-left: 4px solid #{{if $curr_acte->_guess_association == 1}}9f9{{else}}fc9{{/if}};">
          1
        </option>
        <option value="2" {{if $curr_acte->code_association == 2}}selected="selected"{{/if}}
        style="border-left: 4px solid #{{if $curr_acte->_guess_association == 2}}9f9{{else}}fc9{{/if}};">
          2
        </option>
        <option value="3" {{if $curr_acte->code_association == 3}}selected="selected"{{/if}}
        style="border-left: 4px solid #{{if $curr_acte->_guess_association == 3}}9f9{{else}}fc9{{/if}};">
          3
        </option>
        <option value="4" {{if $curr_acte->code_association == 4}}selected="selected"{{/if}}
        style="border-left: 4px solid #{{if $curr_acte->_guess_association == 4}}9f9{{else}}fc9{{/if}};">
          4
        </option>
        <option value="5" {{if $curr_acte->code_association == 5}}selected="selected"{{/if}}
        style="border-left: 4px solid #{{if $curr_acte->_guess_association == 5}}9f9{{else}}fc9{{/if}};">
          5
        </option>
      </select>
      </form>
    </td>
    <td class="button">{{mb_value object=$curr_acte field=montant_depassement}}</td>
    <td class="button">{{mb_value object=$curr_acte field=_rembex}}</td>
  </tr>
  {{/foreach}}
</table>