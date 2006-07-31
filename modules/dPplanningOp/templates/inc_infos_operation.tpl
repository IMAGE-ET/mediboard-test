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

  {{foreach from=$sejour->_ref_operations item=curr_operation}}
  <tr>
    <td>
      <a href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$curr_operation->operation_id}}">{{$curr_operation->_ref_chir->_view}}</a>
    </td>
    <td>{{$curr_operation->_datetime|date_format:"%a %d %b %Y"}}</td>
    {{if $curr_operation->annulee}}
    <td style="background: #f00">
      <strong>[OPERATION ANNULEE]</strong>
	</td>
    {{else}}
    <td class="text">
      {{foreach from=$curr_operation->_ext_codes_ccam item=curr_ext_code}}
      <strong>{{$curr_ext_code->code}}</strong> :
      {{$curr_ext_code->libelleLong}}
       <br />
      {{/foreach}}
    </td>
    {{/if}}
  </tr>
  {{/foreach}}

</table> 

