<table class="main">
  <tr>
  	<td>
  		<table class="main tbl">
  			<tr>
  				<th>{{mb_title class=CDailyCheckItemCategory field=title}}</th>
          <th>{{mb_title class=CDailyCheckItemCategory field=target_class}}</th>
          <th>{{mb_title class=CDailyCheckItemCategory field=type}}</th>
          <th>{{mb_title class=CDailyCheckItemCategory field=desc}}</th>
  			</tr>
				{{foreach from=$item_categories_list item=curr_item}}
        <tr>
          <td><a href="?m={{$m}}&amp;a=vw_daily_check_item_category&amp;item_category_id={{$curr_item->_id}}&amp;dialog={{$dialog}}">{{mb_value object=$curr_item field=title}}</a></td>
          <td>{{mb_value object=$curr_item field=target_class}}</td>
          <td>{{mb_value object=$curr_item field=type}}</td>
          <td>{{mb_value object=$curr_item field=desc}}</td>
        </tr>
				{{foreachelse}}
				<tr>
          <td colspan="10">{{tr}}CDailyCheckItemCategory.none{{/tr}}</td>
        </tr>
				{{/foreach}}
  		</table>
  	</td>
		<td>
      <button type="button" class="new" onclick="location.href='?m={{$m}}&amp;a=vw_daily_check_item_category&amp;item_category_id=0&amp;dialog={{$dialog}}'">
      	{{tr}}CDailyCheckItemCategory-title-create{{/tr}}
			</button>
      <form name="edit-CDailyCheckItemCategory" action="?m={{$m}}&amp;a=vw_daily_check_item_category&amp;dialog={{$dialog}}" method="post" onsubmit="return checkForm(this)">
	      <input type="hidden" name="dosql" value="do_daily_check_item_category_aed" />
        <input type="hidden" name="m" value="{{$m}}" />
	      <input type="hidden" name="daily_check_item_category_id" value="{{$item_category->_id}}" />
	      <input type="hidden" name="del" value="0" />
				<table class="main form">
	        <tr>
	          {{if $item_category->_id}}
	          <th class="title modify" colspan="2">{{$item_category->title}}</th>
	          {{else}}
	          <th class="title" colspan="2">{{tr}}CDailyCheckItemCategory-title-create{{/tr}}</th>
	          {{/if}}
	        </tr>
	        <tr>
	          <th style="width: 1%;">{{mb_label object=$item_category field="title"}}</th>
	          <td>{{mb_field object=$item_category field="title"}}</td>
	        </tr>
          <tr>
            <th>{{mb_label object=$item_category field="target_class"}}</th>
            <td>{{mb_field object=$item_category field="target_class"}}</td>
          </tr>
	        <tr>
	          <th>{{mb_label object=$item_category field="desc"}}</th>
	          <td>{{mb_field object=$item_category field="desc"}}</td>
	        </tr>
					<tr>
	          <td class="button" colspan="2">
	            {{if $item_category->_id}}
	            <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
	            <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$item_category->_view|smarty:nodefaults|JSAttribute}}'})">
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