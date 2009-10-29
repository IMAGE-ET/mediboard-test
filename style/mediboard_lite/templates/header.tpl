{{include file="../../mediboard/templates/common.tpl"}}

{{if !$offline && !$dialog}}

{{include file="../../mediboard/templates/message.tpl"}}

<table id="header" cellspacing="0"><!-- IE Hack: cellspacing should be useless -->
  <tr>
    <td id="menubar">
      <table>
        <tr>
          {{foreach from=$modules key=mod_name item=currModule}}  
          {{if $currModule->mod_ui_active && $currModule->_can->view}}  
          <td align="center">
            <a href="?m={{$mod_name}}">
              <img src="./modules/{{$mod_name}}/images/icon.png" alt="{{tr}}module-{{$mod_name}}-court{{/tr}}" height="48" width="48" />
              <br />{{tr}}module-{{$currModule->mod_name}}-court{{/tr}}
            </a>
          </td>
          {{/if}}
          {{/foreach}}
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td id="user">
      <table>
        <tr>
          <td id="userWelcome">
            <form name="ChangeGroup" action="" method="get">
              <span title="{{tr}}Last connection{{/tr}} : {{$app->user_last_login|date_format:$dPconfig.datetime}}">
                {{$app->user_first_name}} {{$app->user_last_name}}
              </span>
              <input type="hidden" name="m" value="{{$m}}" />
              <select name="g" onchange="this.form.submit();">
                {{foreach from=$Etablissements item=currEtablissement key=keyEtablissement}}
                <option value="{{$keyEtablissement}}" {{if $keyEtablissement==$g}}selected="selected"{{/if}}>
                  {{$currEtablissement->_view}}
                </option>
                {{/foreach}}
              </select>
				      {{mb_include module=mediboard template=svnstatus}}    
            </form>
          </td>
          <td id="userMenu">
            <a href="{{$portal.help}}" target="_blank">{{tr}}portal-help{{/tr}}</a> |
            <a href="{{$portal.tracker}}" target="_blank">{{tr}}portal-tracker{{/tr}}</a> |
            <a href="javascript:popChgPwd()">{{tr}}menu-changePassword{{/tr}}</a> |
            <a href="?m=mediusers&amp;a=edit_infos">{{tr}}menu-myInfo{{/tr}}</a> |
            <a href="javascript:Session.lock()">{{tr}}menu-lockSession{{/tr}}</a> |
            <a href="javascript:UserSwitch.popup()">{{tr}}menu-switchUser{{/tr}}</a> | 
            <a href="?logout=-1">{{tr}}menu-logout{{/tr}}</a>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
{{/if}}

<table id="main" class="{{if $dialog}}dialog{{/if}} {{$m}}">
  <tr>
    <td>
      <div id="systemMsg">
        {{$errorMessage|nl2br|smarty:nodefaults}}
      </div>
      {{if !$dialog && !$offline}}
      <table class='titleblock'>
        <tr>
          <td>
            <img src="./modules/{{$m}}/images/icon.png" alt="Icone {{$m}}" height="24" width="24" />
          </td>
          <td class='titlecell'>
            {{tr}}module-{{$m}}-long{{/tr}}
          </td>
        </tr>
      </table>
      {{/if}}