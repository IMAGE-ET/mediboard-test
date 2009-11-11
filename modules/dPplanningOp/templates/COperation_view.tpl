{{include file=CMbObject_view.tpl}}

{{assign var=operation value=$object}}

<table class="tbl tooltip">
  {{if $operation->annulee == 1}}
  <tr>
    <th class="category cancelled" colspan="4">
    {{tr}}COperation-annulee{{/tr}}
    </th>
  </tr>
  {{/if}}
  
  <tr>
    <td class="button">
      {{if $can->edit}}
      <a href="?m=dPplanningOp&tab=vw_edit_planning&operation_id={{$operation->_id}}" class="button edit">{{tr}}Modify{{/tr}}</a>
      {{/if}}

      <script type="text/javascript">
    	function printIntervention(id) {
			  var url = new Url("dPplanningOp", "view_planning");
			  url.addParam("operation_id", id);
			  url.popup(700, 550, "Admission");
			}
      </script>
			<button type="button" class="print" onclick="printIntervention({{$operation->_id}});">
				{{tr}}Print{{/tr}}
			</button>
    </td>
  </tr>
</table>