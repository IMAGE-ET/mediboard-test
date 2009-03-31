<table class="main">
  <tr>
  	<td>
  		<table class="main tbl">
  			<tr>
  				<th>{{mb_title class=CDailyCheckItemType field=title}}</th>
          <th>{{mb_title class=CDailyCheckItemType field=desc}}</th>
          <th>{{mb_title class=CDailyCheckItemType field=active}}</th>
  			</tr>
				{{foreach from=$item_types_list item=curr_item}}
        <tr>
          <td><a href="?m={{$m}}&amp;tab=vw_daily_check_item_type&amp;item_type_id={{$curr_item->_id}}">{{mb_value object=$curr_item field=title}}</a></td>
          <td>{{mb_value object=$curr_item field=desc}}</td>
          <td>{{mb_value object=$curr_item field=active}}</td>
        </tr>
				{{foreachelse}}
				<tr>
          <td colspan="10">{{tr}}CDailyCheckItemType.none{{/tr}}</td>
        </tr>
				{{/foreach}}
  		</table>
  	</td>
		<td>
      <button type="button" class="new" onclick="location.href='?m={{$m}}&amp;tab=vw_daily_check_item_type&amp;check_item_type_id=0'">
      	{{tr}}CDailyCheckItemType-title-create{{/tr}}
			</button>
      <form name="edit-CDailyCheckItemType" action="?m={{$m}}&amp;tab=vw_daily_check_item_type" method="post" onsubmit="return checkForm(this)">
	      <input type="hidden" name="dosql" value="do_daily_check_item_type_aed" />
        <input type="hidden" name="m" value="{{$m}}" />
	      <input type="hidden" name="daily_check_item_type_id" value="{{$item_type->_id}}" />
        <input type="hidden" name="group_id" value="{{$g}}" />
	      <input type="hidden" name="del" value="0" />
				<table class="main form">
	        <tr>
	          {{if $item_type->_id}}
	          <th class="title modify" colspan="2">{{$item_type->title}}</th>
	          {{else}}
	          <th class="title" colspan="2">{{tr}}CDailyCheckItemType-title-create{{/tr}}</th>
	          {{/if}}
	        </tr>
	        <tr>
	          <th style="width: 1%;">{{mb_label object=$item_type field="title"}}</th>
	          <td>{{mb_field object=$item_type field="title"}}</td>
	        </tr>
	        <tr>
	          <th>{{mb_label object=$item_type field="desc"}}</th>
	          <td>{{mb_field object=$item_type field="desc"}}</td>
	        </tr>
	        <tr>
	          <th>{{mb_label object=$item_type field="active"}}</th>
	          <td>{{mb_field object=$item_type field="active"}}</td>
	        </tr>
					<tr>
	          <td class="button" colspan="2">
	            {{if $item_type->_id}}
	            <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
	            <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$item_type->_view|smarty:nodefaults|JSAttribute}}'})">
	              {{tr}}Delete{{/tr}}
	            </button>
	            {{else}}
	            <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
	            {{/if}}
	          </td>
	        </tr> 
	      </table>
			</form>
		</td>
  </tr>
</table>