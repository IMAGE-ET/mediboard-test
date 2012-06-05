<tr>
  <td style="width: 25%;">
    <strong>{{mb_title object=$patient->_ref_constantes_medicales field=poids}}:</strong>
    {{if $patient->_ref_constantes_medicales->poids}}
      {{mb_value object=$patient->_ref_constantes_medicales field=poids}} kg
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
      {{mb_value object=$patient->_ref_constantes_medicales field=taille}} cm
    {{else}}
    &mdash;
    {{/if}}
  </td>
  <td style="width: 25%;">
    <strong>{{mb_title object=$patient->_ref_constantes_medicales field=_imc}}:</strong>
    {{if $patient->_ref_constantes_medicales->_imc}}
      {{mb_value object=$patient->_ref_constantes_medicales field=_imc}}
    {{else}}
    &mdash;
    {{/if}}
  </td>
</tr>