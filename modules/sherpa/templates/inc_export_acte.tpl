<tr>
  <td>{{$_acte_ccam->_ref_executant->_view}}</td>
  <td>{{mb_value object=$_acte_ccam field=execution}}</td>
  <td>{{mb_value object=$_acte_ccam field=code_acte}}</td>
  <td>{{mb_value object=$_acte_ccam field=code_activite}}</td>
  <td>{{mb_value object=$_acte_ccam field=code_phase}}</td>
  <td>{{mb_value object=$_acte_ccam field=modificateurs}}</td>
  <td>{{mb_value object=$_acte_ccam field=code_association}}</td>
  <td>{{mb_value object=$_acte_ccam field=montant_base}}</td>
  <td>{{mb_value object=$_acte_ccam field=montant_depassement}}</td>
  <td>
    {{assign var=acte_id value=$_acte_ccam->_id}}
    {{if $exports.$acte_id}}
    <div class="error">{{$exports.$acte_id}}</div>
    {{else}}
    <div class="message">Acte correctement exporté</div>
    {{/if}}
  </td>
</tr>
