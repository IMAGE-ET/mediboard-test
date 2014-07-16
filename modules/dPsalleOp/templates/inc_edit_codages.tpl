<h1>Actes du Dr {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$codage->_ref_praticien}}</h1>

<table class="tbl">
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

<table class="tbl">
  <tr>
    <th class="title" colspan="20">
      Règles d'association
    </th>
  </tr>
  <tr>
    {{foreach from=$codage->_possible_rules key=_rulename item=_rule}}
      {{if $_rule}}
        <td class="ok">{{$_rulename}}</td>
        {{else}}
        <td class="error">{{$_rulename}}</td>
      {{/if}}
    {{/foreach}}
  </tr>
</table>