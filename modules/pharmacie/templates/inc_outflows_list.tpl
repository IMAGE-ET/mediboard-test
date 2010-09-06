<tr>
  <td colspan="6">
    {{mb_include module=system template=inc_pagination total=$total_outflows change_page=changePage current=$start}}
  </td>
</tr>

{{foreach from=$list_outflows item=_delivery}}
  <tr>
    <td>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_delivery->_ref_stock->_guid}}')">
        {{mb_value object=$_delivery->_ref_stock field=product_id}}
      </span>
    </td>
    <td>{{mb_value object=$_delivery field=quantity}}</td>
    <td>{{mb_value object=$_delivery field=date_delivery}}</td>
    <td>
      {{if $_delivery->service_id}}
        {{mb_value object=$_delivery field=service_id}}
      {{/if}}
    </td>
    <td>{{mb_value object=$_delivery field=comments}}</td>
    <td>
      <button type="button" class="cancel notext" onclick="removeOutflow('{{$_delivery->_id}}', '{{$_delivery->_ref_stock}}')">{{tr}}Supprimer{{/tr}}</button>
    </td>
  </tr>
{{foreachelse}}
  <tr>
    <td colspan="6">{{tr}}CProductDeliveryTrace.none{{/tr}}</td>
  </tr>
{{/foreach}}