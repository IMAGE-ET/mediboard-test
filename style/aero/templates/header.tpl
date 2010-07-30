{{include file="../../mediboard/templates/common.tpl" nodebug=true}}

{{if !$offline && !$dialog}}

{{include file="../../mediboard/templates/message.tpl"}}

<table id="headerMenu">
  <tr>
    <td class="menuTitle" id="modMenu" onmouseover="$(this).down().show()" onmouseout="$(this).down().hide()">
      Modules
      <div class="dropdown" style="display: none; position: absolute;">
		    {{foreach from=$modules key=mod_name item=currModule}}
		    {{if $currModule->mod_ui_active && $currModule->_can->view}}
          <a href="?m={{$mod_name}}" title="{{tr}}module-{{$mod_name}}-long{{/tr}}" class="{{if $mod_name == $m}}selected{{/if}}">
            {{thumb src="modules/$mod_name/images/icon.png" h="16" w="16" f="png"}}
            {{tr}}module-{{$mod_name}}-court{{/tr}}
          </a>
        {{/if}}
        {{/foreach}}
      </div>
    </td>
    <td class="menuTitle" id="toolMenu" onmouseover="$(this).down().show()" onmouseout="$(this).down().hide()">
      Outils
      <div class="dropdown" style="display: none; position: absolute;">
        <a href="{{$portal.help}}" target="_blank">
          <img src="style/aero/images/icons/help.png" title="{{tr}}portal-help{{/tr}}" />
          {{tr}}portal-help{{/tr}}
        </a>
        <a href="{{$portal.tracker}}" target="_blank">
          <img src="style/aero/images/icons/modif.png" title="{{tr}}portal-tracker{{/tr}}" />
          {{tr}}portal-tracker{{/tr}}
        </a>
        <a href="#1" onclick="popChgPwd()">
          <img src="style/aero/images/icons/passwd.png" title="{{tr}}menu-changePassword{{/tr}}" />
          {{tr}}menu-changePassword{{/tr}}
        </a>
        <a href="?m=mediusers&amp;a=edit_infos">
          <img src="style/aero/images/icons/myinfos.png" title="{{tr}}menu-myInfo{{/tr}}" />
          {{tr}}menu-myInfo{{/tr}}
        </a>
        <a href="#1" onclick="Session.lock()">
          <img src="style/aero/images/icons/lock.png" title="{{tr}}menu-lockSession{{/tr}}" />
          {{tr}}menu-lockSession{{/tr}}
        </a>
        <a href="#1" onclick="UserSwitch.popup()">
          <img src="./images/icons/switch.png" title="{{tr}}menu-switchUser{{/tr}}" />
          {{tr}}menu-switchUser{{/tr}}
        </a>
        <a href="?logout=-1">
          <img src="style/aero/images/icons/logout.png" title="{{tr}}menu-logout{{/tr}}" />
          {{tr}}menu-logout{{/tr}}
        </a>
      </div>
    </td>
    <td class="titlecell">
      <img src="./modules/{{$m}}/images/icon.png" height="24" width="24" />
    </td>
    <td class="titlecell">
      {{tr}}module-{{$m}}-long{{/tr}}
    </td>
    <td class="message">
      {{if !$dialog}}
      <!-- System messages -->
      <div id="systemMsg">
        {{$errorMessage|nl2br|smarty:nodefaults}}
      </div>
      {{/if}}
    </td>
    <td class="end">
      {{mb_include module=mediboard template=svnstatus}}
      {{mb_include module=mediboard template=change_group}}
    </td>
  </tr>
</table>
{{/if}}

<table id="main" class="{{if $dialog}}dialog {{/if}}{{$m}}">
  <tr>
    <td class="tabox">
      {{mb_include module=mediboard template=obsolete_module}}
{{if $dialog}}
	  <!-- System messages -->
	  <div id="systemMsg">
	    {{$errorMessage|nl2br|smarty:nodefaults}}
	  </div>
{{/if}}
