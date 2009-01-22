<table class="tbl">
  <tr>
    <th>{{mb_title class=CUser field=user_username}}</th>
    <th>{{tr}}CUser{{/tr}}</th>
  </tr>
  
  {{foreach from=$siblings key=user_name item=users}}
  <tr>
    <td>{{$user_name}}</td>
    <td>
      {{foreach from=$users item=_user}}
      	<a href="?m=admin&amp;tab=vw_edit_users&amp;user_id={{$_user->_id}}">{{$_user}}</a>
			{{/foreach}}
    </td>
  </tr>
	{{/foreach}}

</table>
