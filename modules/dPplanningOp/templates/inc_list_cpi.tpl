{{foreach from=$cpi_list item=_cpi}}
  <option value="{{$_cpi->_id}}" data-type="{{$_cpi->type}}" data-type_pec="{{$_cpi->type_pec}}">
    {{$_cpi}}
  </option>
{{/foreach}}
