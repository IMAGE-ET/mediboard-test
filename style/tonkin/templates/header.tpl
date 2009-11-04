{{include file="../../mediboard/templates/common.tpl" nodebug=true}}

{{if !$dialog}}

{{if !$offline}}
{{include file="../../mediboard/templates/message.tpl"}}
{{/if}}

<table id="header" cellspacing="0">
  <tr>
    <td id="mainHeader">
      <table>
        <tr>
          <td rowspan="3" class="logo">
            {{thumb src="images/pictures/logo.png" w="140" f="png"}}
            <!-- <img src="./style/{{$uistyle}}/images/pictures/tonkin.gif" alt="Groupe Tonkin" />-->
          </td>
          <th width="1%">
            {{if !$offline}}
            <table class='titleblock'>
              <tr>
                <td>
                  <img src="./modules/{{$m}}/images/icon.png" alt="Icone {{$m}}" height="24" width="24" />
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
					    {{mb_include module=mediboard template=svnstatus}}    
            <select name="g" onchange="this.form.submit();">
              {{foreach from=$Etablissements item=currEtablissement key=keyEtablissement}}
              <option value="{{$keyEtablissement}}" {{if $keyEtablissement==$g}}selected="selected"{{/if}}>
                {{$currEtablissement->_view}}
              </option>
              {{/foreach}}
            </select>
            <a href="{{$portal.help}}" target="_blank">{{tr}}portal-help{{/tr}}</a> |
            <a href="{{$portal.tracker}}" target="_blank">{{tr}}portal-tracker{{/tr}}</a> |
            <a href="javascript:popChgPwd()">{{tr}}menu-changePassword{{/tr}}</a> |
            <a href="?m=mediusers&amp;a=edit_infos">{{tr}}menu-myInfo{{/tr}}</a> |
            <a href="javascript:Session.lock()">{{tr}}menu-lockSession{{/tr}}</a> | 
            <a href="javascript:UserSwitch.popup()">{{tr}}menu-switchUser{{/tr}}</a> | 
            <a href="?logout=-1">{{tr}}menu-logout{{/tr}}</a> |
            </form>
            {{/if}}
          </td>
        </tr>
        <tr>
          <td colspan="2" id="menubar2">
            {{if !$offline}}
	          {{foreach from=$modules key=mod_name item=currModule}}
	          {{if $currModule->_can->view && $currModule->mod_ui_active}}
            <a href="?m={{$mod_name}}" class="{{if $mod_name==$m}}textSelected{{else}}textNonSelected{{/if}}">
              {{tr}}module-{{$mod_name}}-court{{/tr}}
            </a> |
            {{/if}}
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