<tr>
  <td>{{$stock->_view}}</td>
  <td>{{$stock->quantity}}</td>
  <td>{{include file="inc_bargraph.tpl"}}</td>
  <td>
    <input type="text" name="_quantity[{{$stock->_id}}]" value="5" size="2" />
    <button type="button" class="tick notext" onclick="stockOut(this.form, {{$stock->_id}})">OK</button>
  </td>
  <td><input type="text" name="_product_code[{{$stock->_id}}]" value="" /></td>
  <td>
  <select name="_function_id[{{$stock->_id}}]">
    {{foreach from=$list_functions item=curr_function}}
    <option value="{{$curr_function->_id}}">{{$curr_function->_view}}</option>
    {{/foreach}}
  </select>
  </td>
</tr>