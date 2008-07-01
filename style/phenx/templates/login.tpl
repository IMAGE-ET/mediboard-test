{{include file="../../mediboard/templates/common.tpl"}}

<div id="login">
  <form name="loginFrm" action="?" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="login" value="{{$time}}" />
  <input type="hidden" name="redirect" value="{{$redirect|smarty:nodefaults}}" />
  <input type="hidden" name="dialog" value="{{$dialog}}" />
  
  {{if !$dialog}}
  <div>{{$dPconfig.company_name}}</th>
  <div class="logo">
    <a href="http://www.mediboard.org/">
      <img src="images/pictures/mbNormal.gif" alt="MediBoard logo" />
    </a>
  </div>
  {{/if}}

  <table class="form">
    <tr>
      <th>
        <label for="username" title="Nom de l'utilisateur pour s'authentifier">{{tr}}Username{{/tr}}</label>
      </th>
      <td><input type="text" class="notNull str" size="25" maxlength="20" name="username" /></td>
    </tr>
    <tr>
      <th><label for="password" title="Mot de passe d'authentification">{{tr}}Password{{/tr}}</label></th>
      <td><input type="password" class="notNull str" size="25" maxlength="32" name="password" /></td>
    </tr>
    <tr>
      <td colspan="2" class="button"><button class="tick" type="submit" name="login">{{tr}}login{{/tr}}</button></td>
    </tr>
  </table>
  
  {{if !$dialog}}
  <div>
    <a href="http://www.dotproject.net/">
      <img src="images/pictures/dp_icon.gif" alt="Basé sur dotProject version 1.02" />
    </a>
    <a href="http://www.mozilla-europe.org/fr/products/firefox/" title="Pour un meilleur confort et plus de sécurité, nous recommandons d'utiliser le navigateur Firefox">
      <img src="http://www.spreadfirefox.com/community/images/affiliates/Buttons/80x15/firefox_80x15.png" alt="Firefox Logo" />
    </a>
    Mediboard {{$version.string}}
  </div>
  {{/if}}
  
  </form>
  <div id="systemMsg">
    {{$errorMessage|nl2br|smarty:nodefaults}}
  </div>
</div>

</body>
</html>