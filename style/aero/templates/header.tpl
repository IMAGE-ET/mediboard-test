{{include file="../../mediboard/templates/common.tpl"}}

{{if !$offline}}

<script type="text/javascript">
function chgMenu(id, type) {
  $(id).style.visibility = type;
}
</script>

{{if !$dialog}}

{{foreach from=$messages item=currMsg}}
  <div style='{{if $currMsg->urgence == "urgent"}}background: #eee; color: #f00;{{else}}background: #aaa; color: #fff;{{/if}}'>
    <strong>{{$currMsg->titre}}</strong> : {{$currMsg->corps}}
  </div>
{{/foreach}}

<table id="headerMenu">
  <tr>
    <td class="menuTitle" id="modMenu" onmouseover="chgMenu('modMenuList', 'visible')" onmouseout="chgMenu('modMenuList', 'hidden')">
      Modules
      <div id="modMenuList" style="visibility: hidden; position: absolute">
		    {{foreach from=$modules key=mod_name item=currModule}}
        <div class="menuItem {{if $mod_name == $m}}selected{{/if}}">
          <a href="?m={{$mod_name}}" title="{{tr}}module-{{$mod_name}}-long{{/tr}}">
            {{thumb src="images/modules/$mod_name.png" h="16" w="16" f="png"}}
            {{tr}}module-{{$mod_name}}-court{{/tr}}
          </a>
        </div>
        {{/foreach}}
      </div>
    </td>
    <td class="menuTitle" id="toolMenu" onmouseover="chgMenu('toolMenuList', 'visible')" onmouseout="chgMenu('toolMenuList', 'hidden')">
      Outils
      <div id="toolMenuList" style="visibility: hidden; position: absolute">
        <div class="menuItem">
          <img src="style/aero/images/icons/help.png" alt="Aide" border="0" height="16" width="16" />
          <a href="{{$portal.help}}" title="{{tr}}portal-help{{/tr}}" target="_blank">{{tr}}portal-help{{/tr}}</a>
        </div>
        <div class="menuItem">
          <img src="style/aero/images/icons/modif.png" alt="Suggestions" border="0" height="16" width="16" />
          <a href="{{$portal.tracker}}" title="{{tr}}portal-tracker{{/tr}}" target="_blank">{{tr}}portal-tracker{{/tr}}</a>
        </div>
        <div class="menuItem">
          <a href="#" onclick="popChgPwd();">
            <img src="style/aero/images/icons/passwd.png" alt="Mot de passe" border="0" height="16" width="16" />
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
          <a href="?m=admin&amp;a=edit_prefs&amp;user_id={{$app->user_id}}">
            <img src="style/aero/images/icons/prefs.png" alt="Preferences" border="0" height="16" width="16" />
            {{tr}}mod-admin-tab-edit_prefs{{/tr}}
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
      <img src="./images/modules/{{$m}}.png" height="24" width="24" />
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
      <form name="ChangeGroup" action="" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <select name="g" onchange="ChangeGroup.submit();">
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
