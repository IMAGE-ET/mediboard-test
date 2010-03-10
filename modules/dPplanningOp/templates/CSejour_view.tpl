{{include file=CMbObject_view.tpl}}

{{assign var=sejour value=$object}}

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
      {{mb_include_script module="dPplanningOp" script="sejour" ajax="true"}}

      {{if $can->edit}}
			<button type="button" class="edit" onclick="Sejour.edit('{{$sejour->_id}}');">
				{{tr}}Modify{{/tr}}
			</button>
      {{/if}}

      {{if @$modules.dPadmissions->_can->read}}
			<button type="button" class="search" onclick="Sejour.admission('{{$sejour->_date_entree_prevue}}');">
				{{tr}}Admission{{/tr}}
			</button>
			{{/if}}

      {{if $sejour->type == "ssr" && @$modules.ssr->_can->read}}
			<br />
      <button type="button" class="search" onclick="Sejour.showSSR('{{$sejour->_id}}');">
        {{tr}}module-ssr-long{{/tr}}
      </button>
      {{/if}}

      {{if $sejour->type == "urg" && @$modules.dPurgences->_can->read}}
      <br />
      <button type="button" class="search" onclick="Sejour.showUrgences('{{$sejour->_id}}');">
        {{tr}}module-dPurgences-long{{/tr}}
      </button>
      {{/if}}
    </td>
  </tr>
</table>

<table class="tbl tooltip">
  {{mb_include module=dPcabinet template=inc_list_actes_ccam subject=$sejour vue=view}}
</table>
