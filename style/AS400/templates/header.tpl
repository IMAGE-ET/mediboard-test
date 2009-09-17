{{include file="../../mediboard/templates/common.tpl"}}

{{if !$offline && !$dialog}}

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
          <td width="1%">
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
          </td>
          <td class="welcome">
            <form name="ChangeGroup" action="" method="get">
              <input type="hidden" name="m" value="{{$m}}" />
              <span title="{{tr}}Last connection{{/tr}} : {{$app->user_last_login|date_format:$dPconfig.datetime}}">
              {{$app->user_first_name}} {{$app->user_last_name}}
              </span>
              <br />
              {{if $svnStatus}}
              <a href="tmp/svnlog.txt" target="_blank" title="{{$svnStatus.1|date_format:$dPconfig.datetime}} (r{{$svnStatus.0}})">
                {{tr}}Latest update{{/tr}} {{$svnStatus.relative.count}} {{tr}}{{$svnStatus.relative.unit}}{{if $svnStatus.relative.count > 1}}s{{/if}}{{/tr}}
              </a>
              {{/if}}
              -
              <select name="g" onchange="this.form.submit();">
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
	    {{foreach from=$modules key=mod_name item=currModule}}
	    {{if $currModule->_can->view && $currModule->mod_ui_active}}
      <a href="?m={{$mod_name}}" class="{{if $mod_name==$m}}textSelected{{else}}textNonSelected{{/if}}">
        {{tr}}module-{{$mod_name}}-court{{/tr}}
      </a> |
      {{/if}}
      {{/foreach}}
      <a href="javascript:popChgPwd()">{{tr}}menu-changePassword{{/tr}}</a> | 
      <a href="?m=mediusers&amp;a=edit_infos">{{tr}}menu-myInfo{{/tr}}</a> | 
      <a href="javascript:UserSwitch.popup()">{{tr}}menu-switchUser{{/tr}}</a> | 
      <a href="javascript:Session.lock()">{{tr}}menu-lockSession{{/tr}}</a> |
      <a href="?logout=-1">{{tr}}menu-logout{{/tr}}</a> |
    </td>
  </tr>
</table>
{{/if}}

<div id="systemMsg">
  {{$errorMessage|nl2br|smarty:nodefaults}}
</div>

<table id="main" class="{{if $dialog}}dialog{{/if}} {{$m}}">

<tr>
  <td>