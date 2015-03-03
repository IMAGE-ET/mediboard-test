{{mb_include style=mediboard template=common}}

<div id="login" {{if $dialog}} class="dialog" {{/if}}>
  {{if !$dialog}}
    <a href="{{$conf.system.website_url}}">
      {{mb_include style="mediboard" template="logo" alt=$conf.company_name title=$conf.company_name width="400"}}
      <br />
    </a>
  {{/if}}
  
  <h2>{{$conf.company_name}}</h2>

  <form name="loginFrm" action="?" method="post" onsubmit="return checkForm(this)">
    <input type="hidden" name="login" value="{{$time}}" />
    <input type="hidden" name="redirect" value="{{$redirect|smarty:nodefaults}}" />
    <input type="hidden" name="dialog" value="{{$dialog}}" />
    <table class="form">
      <tr>
        <th style="width: 1%;">
          <label for="username" title="{{tr}}CUser-user_username-desc{{/tr}}">{{tr}}CUser-user_username{{/tr}}</label>
        </th>
        <td style="width: 1%;">
          <input type="text" class="notNull str" size="15" name="username" />
        </td>
      </tr>
      <tr>
        <th>
          <label for="password" title="{{tr}}CUser-user_password-desc{{/tr}}">{{tr}}CUser-user_password{{/tr}}</label>
        </th>
        <td>
          <input type="password" class="notNull str" size="15" name="password" />
          <button class="tick" type="submit" name="login">{{tr}}Login{{/tr}}</button>
        </td>
      </tr>
    </table>
  </form>
  <div id="systemMsg">{{$errorMessage|nl2br|smarty:nodefaults}}</div>
</div>

{{if !$dialog}}
  <div id="version" title="Plateforme Open Source pour les Etablissements de Santé">
    Version {{$version.version}}
    {{if $applicationVersion.releaseCode}}
      - Branche : {{$applicationVersion.releaseTitle|capitalize}}
    {{/if}}
  </div>
{{/if}}

{{mb_include style=mediboard template=common_end nodebug=true}}
