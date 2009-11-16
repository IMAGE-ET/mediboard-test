{{include file="../../mediboard/templates/common.tpl" nodebug=true}}

{{if !$offline && !$dialog}}

{{include file="../../mediboard/templates/message.tpl"}}

<table id="headerMenu">
  <tr>
    <td class="menuTitle" id="modMenu" onmouseover="$('modMenuList').show()" onmouseout="$('modMenuList').hide()">
      Modules
      <div id="modMenuList" style="display: none; position: absolute;">
		    {{foreach from=$modules key=mod_name item=currModule}}
		    {{if $currModule->mod_ui_active && $currModule->_can->view}}
        <div class="menuItem {{if $mod_name == $m}}selected{{/if}}">
          <a href="?m={{$mod_name}}" title="{{tr}}module-{{$mod_name}}-long{{/tr}}">
            {{thumb src="modules/$mod_name/images/icon.png" h="16" w="16" f="png"}}
            {{tr}}module-{{$mod_name}}-court{{/tr}}
          </a>
        </div>
        {{/if}}
        {{/foreach}}
      </div>
    </td>
    <td class="menuTitle" id="toolMenu" onmouseover="$('toolMenuList').show()" onmouseout="$('toolMenuList').hide()">
      Outils
      <div id="toolMenuList" style="display: none; position: absolute;">
        <div class="menuItem">
          <a href="{{$portal.help}}" target="_blank">
            <img src="style/aero/images/icons/help.png" alt="{{tr}}portal-help{{/tr}}" border="0" height="16" width="16" />
            {{tr}}portal-help{{/tr}}
          </a>
        </div>
        <div class="menuItem">
          <a href="{{$portal.tracker}}" target="_blank">
            <img src="style/aero/images/icons/modif.png" alt="{{tr}}portal-tracker{{/tr}}" border="0" height="16" width="16" />
            {{tr}}portal-tracker{{/tr}}
          </a>
        </div>
        <div class="menuItem">
          <a href="#1" onclick="popChgPwd()">
            <img src="style/aero/images/icons/passwd.png" alt="{{tr}}menu-changePassword{{/tr}}" border="0" height="16" width="16" />
            {{tr}}menu-changePassword{{/tr}}
          </a>
        </div>
        <div class="menuItem">
          <a href="?m=mediusers&amp;a=edit_infos">
            <img src="style/aero/images/icons/myinfos.png" alt="{{tr}}menu-myInfo{{/tr}}" border="0" height="16" width="16" />
            {{tr}}menu-myInfo{{/tr}}
          </a>
        </div>
        <div class="menuItem">
          <a href="#1" onclick="Session.lock()">
            <img src="style/aero/images/icons/lock.png" alt="{{tr}}menu-lockSession{{/tr}}" border="0" height="16" width="16" />
            {{tr}}menu-lockSession{{/tr}}
          </a>
        </div>
        <div class="menuItem">
          <a href="#1" onclick="UserSwitch.popup()">
            <img src="./images/icons/switch.png" alt="{{tr}}menu-switchUser{{/tr}}" border="0" height="16" width="16" />
            {{tr}}menu-switchUser{{/tr}}
          </a>
        </div>
        <div class="menuItem">
          <a href="?logout=-1">
            <img src="style/aero/images/icons/logout.png" alt="{{tr}}menu-logout{{/tr}}" border="0" height="16" width="16" />
            {{tr}}menu-logout{{/tr}}
          </a>
        </div>
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

      <form name="ChangeGroup" action="" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
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
{{/if}}

<table id="main" class="{{if $dialog}}dialog{{/if}} {{$m}}">
  <tr>
  
{{if $dialog}}
    <td class="tabox">
	  <!-- System messages -->
	  <div id="systemMsg">
	    {{$errorMessage|nl2br|smarty:nodefaults}}
	  </div>
{{/if}}
