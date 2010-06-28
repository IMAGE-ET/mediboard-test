<form name="Filter" action="?" method="get" onsubmit="return checkForm(this);">
	
<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />

<table class="form">
  <tr>
    <th class="title" colspan="4">
    	Filtre de statistiques
    </th>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field=_function_id}}</th>
    <td>
      <select name="_function_id" class="{{$filter->_props._function_id}} notNull">
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{foreach from=$functions item=_function}}
        <option value="{{$_function->_id}}" class="mediuser" style="border-color: #{{$_function->color}}" {{if $_function->_id == $filter->_function_id}} selected="selected" {{/if}}>
          {{$_function}}
        </option>
        {{/foreach}}
      </select>
    </td>

    <th>{{mb_label object=$filter field=_date_min}}</th>
    <td>{{mb_field object=$filter field=_date_min form=Filter register=true canNull=false}}</td>
	</tr>
	
	<tr>
    <th>
      {{mb_label object=$filter field=_other_function_id}}
    </th>
    <td>
      <select name="_other_function_id" class="{{$filter->_props._other_function_id}}">
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{foreach from=$functions item=_function}}
        <option value="{{$_function->_id}}" class="mediuser" style="border-color: #{{$_function->color}}" {{if $_function->_id == $filter->_other_function_id}} selected="selected" {{/if}}>
          {{$_function}}
        </option>
        {{/foreach}}
      </select>
    </td>

    <th>{{mb_label object=$filter field=_date_max}}</th>
    <td>{{mb_field object=$filter field=_date_max form=Filter register=true canNull=false}}</td>
  </tr>
	
	<tr>
		<td class="button" colspan="4">
			<button type="submit" class="change">
				{{tr}}Compute{{/tr}}
			</button>
    </td>
	</tr>

</table> 

</form>