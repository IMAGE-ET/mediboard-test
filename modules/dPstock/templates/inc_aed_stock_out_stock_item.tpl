<tr>
  <td>{{$stock->_view}}</td>
  <td>{{$stock->quantity}}</td>
  <td>{{include file="inc_bargraph.tpl"}}</td>
  <td>
    <span id="stock-back-{{$stock->_id}}">+</span>
    <input type="text" name="_quantity[{{$stock->_id}}]" value="5" size="2" />
    <button type="button" class="tick notext" onclick="stockOut(this.form, {{$stock->_id}})">OK</button>
    <input type="checkbox" name="_stock_back[{{$stock->_id}}]" onclick="$('stock-back-{{$stock->_id}}').innerHTML = this.checked?'-':'+';" /> Retour
  </td>
  <td><input type="text" name="_product_code[{{$stock->_id}}]" value="" /></td>
</tr>
