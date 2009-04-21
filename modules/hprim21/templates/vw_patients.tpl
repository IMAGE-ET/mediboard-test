{{* $Id$ *}}

<table class="main">
  <tr>
    <td class="halfPane">
      {{include file="inc_list_patient.tpl"}}
    </td>

    {{if $patient->_id}}
    <td class="halfPane" id="vwPatient">
      {{include file="inc_vw_patient.tpl"}}
    </td>
    {{/if}}
  </tr>
</table>