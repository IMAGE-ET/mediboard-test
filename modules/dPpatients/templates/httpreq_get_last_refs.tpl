<table width="100%">
  <tr>
    <td><strong>{$patient->_view} &mdash; {$patient->_age} ans</strong></td>
  </tr>
  {if $patient->_ref_operations|@count == 0}
  <tr>
    <td>Aucune intervention</td>
  </tr>
  {/if}
  {foreach from=$patient->_ref_operations item=curr_op}
  <tr>
    <td class="text">
      <input type="radio" name="_operation_id" value="{$curr_op->operation_id}" />
      Intervention le {$curr_op->_ref_plageop->date|date_format:"%d/%m/%Y"}
      avec le Dr. {$curr_op->_ref_chir->_view}
      {if $curr_op->_ext_codes_ccam|@count}
      <ul>
        {foreach from=$curr_op->_ext_codes_ccam item=curr_code}
        <li><i>{$curr_code->libelleLong}</i></li>
        {/foreach}
      </ul>
      {/if}
    </td>
  </tr>
  {/foreach}
  {if $patient->_ref_consultations|@count == 0}
  <tr>
    <td>Aucune consultation</td>
  </tr>
  {/if}
  {foreach from=$patient->_ref_consultations item=curr_consult}
  <tr>
    <td class="text">
      Consultation le {$curr_consult->_ref_plageconsult->date|date_format:"%d/%m/%Y"}
      avec le Dr. {$curr_consult->_ref_plageconsult->_ref_chir->_view}
    </td>
  </tr>
  {/foreach}
</table>