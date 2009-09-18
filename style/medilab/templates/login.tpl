{{include file="../../mediboard/templates/common.tpl"}}

<div id="login">
  <form name="loginFrm" action="?" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="login" value="{{$time}}" />
  <input type="hidden" name="redirect" value="{{$redirect|smarty:nodefaults}}" />
  <input type="hidden" name="dialog" value="{{$dialog}}" />
  <table class="form">
    {{if !$dialog}}    
    <tr>
      <td class="logo" colspan="3 ">
        <a href="{{$dPconfig.system.website_url}}">
          <img src="style/{{$uistyle}}/images/pictures/medilab.jpg" alt="MediLab logo" />
        </a>
        <p>
          Plateforme de prescription d'analyses et dossier patient
          <br/>
          Version {{$version.string}}
        </p>
      </td>
    </tr>

    {{/if}}
    <tr>
      <th class="category" colspan="2">Connexion</th>
      {{if $dPconfig.demo_version}}
      <th class="category">Comptes disponibles</th>
      {{/if}}
    </tr>
    
    <tr>
      <th><label for="username" title="{{tr}}CUser-user_username-desc{{/tr}}">{{tr}}CUser-user_username{{/tr}}</label></th>
      <td><input type="text" class="notNull str" size="25" maxlength="20" name="username" /></td>
      {{if $dPconfig.demo_version}}
      <td rowspan="3" class="category">
        <strong>Administrateur</strong>: admin/admin<br />
        <strong>Chirurgien</strong>: chir/chir<br />
        <strong>PMSI</strong>: pmsi/pmsi<br />
        <strong>Surveillante de bloc</strong>: survbloc/survbloc<br />
        <strong>Hospitalisation</strong>: hospi/hospi
      </td>
      {{/if}}
    </tr>
    
    <tr>
      <th><label for="password" title="{{tr}}CUser-user_password-desc{{/tr}}">{{tr}}CUser-user_password{{/tr}}</label></th>
      <td><input type="password"  class="notNull str" size="25" maxlength="32" name="password" /></td>
    </tr>
    
    <tr>
      <td colspan="2" class="button"><button class="tick" type="submit" name="login">{{tr}}Login{{/tr}}</button></td>
    </tr>
    {{if !$dialog}}
     <tr>
      <th class="category" colspan="3">
        {{$dPconfig.company_name}}
      </th>
    </tr>
    <tr>
      <td class="logo" colspan="3 ">
        <a href="http://www.mediboard.org/">
          <img src="style/{{$uistyle}}/images/pictures/proxilab-360.jpg" alt="MediLab logo" />
        </a>
      </td>
    </tr>
    {{/if}}
    </table>
  </form>
</div>

<!-- System messages -->
<div id="systemMsg">
  {{$errorMessage|nl2br|smarty:nodefaults}}
</div>

</body>
</html>