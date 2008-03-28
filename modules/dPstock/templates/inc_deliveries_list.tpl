<table class="tbl">
    <tr>
      <th>Produit</th>
      <th>Date</th>
      <th>Type de cible</th>
      <th>Cible</th>
      <th>Description</th>
    </tr>
    {{foreach from=$deliveries_list item=curr_delivery}}
    <tr>
      <td>{{$curr_delivery->_ref_product->_view}}</td>
      <td>{{mb_value object=$curr_delivery field=date}}</td>
      <td>{{tr}}{{$curr_delivery->_ref_target->_class_name}}{{/tr}}</td>
      <td>{{mb_value object=$curr_delivery->_ref_target field=_view}}</td>
      <td>{{mb_value object=$curr_delivery field=description}}</td>
    </tr>
    {{/foreach}}
  </table>
</form>
