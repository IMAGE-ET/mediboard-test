<table class="main tbl">
  <tr>
    <th>{{mb_title class=CProductReference field=product_id}}</th>
    <th>{{mb_title class=CProductOrderItemReception field=code}}</th>
    <th>{{mb_title class=CProductOrderItemReception field=lapsing_date}}</th>
    <th>Quantité totale</th>
    <th>Quantité utilisée</th>
    <th>Quantité restante</th>
  </tr>
  
  {{foreach from=$receptions item=_reception}}
    <tr>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_reception->_guid}}')">
          {{mb_value object=$_reception->_ref_order_item->_ref_reference field=product_id}}
        </span>
      </td>
      <td>{{mb_value object=$_reception field=code}}</td>
      <td {{if $_reception->lapsing_date|strtotime < $smarty.now}}class="error"{{/if}}>
        {{mb_value object=$_reception field=lapsing_date}}
      </td>
      <td>{{$_reception->_total_quantity}}</td>
      <td>{{$_reception->_used_quantity}}</td>
      <td>{{$_reception->_remaining_quantity}}</td>
    </tr>
  {{/foreach}}
</table>