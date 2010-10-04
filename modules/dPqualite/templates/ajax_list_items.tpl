<option value=""> &mdash; Item</option>
{{foreach from=$items item=_item}}
  <option value="{{$_item->_id}}">
    {{$_item}}
  </option>
{{/foreach}}