<table class="tbl">
  <tr>
    <th class="title text">
      {{mb_include module=system template=inc_object_idsante400}}
      {{mb_include module=system template=inc_object_history}}
      {{mb_include module=system template=inc_object_notes}}
      {{$object}}
    </th>
  </tr>
  <tr>
    <td>
      {{foreach from=$object->_specs key=prop item=spec}}
      {{mb_include module=system template=inc_field_view}}
      {{/foreach}}
    </td>
  </tr>
</table>

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
      {{mb_include_script module="dPplanningOp" script="sejour" ajax="true"}}

      {{if $can->edit}}
			<button type="button" class="edit" onclick="Sejour.edit('{{$sejour->_id}}');">
				{{tr}}Modify{{/tr}}
			</button>
      {{/if}}

      {{if $modules.dPadmissions->_can->read}}
			<button type="button" class="search" onclick="Sejour.admission('{{$sejour->_date_entree_prevue}}');">
				{{tr}}Admission{{/tr}}
			</button>
			{{/if}}
    </td>
  </tr>
</table>

<table class="tbl tooltip">
  {{mb_include module=dPcabinet template=inc_list_actes_ccam subject=$sejour vue=view}}
</table>
