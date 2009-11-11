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
    	{{if $can->edit}}
      <a href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$sejour->_id}}" class="button edit">{{tr}}Modify{{/tr}}</a>
      {{/if}}
      {{if $modules.dPadmissions->_can->edit}} 
      <a href="?m=dPadmissions&tab=vw_idx_admission&date={{$sejour->_date_entree_prevue}}#adm{{$sejour->_id}}" class="button tick">{{tr}}Admission{{/tr}}</a>
      {{/if}}
    </td>
  </tr>
</table>

<table class="tbl tooltip">
  {{mb_include module=dPcabinet template=inc_list_actes_ccam subject=$sejour vue=view}}
</table>
