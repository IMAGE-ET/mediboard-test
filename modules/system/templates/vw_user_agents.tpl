<table class="main tbl">
  <tr>
    <th>{{mb_title class=CUserAgent field=browser_name}}</th>
    <th>{{mb_title class=CUserAgent field=browser_version}}</th>

    <th>{{mb_title class=CUserAgent field=platform_name}}</th>
    <th>{{mb_title class=CUserAgent field=platform_version}}</th>

    <th>{{mb_title class=CUserAgent field=device_name}}</th>
    <th>{{mb_title class=CUserAgent field=device_maker}}</th>
    <th>{{mb_title class=CUserAgent field=device_type}}</th>
    <th>{{mb_title class=CUserAgent field=pointing_method}}</th>
    <th>{{tr}}CUserAgent-back-user_authentications{{/tr}}</th>
    <th>{{mb_title class=CUserAgent field=user_agent_string}}</th>
  </tr>
  
  {{foreach from=$user_agents item=_user_agent}}
    <tr>
      <td style="text-align: right;">{{mb_value object=$_user_agent field=browser_name}}</td>
      <td>{{mb_value object=$_user_agent field=browser_version}}</td>

      <td style="text-align: right;">{{mb_value object=$_user_agent field=platform_name}}</td>
      <td {{if $_user_agent->platform_version == "unknown"}} class="empty" {{/if}}>{{mb_value object=$_user_agent field=platform_version}}</td>

      <td {{if $_user_agent->device_name == "unknown"}} class="empty" {{/if}}>{{mb_value object=$_user_agent field=device_name}}</td>
      <td {{if $_user_agent->device_maker == "unknown"}} class="empty" {{/if}}>{{mb_value object=$_user_agent field=device_maker}}</td>
      <td {{if $_user_agent->device_type == "unknown"}} class="empty" {{/if}}>{{mb_value object=$_user_agent field=device_type}}</td>
      <td {{if $_user_agent->pointing_method == "unknown"}} class="empty" {{/if}}>{{mb_value object=$_user_agent field=pointing_method}}</td>
      <td>{{$_user_agent->_count.user_authentications}}</td>
      <td class="compact text">{{mb_value object=$_user_agent field=user_agent_string}}</td>
    </tr>
  {{/foreach}}
</table>