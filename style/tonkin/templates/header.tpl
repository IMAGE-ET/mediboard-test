{{include file="../../mediboard/templates/common.tpl"}}

{{if !$dialog}}

{{if !$offline}}
{{foreach from=$messages item=currMsg}}
  <div style='{{if $currMsg->urgence == "urgent"}}background: #eee; color: #f00;{{else}}background: #aaa; color: #fff;{{/if}}'>
    <strong>{{$currMsg->titre}}</strong> : {{$currMsg->corps}}
  </div>
{{/foreach}}
{{/if}}

<table id="header" cellspacing="0">
  <tr>
    <td id="mainHeader">
      <table>
        <tr>
          <td rowspan="3" class="logo">
            <img src="./style/{{$uistyle}}/images/pictures/tonkin.gif" alt="Groupe Tonkin" />
          </td>
          <th width="1%">
            {{if !$offline}}
            <table class='titleblock'>
              <tr>
                <td>
                  <img src="./images/modules/{{$m}}.png" alt="Icone {{$m}}" height="24" width="24" />
                </td>
                <td class='titlecell'>
                  {{tr}}module-{{$m}}-long{{/tr}}
                </td>
              </tr>
            </table>
            {{/if}}
          </th>
          <td width="100%">
            <div id="systemMsg">
              {{$errorMessage|nl2br|smarty:nodefaults}}
            </div>
          </td>
        </tr>
        <tr>
          <td colspan="2" id="menubar1">
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
            <a href="{{$portal.help}}" title="{{tr}}portal-help{{/tr}}" target="_blank">{{tr}}portal-help{{/tr}}</a> |
            <a href="{{$portal.tracker}}" title="{{tr}}portal-tracker{{/tr}}" target="_blank">{{tr}}portal-tracker{{/tr}}</a> |
            <a href="#" onclick="popChgPwd();">{{tr}}menu-changePassword{{/tr}}</a> |
            <a href="?m=mediusers&amp;a=edit_infos">{{tr}}menu-myInfo{{/tr}}</a> |
            <a href="?m=admin&amp;a=edit_prefs&amp;user_id={{$app->user_id}}">{{tr}}mod-admin-tab-edit_prefs{{/tr}}</a> |
            <a href="?logout=-1">{{tr}}menu-logout{{/tr}}</a> |
            </form>
            {{/if}}
          </td>
        </tr>
        <tr>
          <td colspan="2" id="menubar2">
            {{if !$offline}}
            {{foreach from=$affModule item=currModule}}    
            <a href="?m={{$currModule.modName}}" class="{{if $currModule.modName==$m}}textSelected{{else}}textNonSelected{{/if}}">
              {{$currModule.modNameCourt}}
            </a> |
            {{/foreach}}
            {{/if}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td id="menubar"></td>
  </tr>
</table>
{{else}}
<div id="systemMsg" style="display: block;">
  {{$errorMessage|nl2br|smarty:nodefaults}}
</div>
{{/if}}
<table id="main" class="{{$m}}">
  <tr>
    <td>