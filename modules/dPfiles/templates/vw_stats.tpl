<table class="tbl">
	<tr>
		<th>{{tr}}CFile-_count{{/tr}}</th>
    <th>{{tr}}CFile-_total_weight{{/tr}}</th>
    <th>{{tr}}CFile-_average_weight{{/tr}}</th>
		<th>{{tr}}CMediusers{{/tr}}</th>
		
	</tr>
	
  {{foreach from=$stats item=_stat}}
  <tr>
    <td style="text-align: right;">{{$_stat.files_count}}</td>
    <td style="text-align: right;">{{$_stat._files_weight}}</td>
    <td style="text-align: right;">{{$_stat._file_average_weight}}</td>
    <td>
    	{{assign var=user value=$_stat._ref_user}}
    	{{if $user}}
				<span class="mediuser" style="border-color: #{{$user->_ref_function->color}};" onmouseover="ObjectTooltip.createEx(this, '{{$user->_guid}}')">
					{{$user}}
				</span>
			{{else}}
	      {{$_stat.user_first_name}} {{$_stat.user_last_name}}
			{{/if}}			
		</td>
  </tr>
	
  {{foreachelse}}
	<tr>
		<td><em>{{tr}}CFile.none{{/tr}}</em></td>
	</tr>
	{{/foreach}}

</table>	

