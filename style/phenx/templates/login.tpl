{{include file="common.tpl" nodebug=true}}

<div id="login" {{if $dialog}}style="height: 50px; margin: auto; position: relative; top: 0; left: 0;"{{/if}}>
  <h2>{{$dPconfig.company_name}}</h2>
  {{if !$dialog}}
    <a href="http://www.mediboard.org/">
      <img src="images/pictures/logo.png" alt="{{$dPconfig.company_name}}" title="{{$dPconfig.company_name}}" width="290" height="107" /><br />
    </a>
  {{/if}}

  <form name="loginFrm" action="?" method="post" onsubmit="return checkForm(this)">
    <input type="hidden" name="login" value="{{$time}}" />
    <input type="hidden" name="redirect" value="{{$redirect|smarty:nodefaults}}" />
    <input type="hidden" name="dialog" value="{{$dialog}}" />
    <table class="form">
      <tr>
        <th style="width: 1%;"><label for="username" title="{{tr}}CUser-user_username-desc{{/tr}}">{{tr}}CUser-user_username{{/tr}}</label></th>
        <td style="width: 1%;"><input type="text" class="notNull str" size="25" maxlength="20" name="username" /></td>
      </tr>
      <tr>
        <th><label for="password" title="{{tr}}CUser-user_password-desc{{/tr}}">{{tr}}CUser-user_password{{/tr}}</label></th>
        <td>
          <input type="password" class="notNull str" size="25" maxlength="32" name="password" />
          <button class="tick" type="submit" name="login">{{tr}}Login{{/tr}}</button>
        </td>
      </tr>
    </table>
  </form>
  <div id="systemMsg">{{$errorMessage|nl2br|smarty:nodefaults}}</div>
</div>

{{if !$dialog}}
  <div id="version" title="Plateforme Open Source pour les Etablissements de Santé">v.{{$version.string}}</div>
{{/if}}


</body>
</html>