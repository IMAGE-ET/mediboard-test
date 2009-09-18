{{include file="../../mediboard/templates/common.tpl"}}

<div id="login">
  <form name="loginFrm" action="?" method="post">
  <input type="hidden" name="login" value="{{$time}}" />
  <input type="hidden" name="redirect" value="{{$redirect|smarty:nodefaults}}" />
  <input type="hidden" name="dialog" value="{{$dialog}}" />
  <table class="form">
    {{if !$dialog}}
    <tr>
      <th class="category" colspan="3">{{$dPconfig.company_name}}</th>
    </tr>
    <tr>
      <td class="logo" colspan="3 ">
        <a href="{{$dPconfig.system.website_url}}">
          <img src="images/pictures/logo.png" alt="MediBoard logo" />
        </a>
        <p>
          Plateforme Open Source pour les Etablissements de Santé<br/>
          Version {{$version.string}}
        </p>
      </td>
    </tr>
    {{/if}}
    <tr>
      <th class="category" rowspan="6" style="vertical-align: middle;">
        <img src="./style/{{$uistyle}}/images/pictures/tonkin.gif" alt="Groupe Tonkin" />
      </th>
      <th class="category" colspan="2">Connexion</th>
    </tr>
    <tr>
      <th><label for="username" title="{{tr}}CUser-user_username-desc{{/tr}}">{{tr}}CUser-user_username{{/tr}}</label></th>
      <td><input type="text" class="notNull str" size="25" maxlength="20" name="username" class="text" /></td>
    </tr>
    <tr>
      <th><label for="password" title="{{tr}}CUser-user_password-desc{{/tr}}">{{tr}}CUser-user_password{{/tr}}</label></th>
      <td><input type="password"  class="notNull str" size="25" maxlength="32" name="password" class="text" /></td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        <button class="tick" type="submit" name="login">
          {{tr}}Login{{/tr}}
        </button>
      </td>
    </tr>
	</table>
  </form>
</div>

<!-- System messages -->
<div id="systemMsg">
  {{$errorMessage|nl2br|smarty:nodefaults}}
</div>

</body>
</html>    