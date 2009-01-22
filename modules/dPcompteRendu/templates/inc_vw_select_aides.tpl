<select name="_helpers_{{$field}}" style="width: 80px;" size="1" onchange="pasteHelperContent(this)">
  <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
  {{foreach from=$object->_aides item=_list key=sTitleOpt}}
		 {{foreach from=$_list item=list_aides_by_type key=_type_aide}}
		   {{if $_type_aide != "no_enum"}}
			    <optgroup label="{{$_type_aide}}">
			    {{foreach from=$list_aides_by_type item=_list_aides key=cat}}
				    <optgroup label="{{$cat}}" style="padding-left: 10px;">
				      {{html_options options=$_list_aides}}
				    </optgroup>
			   {{/foreach}}
			   </optgroup>
		   {{/if}}
     {{/foreach}}
  {{/foreach}}
</select>