<table class="tbl">
  <tr>
    <th colspan="5" class="title">20 {{tr}}last{{/tr}} {{tr}}CProductDelivery.more{{/tr}}</th>
  </tr>
  <tr>
    <th>{{tr}}CProduct{{/tr}}</th>
    <th>{{tr}}CProductDelivery-date{{/tr}}</th>
    <th>{{tr}}CProductDelivery-quantity{{/tr}}</th>
    <th>{{tr}}CProductDelivery-code{{/tr}}</th>
    <th>{{tr}}CProductDelivery-service_id{{/tr}}</th>
  </tr>
  {{foreach from=$list_latest_deliveries item=curr_delivery}}
  <tr>
    <td>{{$curr_delivery->_ref_stock->_view}}</td>
    <td class="date">{{mb_value object=$curr_delivery field=date}}</td>
    <td>{{mb_value object=$curr_delivery field=quantity}}</td>
    <td>{{mb_value object=$curr_delivery field=code}}</td>
    <td>{{mb_value object=$curr_delivery->_ref_service field=_view}}</td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CProductDelivery.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>
