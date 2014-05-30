{{mb_default var=add_class value=0}}

<tr>
  <td style="width: 25%;">
    <strong>{{mb_title object=$patient->_ref_constantes_medicales field=poids}}:</strong>
    {{if $patient->_ref_constantes_medicales->poids}}
      <span {{if $add_class}}class="poids_patient"{{/if}}>{{mb_value object=$patient->_ref_constantes_medicales field=poids}}</span> kg
    {{else}}
    &mdash;
    {{/if}}
  </td>
  <td style="width: 25%;">
    <strong>{{mb_title object=$patient field=naissance}}:</strong>
    {{mb_value object=$patient field=naissance}} ({{$patient->_age}})
  </td>
  <td style="width: 25%;">
    <strong>{{mb_title object=$patient->_ref_constantes_medicales field=taille}}:</strong>
    {{if $patient->_ref_constantes_medicales->taille}}
      <span {{if $add_class}}class="taille_patient"{{/if}}>{{mb_value object=$patient->_ref_constantes_medicales field=taille}}</span> cm
    {{else}}
    &mdash;
    {{/if}}
  </td>
  <td style="width: 25%;">
    <strong>{{mb_title object=$patient->_ref_constantes_medicales field=_imc}}:</strong>
    {{if $patient->_ref_constantes_medicales->_imc}}
      <span {{if $add_class}}class="imc_patient"{{/if}}>{{mb_value object=$patient->_ref_constantes_medicales field=_imc}}</span>
    {{else}}
    &mdash;
    {{/if}}
  </td>
</tr>