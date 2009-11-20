{{mb_include_script module="system" script="object_selector"}}

<table class="main">
	<tr>
		<td>
			<form name="filter-check-lists" action="?" method="get">
				<input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
				<table class="form">
					<tr>
						<th>{{mb_label object=$check_list_filter field=_date_min}}</th>
						<td>{{mb_field object=$check_list_filter field=_date_min register=true form="filter-check-lists"}}</td>
            <th>{{mb_label object=$check_list_filter field=_date_max}}</th>
            <td>{{mb_field object=$check_list_filter field=_date_max register=true form="filter-check-lists"}}</td>

            <th>{{mb_label object=$check_list_filter field=object_id}}</th>
            <td>
              <select name="object_guid">
                {{foreach from=$list_rooms item=list key=class}}
                  <optgroup label="{{if $class == "CBlocOperatoire"}}Salle de réveil{{else}}{{tr}}{{$class}}{{/tr}}{{/if}}">
                    {{foreach from=$list item=room}}
                      <option value="{{$room->_guid}}" {{if $object_guid == $room->_guid}}selected="selected"{{/if}}>{{if $room->_id}}{{$room}}{{else}}Toutes{{/if}}</option>
                    {{/foreach}}
                  </optgroup>
                {{/foreach}}
              </select>
            </td>
					</tr>
					<tr>
						<td colspan="8" class="button">
              <button type="submit" class="submit">{{tr}}Filter{{/tr}}</button>
              Seules les 30 dernières checklists sont affichées
            </td>
          </tr>
				</table>
			</form>
      
			<table class="main tbl">
				<tr>
          <th>{{mb_title class=CDailyCheckList field=date}}</th>
          <th>{{mb_title class=CDailyCheckList field=object_class}}</th>
          <th>{{mb_title class=CDailyCheckList field=object_id}}</th>
          <th>{{mb_title class=CDailyCheckList field=type}}</th>
          <th>{{mb_title class=CDailyCheckList field=comments}}</th>
          <th>{{mb_title class=CDailyCheckList field=validator_id}}</th>
				</tr>
				{{foreach from=$list_check_lists item=curr_list}}
				<tr>
          <td><a href="?m={{$m}}&amp;tab={{$tab}}&amp;check_list_id={{$curr_list->_id}}">{{mb_value object=$curr_list field=date}}</a></td>
          <td>{{mb_value object=$curr_list field=object_class}}</td>
          <td>
            <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_list->_ref_object->_guid}}')">
              {{$curr_list->_ref_object}}
            </span>
          </td>
          <td>{{mb_value object=$curr_list field=type}}</td>
          <td>{{mb_value object=$curr_list field=comments}}</td>
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
					<th class="title" colspan="2">{{$check_list}}</th>
				</tr>
        <tr>
          <th>{{mb_label object=$check_list field=date}}</th>
          <td>{{mb_value object=$check_list field=date}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$check_list field=object_id}}</th>
          <td>
            <span onmouseover="ObjectTooltip.createEx(this, '{{$check_list->_ref_object->_guid}}')">
              {{$check_list->_ref_object}}
            </span>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$check_list field=type}}</th>
          <td>{{mb_value object=$check_list field=type}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$check_list field=validator_id}}</th>
          <td>{{mb_value object=$check_list field=validator_id}}</td>
        </tr>
        
        <tr>
          <td colspan="2" style="padding: 0;">
            <table class="main">
              {{assign var=category_id value=0}}
              {{foreach from=$check_list->_back.items item=_item}}
                {{assign var=curr_type value=$_item->_ref_item_type}}
                {{if $curr_type->category_id != $category_id}}
                  <tr>
                    <th colspan="3" class="text category" style="text-align: left;">
                      <strong>{{$curr_type->_ref_category->title}}</strong>
                      {{if $curr_type->_ref_category->desc}}
                        &ndash; {{$curr_type->_ref_category->desc}}
                      {{/if}}
                    </th>
                  </tr>
                {{/if}}
                <tr>
                  <td style="padding-left: 1em; width: 100%;" class="text" colspan="2">
                    {{mb_value object=$curr_type field=title}}
                    <small style="text-indent: 1em; color: #666;">{{mb_value object=$curr_type field=desc}}</small>
                  </td>
                  <td class="text" {{if $_item->checked == 0 && $_item->checked !== null}}style="color: red; font-weight: bold;"{{/if}}>
                    {{$_item->getAnswer()}}
                  </td>
                </tr>
                {{assign var=category_id value=$curr_type->category_id}}
              {{foreachelse}}
                <tr>
                  <td colspan="3">{{tr}}CDailyCheckItemType.none{{/tr}}</td>
                </tr>
              {{/foreach}}
              <tr>
                <td colspan="3">
                  <strong>Commentaires:</strong><br />
                  {{mb_value object=$check_list field=comments}}
                </td>
              </tr>
            </table>
          </td>
        </tr>
			</table>
		</td>
		{{/if}}
	</tr>
</table>
