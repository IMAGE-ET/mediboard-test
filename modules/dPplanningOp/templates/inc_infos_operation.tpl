<!-- $Id: $ -->

<table class="tbl">
  <tr>
    <th class="title" colspan="3">
      {{tr}}msg-CSejour-infoOper{{/tr}}
    </th>
  </tr>
  
  <tr>
    <th>{{tr}}COperation-chir_id{{/tr}}</th>
    <th>{{tr}}Date{{/tr}}</th>
    <th>{{tr}}COperation-_ext_codes_ccam{{/tr}}</th>
  </tr>

  {{foreach from=$sejour->_ref_operations item=curr_operation}}
  <tr>
    <td>
      <a href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$curr_operation->operation_id}}">{{$curr_operation->_ref_chir->_view}}</a>
    </td>
    <td>{{$curr_operation->_datetime|date_format:"%a %d %b %Y"}}</td>
    {{if $curr_operation->annulee}}
    <th class="category cancelled">
      <strong>{{tr}}COperation-annulee{{/tr}}</strong>
	</th>
    {{else}}
    <td class="text">
      {{if $curr_operation->libelle}}
      <em>[{{$curr_operation->libelle}}]</em>
      <br />
      {{/if}}
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

