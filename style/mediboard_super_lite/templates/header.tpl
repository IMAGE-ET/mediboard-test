<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Mediboard :: Système de gestion des structures de santé</title>
  <meta http-equiv="Content-Type" content="text/html;charset=<?php echo isset( $locale_char_set ) ? $locale_char_set : 'UTF-8';?>" />
  <meta name="Description" content="Mediboard: Plateforme Open Source pour les Etablissement de Santé" />
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

<table id="header" cellspacing="0"><!-- IE Hack: cellspacing should be useless --> 
  <tr>
    <td id="menubar">
      <table>
        <tr>
          {{foreach from=$affModule item=currModule}}    
          <td align='center' style='border-right: 1px solid #000'>
            <a href='?m={{$currModule.modName}}'>
              <strong>
                {{$currModule.modNameCourt}}
              </strong>
            </a>
          </td>
          {{/foreach}}
          <td id="new">
          </td>
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
{{/if}}
<table id="main">
  <tr>
    <td>
      <div id="systemMsg">
        {{$errorMessage}}
      </div>
      {{if !$dialog}}
      {{$titleBlock}}
      {{/if}}