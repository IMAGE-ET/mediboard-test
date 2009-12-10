{{include file=CMbObject_view.tpl}}

{{assign var="sejour" value=$object}}
<table class="tbl tooltip">
  {{if $sejour->annule == 1}}
  <tr>
    <th class="category cancelled" colspan="4">
    {{tr}}CSejour-annule{{/tr}}
    </th>
  </tr>
  {{/if}}
  
  <tr>
    <td class="button">
      <script type="text/javascript">
      modifySejour = function(id) {
        var url = new Url("dPplanningOp", "vw_edit_sejour", "tab");
			  url.addParam("sejour_id", id);
			  url.redirectOpener();
      }
    	viewAdmissionSejour = function(date) {
			  var url = new Url("dPadmissions", "vw_idx_admission", "tab");
			  url.addParam("date", date);
			  url.redirectOpener();
			}
      </script>
      {{if $can->edit}}
			<button type="button" class="edit" onclick="modifySejour({{$sejour->_id}});">
				{{tr}}Modify{{/tr}}
			</button>
      {{/if}}
      {{if $modules.dPadmissions->_can->read}}
			<button type="button" class="search" onclick="viewAdmissionSejour('{{$sejour->_date_entree_prevue}}');">
				{{tr}}Admission{{/tr}}
			</button>
			{{/if}}
    </td>
  </tr>
</table>

<table class="tbl tooltip">
  {{mb_include module=dPcabinet template=inc_list_actes_ccam subject=$sejour vue=view}}
</table>
