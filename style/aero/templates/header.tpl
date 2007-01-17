<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Mediboard :: Système de gestion des structures de santé</title>
  <meta http-equiv="Content-Type" content="text/html;charset={{$localeCharSet}}" />
  <meta name="Description" content="Mediboard: Plateforme Open Source pour les Etablissement de Santé" />
  <meta name="Version" content="{{$mediboardVersion}}" />
  {{$mediboardShortIcon|smarty:nodefaults}}
  {{$mediboardCommonStyle|smarty:nodefaults}}
  {{$mediboardStyle|smarty:nodefaults}}
  {{$mediboardScript|smarty:nodefaults}}
  {{if $offline}}
  <script type="text/javascript">
    var config = {"urlMediboard":"{{$baseUrl}}/index.php"};
  </script>
  {{/if}}
</head>

<body onload="main()">

<div id="waitingMsgMask" class="chargementMask" style="display: none;"></div>
<div id="waitingMsgText" class="chargementText" style="display: none;">
  <table class="tbl">
    <tr>
      <th class="title">
        <div class="loading"><span id="waitingInnerMsgText">Chargement en cours</span></div>
      </th>
    </tr>
  </table>
</div>
{{if !$offline}}
<script type="text/javascript">
function popChgPwd() {
  var url = new Url;
  url.setModuleAction("admin", "chpwd");
  url.popup(400, 300, "ChangePassword");
}

function chgMenu(id, type) {
  $(id).style.visibility = type;
}
</script>

{{if !$dialog}}

{{foreach from=$messages item=currMsg}}
  <div style='background: #aaa; color: #fff;'><strong>{{$currMsg->titre}}</strong> : {{$currMsg->corps}}</div>
{{/foreach}}

<table id="headerMenu">
  <tr>
    <td class="menuTitle" id="modMenu" onmouseover="chgMenu('modMenuList', 'visible')" onmouseout="chgMenu('modMenuList', 'hidden')">
      Modules
      <div id="modMenuList" style="visibility: hidden; position: absolute">
        {{foreach from=$affModule item=currModule}}
        <div class="menuItem {{if $currModule.modName==$m}}selected{{/if}}">
          <a href="?m={{$currModule.modName}}" title="{{$currModule.modNameLong}}">
            {{assign var="modname" value=$currModule.modName}}
            {{thumb src="images/modules/$modname.png" h="16" w="16" f="png"}}
            {{$currModule.modNameCourt}}
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
          {{$helpOnline|smarty:nodefaults}}
        </div>
        <div class="menuItem">
          <img src="style/aero/images/icons/modif.png" alt="Suggestions" border="0" height="16" width="16" />
          {{$suggestion|smarty:nodefaults}}
        </div>
        <div class="menuItem">
          <a href="#" onclick="popChgPwd();">
            <img src="style/aero/images/icons/passwd.png" alt="Mot de passe" border="0" height="16" width="16" />
            Changez votre mot de passe
          </a>
        </div>
        <div class="menuItem">
          <a href="?m=mediusers&amp;a=edit_infos">
            <img src="style/aero/images/icons/myinfos.png" alt="{{tr}}My Info{{/tr}}" border="0" height="16" width="16" />
            {{tr}}My Info{{/tr}}
          </a>
        </div>
        <div class="menuItem">
          <a href="?m=admin&amp;a=edit_prefs&amp;user_id={{$AppUI->user_id}}">
            <img src="style/aero/images/icons/prefs.png" alt="Preferences" border="0" height="16" width="16" />
            {{tr}}Préférences{{/tr}}
          </a>
        </div>
        <div class="menuItem">
          <a href="?logout=-1">
            <img src="style/aero/images/icons/logout.png" alt="{{tr}}Logout{{/tr}}" border="0" height="16" width="16" />
            {{tr}}Logout{{/tr}}
          </a>
        </div>
      </div>
    </td>
    {{if $titleBlockData.icon}}
    <td class="titlecell">
      {{$titleBlockData.icon|smarty:nodefaults}}
    </td>
    {{/if}}
    <td class="titlecell">
      {{tr}}{{$titleBlockData.name}}{{/tr}}
    </td>
    <td class="message">
      {{if !$dialog}}
      <div {{if $dialog}}class="dialog" {{if !$errorMessage}} style="display: none"{{/if}}{{/if}} id="systemMsg">
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
<table id="main" class="{{$m}}">
  <tr>
  
{{if $dialog}}
    <td class="tabox">
      <div class="dialog" {{if !$errorMessage}}style="display: none"{{/if}} id="systemMsg">
        {{$errorMessage|nl2br|smarty:nodefaults}}
      </div>
{{/if}}
