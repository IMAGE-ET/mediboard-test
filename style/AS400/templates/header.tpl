{{include file="../../mediboard/templates/common.tpl" nodebug=true}}

{{if !$offline && !$dialog}}

{{include file="../../mediboard/templates/message.tpl"}}

<table id="header" cellspacing="0">
  <tr>
    <td id="mainHeader">
      <table>
        <tr>
          <td width="1%">
            <table class="titleblock">
              <tr>
                <td>
                  <img src="./modules/{{$m}}/images/icon.png" alt="Icone {{$m}}" height="24" width="24" />
                </td>
                <td class="titlecell">
                  {{tr}}module-{{$m}}-long{{/tr}}
                </td>
              </tr>
            </table>
          </td>
          <td class="welcome">
            <form name="ChangeGroup" action="" method="get">
              <input type="hidden" name="m" value="{{$m}}" />
              <span title="{{tr}}Last connection{{/tr}} : {{$app->user_last_login|date_format:$dPconfig.datetime}}">
              {{$app->user_first_name}} {{$app->user_last_name}}
              </span>
              <br />
					    {{mb_include module=mediboard template=svnstatus}}    
              -
              <select name="g" onchange="this.form.submit();">
                {{foreach from=$Etablissements item=currEtablissement key=keyEtablissement}}
                <option value="{{$keyEtablissement}}" {{if $keyEtablissement==$g}}selected="selected"{{/if}}>
                  {{$currEtablissement->_view}}
                </option>
                {{/foreach}}
              </select>
            </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td id="menubar">
      | <a href="{{$portal.help}}" title="{{tr}}portal-help{{/tr}}" target="_blank">{{tr}}portal-help{{/tr}}</a>
	    {{foreach from=$modules key=mod_name item=currModule}}
	    {{if $currModule->_can->view && $currModule->mod_ui_active}}
      <a href="?m={{$mod_name}}" class="{{if $mod_name==$m}}textSelected{{else}}textNonSelected{{/if}}">
        {{tr}}module-{{$mod_name}}-court{{/tr}}
      </a> |
      {{/if}}
      {{/foreach}}
      <a href="#1" onclick="popChgPwd()">{{tr}}menu-changePassword{{/tr}}</a> | 
      <a href="?m=mediusers&amp;a=edit_infos">{{tr}}menu-myInfo{{/tr}}</a> | 
      <a href="#1" onclick="UserSwitch.popup()">{{tr}}menu-switchUser{{/tr}}</a> | 
      <a href="#1" onclick="Session.lock()">{{tr}}menu-lockSession{{/tr}}</a> |
      <a href="?logout=-1">{{tr}}menu-logout{{/tr}}</a> |
    </td>
  </tr>
</table>
{{/if}}

<div id="systemMsg">
  {{$errorMessage|nl2br|smarty:nodefaults}}
</div>

<table id="main" class="{{if $dialog}}dialog{{/if}} {{$m}}">

<tr>
  <td>