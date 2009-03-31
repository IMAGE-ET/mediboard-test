<table class="main">
	<tr>
		<td>
			<form name="filter-check-lists" action="?" method="get">
				<input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
				<table class="form">
					<tr>
						<th>{{mb_label object=$check_list_filter field=_date_min}}</th>
						<td class="date">{{mb_field object=$check_list_filter field=_date_min register=true form="filter-check-lists"}}</td>
						<th>{{mb_label object=$check_list_filter field=room_id}}</th>
            <td>{{mb_field object=$check_list_filter field=room_id form="filter-check-lists"}}</td>
					</tr>
					<tr>
            <th>{{mb_label object=$check_list_filter field=_date_max}}</th>
            <td class="date">{{mb_field object=$check_list_filter field=_date_max register=true form="filter-check-lists"}}</td>
						<td colspan="2">
              <button type="submit" class="submit">{{tr}}Filter{{/tr}}</button>
            </td>
          </tr>
				</table>
			</form>
			<table class="main tbl">
				<tr>
          <th>{{mb_title class=CDailyCheckList field=date}}</th>
          <th>{{mb_title class=CDailyCheckList field=room_id}}</th>
          <th>{{mb_title class=CDailyCheckList field=validator_id}}</th>
				</tr>
				{{foreach from=$list_check_lists item=curr_list}}
				<tr>
          <td><a href="?m={{$m}}&amp;tab={{$tab}}&amp;check_list_id={{$curr_list->_id}}">{{mb_value object=$curr_list field=date}}</a></td>
          <td>{{mb_value object=$curr_list field=room_id}}</td>
          <td>{{mb_value object=$curr_list field=validator_id}}</td>
        </tr>
				{{foreachelse}}
        <tr>
          <td colspan="10">{{tr}}CDailyCheckList.none{{/tr}}</td>
        </tr>
				{{/foreach}}
			</table>
		</td>
		{{if $check_list->_id}}
		<td>
			<table class="main form">
				<tr>
					<th class="title" colspan="2">{{$check_list->_view}}</th>
				</tr>
        <tr>
          <th>{{mb_label object=$check_list field=date}}</th>
          <td>{{mb_value object=$check_list field=date}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$check_list field=room_id}}</th>
          <td>{{mb_value object=$check_list field=room_id}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$check_list field=validator_id}}</th>
          <td>{{mb_value object=$check_list field=validator_id}}</td>
        </tr>
				<tr>
          <th colspan="2" class="category">{{tr}}CDailyCheckList-back-items{{/tr}}</th>
        </tr>
				{{foreach from=$check_list->_back.items item=curr_item}}
				<tr>
          <td title="{{$curr_item->_ref_item_type->desc}}" style="font-weight: bold;">{{mb_value object=$curr_item->_ref_item_type field=title}}</td>
          <td>{{mb_value object=$curr_item field=checked}}</td>
        </tr>
				{{foreachelse}}
				<tr>
          <td colspan="2">{{tr}}CDailyCheckItem.none{{/tr}}</td>
        </tr>
				{{/foreach}}
			</table>
		</td>
		{{/if}}
	</tr>
</table>
