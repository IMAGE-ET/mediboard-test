<table class="tbl">
  <tr>
    <th>{{tr}}CProductDelivery-product_id{{/tr}}</th>
    <th>{{tr}}CProductDelivery-date{{/tr}}</th>
    <th>{{tr}}CProductDelivery-target_class{{/tr}}</th>
    <th>{{tr}}CProductDelivery-target_id{{/tr}}</th>
    <th>{{tr}}CProductDelivery-code{{/tr}}</th>
    <th>{{tr}}CProductDelivery-description{{/tr}}</th>
  </tr>
  {{foreach from=$deliveries_list item=curr_delivery}}
  <tr>
    <td><a href="?m=dPstock&amp;tab=vw_idx_delivery&amp;delivery_id={{$curr_delivery->_id}}">{{$curr_delivery->_ref_product->_view}}</a></td>
    <td>{{mb_value object=$curr_delivery field=date}}</td>
    <td>{{tr}}{{$curr_delivery->_ref_target->_class_name}}{{/tr}}</td>
    <td>{{mb_value object=$curr_delivery->_ref_target field=_view}}</td>
    <td>{{mb_value object=$curr_delivery field=code}}</td>
    <td>{{mb_value object=$curr_delivery field=description}}</td>
  </tr>
  {{/foreach}}
</table>
