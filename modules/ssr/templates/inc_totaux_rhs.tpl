<table class="form">
  <tr>
    <th class="title" colspan="6">
      {{mb_include module=system template=inc_object_notes      object=$rhs}}
      {{mb_include module=system template=inc_object_idsante400 object=$rhs}}
      {{mb_include module=system template=inc_object_history    object=$rhs}}
      Totaux RHS
    </th>
  </tr>
  {{foreach from=$types_activite item=_type name=liste_types}}
  {{assign var=code value=$_type->code}}
  {{assign var=rhs_id value=$rhs->_id}}
  {{assign var=total value=$totaux.$rhs_id.$code}}
  {{if $smarty.foreach.liste_types.index % 3 == 0}}
  <tr>
  {{/if}}
    <th>{{$_type->_shortview}}</th>
    <td class="button">
      {{if $total}}{{$total}}{{else}}-{{/if}}
    </td>
  {{if $smarty.foreach.liste_types.index % 3 == 3}}
  </tr>
  {{/if}}
  {{/foreach}}
</table>