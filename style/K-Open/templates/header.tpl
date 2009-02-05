{{include file="../../mediboard/templates/common.tpl"}}

{{if !$dialog}}

{{foreach from=$messages item=currMsg}}
  <div style='{{if $currMsg->urgence == "urgent"}}background: #eee; color: #f00;{{else}}background: #aaa; color: #fff;{{/if}}'>
    <strong>{{$currMsg->titre}}</strong> : {{$currMsg->corps}}
  </div>
{{/foreach}}

<table id="header" cellspacing="0">
  <tr>
    <td id="mainHeader">
      <table>
        <tr>
          <td class="logo">
            <img src="./style/{{$uistyle}}/images/pictures/mbLogo.png" alt="Mediboard logo" />
          </td>
          <td width="1%">
            {{if !$offline}}
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
            <select name="g" onchange="ChangeGroup.submit();">
              {{foreach from=$Etablissements item=currEtablissement key=keyEtablissement}}
              <option value="{{$keyEtablissement}}" {{if $keyEtablissement==$g}}selected="selected"{{/if}}>
                {{$currEtablissement->_view}}
              </option>
              {{/foreach}}
            </select>
            <br />
            <span title="{{tr}}Last connection{{/tr}} : {{$app->user_last_login|date_format:$dPconfig.datetime}}">
            {{tr}}Welcome{{/tr}} {{$app->user_first_name}} {{$app->user_last_name}}
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
      | <a href="{{$portal.help}}" title="{{tr}}portal-help{{/tr}}" target="_blank">{{tr}}portal-help{{/tr}}</a> | 
      {{foreach from=$modules key=mod_name item=currModule}}
      {{if $currModule->_can->view && $currModule->mod_ui_active}}
      <a href="?m={{$currModule->mod_name}}" class="{{if $mod_name == $m}}textSelected{{else}}textNonSelected{{/if}}">
        {{tr}}module-{{$mod_name}}-court{{/tr}}
      </a> |
      {{/if}}
      {{/foreach}}
      <a href="#" onclick="popChgPwd()">{{tr}}menu-changePassword{{/tr}}</a> | 
      <a href="?m=mediusers&amp;a=edit_infos">{{tr}}menu-myInfo{{/tr}}</a> |
      <a href="?m=admin&amp;a=edit_prefs&amp;user_id={{$app->user_id}}">{{tr}}mod-admin-tab-edit_prefs{{/tr}}</a> |
      <a href="?logout=-1">{{tr}}menu-logout{{/tr}}</a>
    </td>
  </tr>
  {{/if}}
</table>
{{/if}}

<table id="main" class="{{if $dialog}}dialog{{/if}} {{$m}}">
<tr>
  <td>

<!-- System messages -->
<div id="systemMsg">
  {{$errorMessage|nl2br|smarty:nodefaults}}
</div>
  