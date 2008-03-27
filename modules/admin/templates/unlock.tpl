<!-- $Id: -->

{{if $app->user_type == 1 && $app->user_id != $curr_user->_id && $curr_user->_login_locked}}
<form name="unlock-{{$curr_user->_id}}" action="?m={{$m}}&amp;tab={{$tab}}" method="post">
<input type="hidden" name="dosql" value="do_user_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="user_id" value="{{$curr_user->_id}}" />
<input type="hidden" name="user_login_errors" value="0" />

<button type="submit" class="tick">
  {{tr}}Débloquer{{/tr}}
</button>
  
</form>
{{/if}}
