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
    {{assign var=weight value=$total|ternary:bold:normal}}

    <th style="width: 20%; font-weight: {{$weight}};">
      <span title="{{$_type}}">
        {{$_type->_shortview}}
      </span>
    </th>
    <td style="width: 10%; font-weight: {{$weight}};">
      {{$total|ternary:$total:'-'}}
    </td>
  {{if $smarty.foreach.liste_types.index % 3 == 3}}
  </tr>
  {{/if}}
  {{/foreach}}
</table>