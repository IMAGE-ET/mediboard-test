<option value="0"> ----- Toutes ---- </option>
{{foreach from=$list_categories item=curr_category}}
  <option value="{{$curr_category->_id}}">{{$curr_category->name}}</option>
{{/foreach}}