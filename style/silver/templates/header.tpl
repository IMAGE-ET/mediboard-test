{{include file="../../mediboard/templates/common.tpl"}}

{{if !$offline}}
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
              <img src="images/modules/{{$currModule.modName}}.png" alt="{{$currModule.modNameCourt}}" height="48" width="48" />
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
              <span title="{{tr}}last connection{{/tr}} : {{$AppUI->user_last_login|date_format:"%A %d %B %Y %H:%M"}}">
                {{tr}}Welcome{{/tr}} {{$AppUI->user_first_name}} {{$AppUI->user_last_name}}
              </span>
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
            <a href="{{$portal.help}}" title="{{tr}}portal-help{{/tr}}" target="_blank">{{tr}}portal-help{{/tr}}</a> |
            <a href="{{$portal.tracker}}" title="{{tr}}portal-tracker{{/tr}}" target="_blank">{{tr}}portal-tracker{{/tr}}</a> |
            <a href="#" onclick="popChgPwd();">{{tr}}menu-changePassword{{/tr}}</a> |
            <a href="?m=mediusers&amp;a=edit_infos">{{tr}}menu-myInfo{{/tr}}</a> |
            <a href="?m=admin&amp;a=edit_prefs&amp;user_id={{$AppUI->user_id}}">{{tr}}mod-admin-tab-edit_prefs{{/tr}}</a> |
            <a href="?logout=-1">{{tr}}menu-logout{{/tr}}</a>
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
{{/if}}
<table id="main" class="{{$m}}">
  <tr>
    <td>
      <div {{if $dialog}}class="dialog" {{if !$errorMessage}} style="display: none"{{/if}}{{/if}} id="systemMsg">
        {{$errorMessage|nl2br|smarty:nodefaults}}
      </div>
      {{if !$dialog && !$offline}}
      <table class='titleblock'>
        <tr>
          {{if $titleBlockData.icon}}
          <td>
            {{$titleBlockData.icon|smarty:nodefaults}}
          </td>
          {{/if}}
          <td class='titlecell'>
            {{tr}}{{$titleBlockData.name}}{{/tr}}
          </td>
        </tr>
      </table>
      {{/if}}