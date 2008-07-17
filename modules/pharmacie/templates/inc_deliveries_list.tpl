<table class="tbl">
  <tr>
    <th>{{tr}}CProduct{{/tr}}</th>
    <th>{{tr}}CProductDelivery-date{{/tr}}</th>
    <th>{{tr}}CProductDelivery-quantity{{/tr}}</th>
    <th>{{tr}}CProductDelivery-code{{/tr}}</th>
    <th>{{tr}}CProductDelivery-service_id{{/tr}}</th>
    <th></th>
  </tr>
  {{foreach from=$list_deliveries item=curr_delivery}}
  <tr>
    <td>{{$curr_delivery->_ref_stock->_view}}</td>
    <td class="date">{{mb_value object=$curr_delivery field=date}}</td>
    <td>{{mb_value object=$curr_delivery field=quantity}}</td>
    <td>{{mb_value object=$curr_delivery field=code}}</td>
    <td>{{mb_value object=$curr_delivery->_ref_service field=_view}}</td>
    <td>
    <form name="delivery-{{$curr_delivery->_id}}" action="?" method="post">
      <input type="hidden" name="m" value="dPstock" /> 
      <input type="hidden" name="dosql" value="do_delivery_aed" />
      <input type="hidden" name="delivery_id" value="{{$curr_delivery->_id}}" />
      <input type="hidden" name="_do_deliver" value="1" />
      <input type="hidden" name="status" value="done" />
      
      <button type="button" class="tick" onclick="submitFormAjax(this.form, 'systemMsg', {onComplete: refreshDeliveriesList})">Effectuer</button>
    </form>
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CProductDelivery.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>
