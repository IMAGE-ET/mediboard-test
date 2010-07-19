{{include file="../../mediboard/templates/common.tpl"}}

<div id="login">
  <form name="loginFrm" action="?" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="login" value="{{$time}}" />
  <input type="hidden" name="redirect" value="{{$redirect|smarty:nodefaults}}" />
  <input type="hidden" name="dialog" value="{{$dialog}}" />
  <table class="form">
    {{if !$dialog}}
    <tr>
      <th class="title" colspan="10">
        {{$dPconfig.company_name}}
      </th>
    </tr>
    
    <tr>
      <td class="logo" colspan="10">
        <a href="{{$dPconfig.system.website_url}}">
          <img src="images/pictures/logo.png" alt="MediBoard logo" />
        </a>
        <p>
          Plateforme Open Source pour les Etablissements de Sant�<br/>
          Version {{$version.string}}
        </p>
      </td>
    </tr>
    {{/if}}
    <tr>
      <th class="category" colspan="10">Connexion</th>
    </tr>

    <tr>
      <th><label for="username" title="{{tr}}CUser-user_username-desc{{/tr}}">{{tr}}CUser-user_username{{/tr}}</label></th>
      <td><input type="text" class="notNull str" size="15" maxlength="25" name="username" /></td>
      <td rowspan="2">
        <div id="systemMsg">
          {{$errorMessage|nl2br|smarty:nodefaults}}
        </div>
      </td>
    </tr>
    
    <tr>
      <th><label for="password" title="{{tr}}CUser-user_password-desc{{/tr}}">{{tr}}CUser-user_password{{/tr}}</label></th>
      <td><input type="password" class="notNull str" size="15" maxlength="25" name="password" /></td>
    </tr>
    
    <tr>
      <td colspan="10" class="button"><button class="tick" type="submit" name="login">{{tr}}Login{{/tr}}</button></td>
    </tr>
    </table>
  </form>
</div>

</body>
</html>