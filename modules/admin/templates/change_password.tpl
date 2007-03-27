<h1>
  {{tr}}Change User Password{{/tr}}
</h1>
<form name="chpwdFrm" action="index.php?m={{$m}}&amp;a=chpwd" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="dosql" value="do_chpwd_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dialog" value="1" />

<table class="form">
  <tr>
    <td>
      <label for="old_pwd" title="{{tr}}Current Password{{/tr}}">
        {{tr}}Current Password{{/tr}}
      </label>
    </td>
    <td>
      <input class="notNull str" type="password" name="old_pwd" />
    </td>
  </tr>
  <tr>
    <td>
      <label for="new_pwd1" title="{{tr}}New Password{{/tr}}">
        {{tr}}New Password{{/tr}}
      </label>
    </td>
    <td>
      <input class="notNull str minLength|4" type="password" name="new_pwd1" />
    </td>
  </tr>
  <tr>
    <td>
      <label for="new_pwd2" title="{{tr}}Repeat New Password{{/tr}}">
        {{tr}}Repeat New Password{{/tr}}
      </label>
    </td>
    <td>
      <input class="notNull str sameAs|new_pwd1" type="password" name="new_pwd2" />
    </td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <button type="submit" class="submit">{{tr}}Submit{{/tr}}</button>
    </td>
  </tr>
</table>

</form>