{{include file="../../mediboard/templates/common.tpl"}}

{{if !$offline}}
{{if !$dialog}}

{{foreach from=$messages item=currMsg}}
  <div style='background: #aaa; color: #fff;'><strong>{{$currMsg->titre}}</strong> : {{$currMsg->corps}}</div>
{{/foreach}}

<table id="header" cellspacing="0">
  <tr>
    <td id="mainHeader">
      <table>
        <tr>
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
              {{$errorMessage|nl2br|smarty:nodefaults}}
            </div>
          </td>
          <td class="welcome">
            <form name="ChangeGroup" action="" method="get">
            <input type="hidden" name="m" value="{{$m}}" />
            <span title="{{tr}}last connection{{/tr}} : {{$AppUI->user_last_login|date_format:"%A %d %B %Y %H:%M"}}">
            {{tr}}Welcome{{/tr}} {{$AppUI->user_first_name}} {{$AppUI->user_last_name}}
            </span>
            -
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
    </td>
  </tr>
  <tr>
    <td id="menubar">
      | <a href="{{$portal.help}}" title="{{tr}}portal-help{{/tr}}" target="_blank">{{tr}}portal-help{{/tr}}</a>
      {{foreach from=$affModule item=currModule}}
      <a href="?m={{$currModule.modName}}" class="{{if $currModule.modName==$m}}textSelected{{else}}textNonSelected{{/if}}">
        {{$currModule.modNameCourt}}
      </a> |
      {{/foreach}}
      <a href='#' onclick='popChgPwd();return false'>{{tr}}menu-changePassword{{/tr}}</a> | 
      <a href="?m=mediusers&amp;a=edit_infos">{{tr}}menu-myInfo{{/tr}}</a> | 
      <a href="?m=admin&amp;a=edit_prefs&amp;user_id={{$AppUI->user_id}}">{{tr}}mod-admin-tab-edit_prefs{{/tr}}</a> |
      <a href="?logout=-1">{{tr}}menu-logout{{/tr}}</a> |
    </td>
  </tr>
</table>
{{/if}}
{{else}}
<div id="systemMsg">
  {{$errorMessage|nl2br|smarty:nodefaults}}
</div>
{{/if}}
<table id="main" class="{{$m}}">
<tr>
  <td>