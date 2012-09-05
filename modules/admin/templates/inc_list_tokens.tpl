<button class="new" style="float: right;" onclick="ViewAccessToken.edit(0)">{{tr}}CViewAccessToken-title-create{{/tr}}</button>

<table class="main tbl">
  <tr>
    <th>{{mb_title class=CViewAccessToken field=user_id}}</th>
    <th>{{mb_title class=CViewAccessToken field=datetime_start}}</th>
    <th>{{mb_title class=CViewAccessToken field=ttl_hours}}</th>
    <th>{{mb_title class=CViewAccessToken field=first_use}}</th>
  </tr>
  
  {{foreach from=$tokens item=_token}}
    <tr>
      <td>
        <a href="#1" onclick="ViewAccessToken.edit({{$_token->_id}}); return false;">
          {{mb_value object=$_token field=user_id}}
        </a>
      </td>
      <td>
        <a href="#1" onclick="ViewAccessToken.edit({{$_token->_id}}); return false;">
          {{mb_value object=$_token field=datetime_start}}
        </a>
      </td>
      <td>
        <a href="#1" onclick="ViewAccessToken.edit({{$_token->_id}}); return false;">
          {{mb_value object=$_token field=ttl_hours}}
        </a>
      </td>
      <td>{{mb_value object=$_token field=first_use}}</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="4">{{tr}}CViewAccessToken.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
