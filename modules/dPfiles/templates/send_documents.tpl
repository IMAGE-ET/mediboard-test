{{* $id: $ *}}

<!-- Category summary -->
<table class="tbl">

<tr>
  <th rowspan="2">{{mb_title class=CFilesCategory field=nom}}</th>
  <th rowspan="2">{{mb_title class=CFilesCategory field=class}}</th>
  <th colspan="2">{{tr}}CFilesCategory-back-categorized_files{{/tr}}</th>
  <th colspan="2">{{tr}}CFilesCategory-back-categorized_documents{{/tr}}</th>
</tr>

<tr>
  <th>{{tr}}Unsent{{/tr}}</th>
  <th>{{tr}}Total {{/tr}}</th>
  <th>{{tr}}Unsent{{/tr}}</th>
  <th>{{tr}}Total {{/tr}}</th>
</tr>

{{foreach from=$categories item=_category}}
<tr>
	<td>{{mb_value object=$_category field=nom}}</td>
	<td>{{tr}}{{$_category->class}}{{/tr}}</td>
	<td style="text-align: center">{{$_category->_count_unsent_files}}</td>
	<td style="text-align: center">{{$_category->_count_files}}</td>
	<td style="text-align: center">{{$_category->_count_unsent_documents}}</td>
	<td style="text-align: center">{{$_category->_count_documents}}</td>
</tr>
{{/foreach}}

</table>

<!-- DocItems detail -->
<table class="tbl">
{{foreach from=$items item=_items key=class}} 
	<tr>
		<th class="category" colspan="10">
			{{tr}}{{$class}}{{/tr}}
		</th>
	</tr>
  
  <tr>
    <th>{{mb_title class=$class field=file_category_id}}</th>
    <th>{{mb_title class=$class field=object_id}}</th>
    <th>{{mb_title class=$class field=_extensioned}}</th>
    <th>{{mb_title class=$class field=etat_envoi}}</th>
    <th>{{mb_title class=$class field=_send_problem}}</th>
  </tr>
  
  {{foreach from=$_items item=_item}}
  <tr>
    <td>
      {{assign var=category_id value=$_item->file_category_id}}
    	{{$categories.$category_id}}
    </td>
    <td onmouseover="ObjectTooltip.createEx(this,'{{$_item->_ref_object->_guid}}')">
    	{{$_item->_ref_object}}
    </td>
    <td onmouseover="ObjectTooltip.createEx(this,'{{$_item->_guid}}')">
    	{{mb_value object=$_item field=_extensioned}}
    </td>
    <td>{{mb_value object=$_item field=etat_envoi}}</td>
    <td>
      {{if $_item->_send_problem}}
      <div class="{{mb_ternary test=$_item->_send value=error other=warning}}">
    	{{mb_value object=$_item field=_send_problem}}
      </div>
			{{else}}
				{{if $do}}
	      <div class="message">
		      {{tr}}Sent{{/tr}} !
	      </div>
				{{/if}}
			{{/if}}
    	
    </td>
  </tr>
	{{/foreach}}
{{/foreach}}
</table>

