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
      <script type="text/javascript">
      modifyIntervention = function(id) {
        var url = new Url("dPplanningOp", "vw_edit_planning", "tab");
			  url.addParam("operation_id", id);
			  url.redirectOpener();
      }
    	printIntervention = function(id) {
			  var url = new Url("dPplanningOp", "view_planning", "tab");
			  url.addParam("operation_id", id);
			  url.popup(700, 550, "Admission");
			}
      </script>
      {{if $can->edit}}
			<button type="button" class="edit" onclick="modifyIntervention({{$operation->_id}});">
				{{tr}}Modify{{/tr}}
			</button>
      {{/if}}
			<button type="button" class="print" onclick="printIntervention({{$operation->_id}});">
				{{tr}}Print{{/tr}}
			</button>
    </td>
  </tr>
</table>

<table class="tbl tooltip">
  {{mb_include module=dPcabinet template=inc_list_actes_ccam subject=$operation vue=view}}
</table>
