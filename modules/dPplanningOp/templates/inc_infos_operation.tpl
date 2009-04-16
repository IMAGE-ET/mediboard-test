<!-- $Id: $ -->

{{if $sejour->_canRead}}
<table class="tbl">
  <tr>
    <th class="title" colspan="3">
      {{tr}}CSejour-msg-infoOper{{/tr}}
    </th>
  </tr>
  
  <tr>
    <th>{{tr}}COperation-chir_id{{/tr}}</th>
    <th>{{tr}}Date{{/tr}}</th>
    <th>{{tr}}COperation-_ext_codes_ccam{{/tr}}</th>
  </tr>

  {{foreach from=$sejour->_ref_operations item=_operation name=operation}}
  <tr>
    <td>
      <a href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$_operation->_id}}">
        {{$_operation->_ref_chir->_view}}
      </a>
    </td>
    <td>{{$_operation->_datetime|date_format:"%a %d %b %Y"}}</td>
    {{if $_operation->annulee}}
    <th class="category cancelled">
      <strong>{{tr}}COperation-annulee{{/tr}}</strong>
		</th>
    {{else}}
    {{if $can->edit}}
    <td class="text">
      {{if $_operation->libelle}}
      <em>[{{$_operation->libelle}}]</em>
      <br />
      {{/if}}
      {{foreach from=$_operation->_ext_codes_ccam item=curr_ext_code}}
      <strong>{{$curr_ext_code->code}}</strong> :
      {{$curr_ext_code->libelleLong}}
       <br />
      {{/foreach}}
    </td>
    {{/if}}
    {{/if}}
  </tr>
  {{/foreach}}

</table>
 
{{else}}
<div class="small-info">Vous n'avez pas accès au détail des interventions.</div>
{{/if}}


