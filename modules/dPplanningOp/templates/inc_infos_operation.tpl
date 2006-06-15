<!-- $Id: $ -->

<table class="tbl">
  <tr>
    <th class="title" colspan="3">
      Informations sur les opérations pendant le séjour
    </th>
  </tr>
  
  <tr>
    <th>Chirurgien</th>
    <th>Date</th>
    <th>Actes</th>
  </tr>

  {foreach from=$sejour->_ref_operations item=curr_operation}
  <tr>
    <td>{$curr_operation->_ref_chir->_view}</td>
    <td>{$curr_operation->_ref_plageop->date}</td>
    <td class="text">
      {foreach from=$curr_operation->_ext_codes_ccam item=curr_ext_code}
      <strong>{$curr_ext_code->code}</strong> :
      {$curr_ext_code->libelleLong}
       <br />
      {/foreach}
    </td>
  </tr>
  {/foreach}

</table> 

