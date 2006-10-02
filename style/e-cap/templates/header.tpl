{{if !$includeFooter}}
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
</head>

<body onload="main()">

<script type="text/javascript">
function popChgPwd() {
  var url = new Url();
  url.setModuleAction("admin", "chpwd");
  url.popup(350, 220, "password");
}
</script>

{{if !$dialog}}


{{foreach from=$messages item=currMsg}}
  <div style='background: #aaa; color: #fff;'><strong>{{$currMsg->titre}}</strong> : {{$currMsg->corps}}</div>
{{/foreach}}

<table id="header" cellspacing="0">
  <tr>
    <td id="mainHeader">
      <table>
        <tr>
          <td class="logo">
            <img src="./style/{{$uistyle}}/images/e-cap.jpg" alt="eCap logo" />
          </td>
          <td width="1%">
            <table class="titleblock">
              <tr>
                {{if $titleBlockData.icon}}
                <td>
                  {{$titleBlockData.icon|smarty:nodefaults}}
                </td>
                {{/if}}
                <td class="titlecell">
                  {{tr}}{{$titleBlockData.name}}{{/tr}}
                </td>
              </tr>
            </table>
          </td>
          <td>
            <div id="systemMsg">
              {{$errorMessage|smarty:nodefaults}}
            </div>
          </td>
          <td class="welcome">
            <form name="ChangeGroup" action="" method="get">
            <input type="hidden" name="m" value="{{$m}}" />
            CAPIO Santé -
            <select name="g" onchange="ChangeGroup.submit();">
              {{foreach from=$Etablissements item=currEtablissement key=keyEtablissement}}
              <option value="{{$keyEtablissement}}" {{if $keyEtablissement==$g}}selected="selected"{{/if}}>
                {{$currEtablissement->_view}}
              </option>
              {{/foreach}}
            </select>
            <br />
            {{tr}}Welcome{{/tr}} {{$AppUI->user_first_name}} {{$AppUI->user_last_name}}
            </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td id="menubar">
      | {{$helpOnline|smarty:nodefaults}} | 
      {{foreach from=$affModule item=currModule}}
      <a href="?m={{$currModule.modName}}" class="{{if $currModule.modName==$m}}textSelected{{else}}textNonSelected{{/if}}">
        {{$currModule.modNameCourt}}
      </a> |
      {{/foreach}}
      <a href="javascript:popChgPwd()">Changez votre mot de passe</a> | 
      <a href="?m=admin&amp;a=edit_prefs&amp;user_id={{$AppUI->user_id}}">{{tr}}Préférences{{/tr}}</a> |
      <a href="?logout=-1">{{tr}}Logout{{/tr}}</a>
    </td>
  </tr>
</table>
{{/if}}
<table id="main" class="{{$m}}">
<tr>
  <td>
{{else}}
  {{include file="../../mediboard/templates/footer.tpl"}}
{{/if}}