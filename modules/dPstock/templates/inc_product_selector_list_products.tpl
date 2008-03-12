{{foreach from=$list_products item=curr_product}}
<option value="{{$curr_product->_id}}">{{$curr_product->_view}}</option>
{{/foreach}}
