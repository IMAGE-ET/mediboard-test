<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Mediboard :: Syst�me de gestion des structures de sant�</title>
  <meta http-equiv="Content-Type" content="text/html;charset=<?php echo isset( $locale_char_set ) ? $locale_char_set : 'UTF-8';?>" />
  <meta name="Description" content="Mediboard: Plateforme Open Source pour les Etablissement de Sant�" />
  <meta name="Version" content="{{$mediboardVersion}}" />
  {{$mediboardShortIcon}}
  {{$mediboardCommonStyle}}
  {{$mediboardStyle}}
  {{$mediboardScript}}
</head>

<body onload="main()">

{{if !$dialog}}

{{foreach from=$messages item=currMsg}}
  <div style='background: #aaa; color: #fff;'><strong>{{$currMsg->titre}}</strong> : {{$currMsg->corps}}</div>
{{/foreach}}

<table id="header" cellspacing="0">
  <tr>
    <td id="menubar">
      <table>
        <tbody id="menuIcons">
        <tr>
          <td />
          {{foreach from=$affModule item=currModule}}    
          <td align="center" class="{{if $currModule.modName==$m}}iconSelected{{else}}iconNonSelected{{/if}}">
            <a href="?m={{$currModule.modName}}" title="{{$currModule.modNameLong}}">
              <img src="modules/{{$currModule.modName}}/images/{{$currModule.modName}}.png" alt="{{$currModule.modNameCourt}}" height="48" width="48" />
            </a>
          </td>
          {{/foreach}}
        </tr>
        </tbody>
        <tr>
          <td>
            <button id="menuIcons-trigger" type="button" style="float:left" />
          </td>
          {{foreach from=$affModule item=currModule}}
          <td align="center" class="{{if $currModule.modName==$m}}textSelected{{else}}textNonSelected{{/if}}" title="{{$currModule.modNameLong}}">
            <a href="?m={{$currModule.modName}}">
              <strong>{{$currModule.modNameCourt}}</strong>
            </a>
          </td>
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
            {{tr}}Welcome{{/tr}} {{$AppUI->user_first_name}} {{$AppUI->user_last_name}}
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
          <td id="userMenu">
            {{$helpOnline}} |
            {{$suggestion}} |
            <a href="./index.php?m=admin&amp;a=viewuser&amp;user_id={{$AppUI->user_id}}">{{tr}}My Info{{/tr}}</a> |
            <a href="./index.php?logout=-1">{{tr}}Logout{{/tr}}</a>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<script language="JavaScript" type="text/javascript">
  new PairEffect("menuIcons", { bStartVisible: true });
</script>
{{/if}}

<table id="main" class="{{$m}}">
  <tr>
    <td>
      <div id="systemMsg">
        {{$errorMessage}}
      </div>
      {{if !$dialog}}
      {{$titleBlock}}
      {{/if}}