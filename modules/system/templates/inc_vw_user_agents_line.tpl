<td>
  <button class="edit notext compact" onclick="UserAgent.edit('{{$_user_agent->_id}}');">{{tr}}Edit{{/tr}}</button>
</td>

<td style="text-align: right;">{{mb_value object=$_user_agent field=browser_name}}</td>
<td>{{mb_value object=$_user_agent field=browser_version}}</td>

<td style="text-align: right;">{{mb_value object=$_user_agent field=platform_name}}</td>
<td {{if $_user_agent->platform_version == "unknown"}} class="empty" {{/if}}>{{mb_value object=$_user_agent field=platform_version}}</td>

<td {{if $_user_agent->device_name == "unknown"}} class="empty" {{/if}}>{{mb_value object=$_user_agent field=device_name}}</td>
<td {{if $_user_agent->device_maker == "unknown"}} class="empty" {{/if}}>{{mb_value object=$_user_agent field=device_maker}}</td>
<td {{if $_user_agent->device_type == "unknown"}} class="empty" {{/if}}>{{mb_value object=$_user_agent field=device_type}}</td>
<td {{if $_user_agent->pointing_method == "unknown"}} class="empty" {{/if}}>{{mb_value object=$_user_agent field=pointing_method}}</td>

<td>
  <a href="#" onclick="UserAgent.openAuthentications('{{$_user_agent->_id}}');">
    {{$_user_agent->_count.user_authentications}}
  </a>
</td>

<td class="compact text">{{mb_value object=$_user_agent field=user_agent_string}}</td>