<table class="main tbl">
  <tr>
    <th>{{tr}}CProductDelivery-code{{/tr}}</th>
    <th>{{tr}}CProductDelivery-date_dispensation{{/tr}}</th>
    <th>{{tr}}CProductDelivery-date_delivery{{/tr}}</th>
    <th>{{tr}}CProductDelivery-date_reception{{/tr}}</th>
  </tr>
  {{foreach from=$list item=curr_deliv key=code}}
  <tr>
    <td>{{mb_value object=$curr_deliv field=code}}</td>
    <td>{{mb_value object=$curr_deliv field=date_dispensation}}</td>
    <td>{{mb_value object=$curr_deliv field=date_delivery}}</td>
    <td>{{mb_value object=$curr_deliv field=date_reception}}</td>
  </tr>
  {{/foreach}}
</table>