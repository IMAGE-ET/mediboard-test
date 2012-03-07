{{include file=CMbObject_view.tpl}}

{{assign var=operation value=$object}}

<table class="tbl tooltip">
  {{if $operation->debut_op}}
    <tr colspan="4">
      <td>
        <strong>{{tr}}COperation-debut_op{{/tr}} :</strong>
        {{$operation->debut_op|date_format:$conf.time}}
      </td>
      <td>
      </td>
    </tr>
  {{/if}}
  {{if $operation->annulee == 1}}
  <tr>
    <th class="category cancelled" colspan="4">
    {{tr}}COperation-annulee{{/tr}}
    </th>
  </tr>
  {{/if}}
  
  <tr>
    <td class="button" colspan="4">
      {{mb_script module="dPplanningOp" script="operation" ajax="true"}}
    	
      {{if $can->edit}}
			<button type="button" class="edit" onclick="Operation.edit('{{$operation->_id}}', '{{$operation->plageop_id}}');">
				{{tr}}Modify{{/tr}}
			</button>
      {{/if}}

			<button type="button" class="print" onclick="Operation.print('{{$operation->_id}}');">
				{{tr}}Print{{/tr}}
			</button>
    </td>
  </tr>
</table>

<table class="tbl tooltip">
  {{mb_include module=cabinet template=inc_list_actes_ccam subject=$operation vue=view}}
</table>
