<h3 onmouseover="ObjectTooltip.createEx(this, '{{$user->_guid}}')">
  {{$user}}

  <span style="float: right;">
    {{$user->_count.authentications}} {{tr}}CUser-back-authentications{{/tr}}
  </span>
</h3>

<table class="main tbl">
  <tr>
    <th>{{mb_title class=CUserAuthentication field=auth_method}}</th>
    <th>{{mb_title class=CUserAuthentication field=datetime_login}}</th>
    {{*<th>{{mb_title class=CUserAuthentication field=datetime_logout}}</th>*}}
    <th>{{mb_title class=CUserAuthentication field=id_address}}</th>
    <th>{{mb_title class=CUserAuthentication field=screen_width}}</th>
    <th>{{mb_title class=CUserAuthentication field=user_agent_id}}</th>
  </tr>

  {{foreach from=$list item=_auth}}
    <tr>
      <td>{{mb_value object=$_auth field=auth_method}}</td>
      <td>{{mb_value object=$_auth field=datetime_login}}</td>
      {{*<td>{{mb_value object=$_auth field=datetime_logout}}</td>*}}
      <td>{{mb_value object=$_auth field=id_address}}</td>
      <td>
        {{if $_auth->screen_width && $_auth->screen_height}}
          {{mb_value object=$_auth field=screen_width}}x{{mb_value object=$_auth field=screen_height}}
        {{/if}}
      </td>
      <td class="compact">{{mb_value object=$_auth field=user_agent_id}}</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="6">{{tr}}CUserAuthentication.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>