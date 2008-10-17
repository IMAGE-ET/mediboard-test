<table class="main tbl">
  <tr>
    <th>{{tr}}CProductDelivery-date_dispensation{{/tr}}</th>
  </tr>
  {{foreach from=$list item=curr_deliv}}
  <tr>
    <td>{{mb_value object=$curr_deliv field=date_dispensation}}</td>
  </tr>
  {{/foreach}}
</table>