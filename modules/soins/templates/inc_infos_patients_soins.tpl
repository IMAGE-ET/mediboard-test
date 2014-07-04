{{mb_default var=add_class value=0}}

<tr>
  <td style="width: 25%;">
    <strong>{{mb_title object=$patient->_ref_constantes_medicales field=poids}}:</strong>
    <span {{if $add_class}}class="poids_patient"{{/if}}>
      {{if $patient->_ref_constantes_medicales->poids}}
        {{mb_value object=$patient->_ref_constantes_medicales field=poids}} kg
      {{else}}
        &mdash;
      {{/if}}
    </span>
  </td>
  <td style="width: 25%;">
    <strong>{{mb_title object=$patient field=naissance}}:</strong>
    {{mb_value object=$patient field=naissance}} ({{$patient->_age}})
  </td>
  <td style="width: 25%;">
    <strong>{{mb_title object=$patient->_ref_constantes_medicales field=taille}}:</strong>
    <span {{if $add_class}}class="taille_patient"{{/if}}>
      {{if $patient->_ref_constantes_medicales->taille}}
        {{mb_value object=$patient->_ref_constantes_medicales field=taille}} cm
      {{else}}
        &mdash;
      {{/if}}
    </span>
  </td>
  <td style="width: 25%;">
    <strong>{{mb_title object=$patient->_ref_constantes_medicales field=_imc}}:</strong>
    <span {{if $add_class}}class="imc_patient"{{/if}}>
      {{if $patient->_ref_constantes_medicales->_imc}}
        {{mb_value object=$patient->_ref_constantes_medicales field=_imc}}
      {{else}}
        &mdash;
      {{/if}}
    </span>
  </td>
</tr>