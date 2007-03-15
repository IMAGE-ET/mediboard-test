{{include file="../../mediboard/templates/common.tpl"}}

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
            <img src="./style/{{$uistyle}}/images/pictures/e-cap.jpg" alt="eCap logo" />
          </td>
          <td width="1%">
            {{if !$offline}}
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
            {{/if}}
          </td>
          <td>
            <div id="systemMsg">
              {{$errorMessage|nl2br|smarty:nodefaults}}
            </div>
          </td>
          <td class="welcome">
            {{if !$offline}}
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
            <span title="{{tr}}last connection{{/tr}} : {{$AppUI->user_last_login|date_format:"%A %d %B %Y %H:%M"}}">
            {{tr}}Welcome{{/tr}} {{$AppUI->user_first_name}} {{$AppUI->user_last_name}}
            </span>
            </form>
            {{/if}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
  {{if !$offline}}
  <tr>
    <td id="menubar">
      | {{$helpOnline|smarty:nodefaults}} | 
      {{foreach from=$affModule item=currModule}}
      <a href="?m={{$currModule.modName}}" class="{{if $currModule.modName==$m}}textSelected{{else}}textNonSelected{{/if}}">
        {{$currModule.modNameCourt}}
      </a> |
      {{/foreach}}
      <a href="#" onclick="popChgPwd()">Changez votre mot de passe</a> | 
      <a href="?m=mediusers&amp;a=edit_infos">{{tr}}My Info{{/tr}}</a> |
      <a href="?m=admin&amp;a=edit_prefs&amp;user_id={{$AppUI->user_id}}">{{tr}}Préférences{{/tr}}</a> |
      <a href="?logout=-1">{{tr}}Logout{{/tr}}</a>
    </td>
  </tr>
  {{/if}}
</table>
{{else}}
<div class="dialog" {{if !$errorMessage}} style="display: none"{{/if}} id="systemMsg">
  {{$errorMessage|nl2br|smarty:nodefaults}}
</div>
{{/if}}

<table id="main" class="{{$m}}">
<tr>
  <td>