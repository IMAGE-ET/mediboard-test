<script>
  window.print();
</script>

<!-- Received orders -->
<table class="tbl">
  <tr>
    <th class="title" colspan="6">
      Commandes {{if $not_invoiced xor $invoiced}}{{$invoiced|ternary:"facturées":"non facturées"}}{{/if}} 
      entre le {{$date_min|date_format:$conf.date}} et le {{$date_max|date_format:$conf.date}}
    </th>
  </tr>
  <tr>
    <th class="narrow">{{mb_title class=CProductOrder field=order_number}}</th>
    <th>{{tr}}CProductOrder-societe_id{{/tr}}</th>
    {{*
    <th>{{tr}}CProductOrder-object_id{{/tr}}</th>
    *}}
    <th>{{tr}}CProductOrder-date_ordered{{/tr}}</th>
    <th>{{tr}}CProductOrder-_total{{/tr}}</th>
    <th class="narrow">{{tr}}CProductOrder-bill_number{{/tr}}</th>
  </tr>
  <tbody>
  {{foreach from=$orders item=curr_order}}
    <tr {{if $curr_order->bill_number}}class="bill"{{/if}}>
      <td>{{$curr_order->order_number}}</td>
      <td>{{$curr_order->_ref_societe->_view}}</td>
      {{*
      <td class="text">
        {{if $curr_order->_ref_object}}
          {{$curr_order->_ref_object->_view}}
        {{/if}}
      </td>
      *}}
      <td>{{mb_value object=$curr_order field=date_ordered}}</td>
      <td class="currency" style="text-align: right;">{{mb_value object=$curr_order field=_total}}</td>
      <td>{{mb_value object=$curr_order field=bill_number}}</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="7" class="empty">{{tr}}CProductOrder.none{{/tr}}</td>
    </tr>
  {{/foreach}}
  </tbody>
</table>
