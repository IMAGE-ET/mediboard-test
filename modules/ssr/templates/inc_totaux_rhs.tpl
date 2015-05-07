<table class="form">
  <tr>
    <th class="title" colspan="6">
      {{mb_include module=system template=inc_object_notes      object=$rhs}}
      {{mb_include module=system template=inc_object_idsante400 object=$rhs}}
      {{mb_include module=system template=inc_object_history    object=$rhs}}
      Totaux RHS ({{$rhs->_guid}})
    </th>
  </tr>

  {{if $rhs->_count_cdarr != 0}}
    {{foreach from=$rhs->_ref_types_activite item=_type name=liste_types}}
      {{assign var=code value=$_type->code}}
      {{assign var=total value=$rhs->_totaux.$code}}
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
  {{/if}}

  <tr>
    <td colspan="6" class="button">
      <form name="Edit-{{$rhs->_guid}}" action="?m={{$m}}" method="post" onsubmit="return CotationRHS.onSubmitRHS(this);">

      <input type="hidden" name="m" value="ssr" />
      <input type="hidden" name="dosql" value="do_rhs_aed" />
      <input type="hidden" name="del" value="0" />
      {{mb_key object=$rhs}}
      {{mb_field object=$rhs field=facture hidden=1}}
      {{mb_field object=$rhs field=sejour_id hidden=1}}
      
      {{if $rhs->facture}}
        <button class="cancel" type="button" onclick="$V(this.form.facture, '0'); this.form.onsubmit();">
          {{tr}}Restore{{/tr}}
        </button> 
      {{else}}
        <button class="change" type="button" onclick="CotationRHS.refreshRHS('{{$rhs->_id}}', '1')">
          {{tr}}CLigneActivitesRHS.recalculate{{/tr}}
        </button>
        <button class="tick"   type="button" onclick="$V(this.form.facture, '1'); this.form.onsubmit();">
          {{tr}}Charge{{/tr}} 
        </button> 
      {{/if}}
      
      </form>
    </td>
  </tr>
</table>