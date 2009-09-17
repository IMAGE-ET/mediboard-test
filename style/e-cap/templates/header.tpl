{{include file="../../mediboard/templates/common.tpl"}}

{{if !$dialog && (!$app->_ref_user || !$app->_ref_user->_id)}}
<!-- No Mediuser -->
<div class="small-warning">
  {{tr}}common-warning-no-mediuser{{/tr}}<br/>
  {{tr}}common-suggest-no-mediuser{{/tr}}
</div>
{{/if}}

<script type="text/javascript">
var Menu = {
  toggle: function () {
    var oCNs = Element.classNames("menubar");
    oCNs.flip("iconed", "uniconed");
    oCNs.save("menubar", Date.year);
  },
  
  init: function() {
    var oCNs = Element.classNames("menubar");
    oCNs.load("menubar");
  }
}
</script>

<table id="main" class="{{if $dialog}}dialog{{/if}} {{$m}}">
  <tr>
  
{{if !$dialog}}

{{foreach from=$messages item=_message}}
<div style='{{if $_message->urgence == "urgent"}}background: #eee; color: #f00;{{else}}background: #aaa; color: #fff;{{/if}}'>
  <strong>{{$_message->titre}}</strong> : {{$_message->corps}}
</div>
{{/foreach}}

{{if @$app->user_prefs.MenuPosition == "left"}}
<td id="leftMenu">
  <img src="images/pictures/logo.png" width="140" />
<!--  {{thumb src="style/$uistyle/images/pictures/e-cap.jpg" w="140" f="png"}} -->
  
  {{if !$offline}}
  <!-- Changement d'établissement courant -->
  <form name="ChangeGroup" action="?" method="get">
    <input type="hidden" name="m" value="{{$m}}" />
    <select name="g" style="width: 140px;" onchange="this.form.submit();">
      {{foreach from=$Etablissements item=currEtablissement}}
      <option value="{{$currEtablissement->_id}}" {{if $currEtablissement->_id == $g}}selected="selected"{{/if}}>
        {{$currEtablissement->_view}}
      </option>
      {{/foreach}}
    </select>
  </form>
  
  <!-- Welcome -->
  <div>
    <label title="{{tr}}Last connection{{/tr}} : {{$app->user_last_login|date_format:$dPconfig.datetime}}">
      {{$app->user_first_name}} {{$app->user_last_name}}
    </label>
    <br />
    {{if $svnStatus}}
    <a href="tmp/svnlog.txt" target="_blank" title="{{$svnStatus.1|date_format:$dPconfig.datetime}} (r{{$svnStatus.0}})">
      {{tr}}Latest update{{/tr}} {{$svnStatus.relative.count}} {{tr}}{{$svnStatus.relative.unit}}{{if $svnStatus.relative.count > 1}}s{{/if}}{{/tr}}
    </a>
    {{/if}}
  </div>
  {{/if}}

  <div id="menubar" class="iconed">
    <div id="menuTools">
      <a href="{{$portal.help}}" title="{{tr}}portal-help{{/tr}}" target="_blank">
        <img src="style/{{$uistyle}}/images/icons/help.png" alt="{{tr}}portal-help{{/tr}}" />
      </a>
      <a href="{{$portal.tracker}}" title="{{tr}}portal-tracker{{/tr}}" target="_blank">
        <img src="style/{{$uistyle}}/images/icons/modif.png" alt="{{tr}}portal-tracker{{/tr}}" />
      </a>
      <a href="javascript:popChgPwd()" title="{{tr}}menu-changePassword{{/tr}}">
        <img src="style/{{$uistyle}}/images/icons/passwd.png" alt="{{tr}}menu-changePassword{{/tr}}" />
      </a>
      <a href="?m=mediusers&amp;a=edit_infos" title="{{tr}}menu-myInfo{{/tr}}">
        <img src="style/{{$uistyle}}/images/icons/myinfos.png" alt="{{tr}}menu-myInfo{{/tr}}" />
      </a>
      <a href="javascript:Session.lock()" title="{{tr}}menu-lockSession{{/tr}}">
        <img src="style/{{$uistyle}}/images/icons/lock.png" alt="{{tr}}menu-lockSession{{/tr}}" />
      </a>
      <a href="javascript:UserSwitch.popup()" title="{{tr}}menu-switchUser{{/tr}}">
        <img src="./images/icons/switch.png" alt="{{tr}}menu-switchUser{{/tr}}" />
      </a>
      <a href="?logout=-1" title="{{tr}}menu-logout{{/tr}}">
        <img src="style/{{$uistyle}}/images/icons/logout.png" alt="{{tr}}menu-logout{{/tr}}" />
      </a>
      
      <a id="toggleIcons" href="javascript:Menu.toggle()" title="{{tr}}menu-toggleIcons{{/tr}}"></a>
    </div>

    <hr />
    {{foreach from=$modules key=mod_name item=currModule}}
	    {{if $currModule->mod_ui_active && $currModule->_can->view}}
		    <a href="?m={{$currModule->mod_name}}" title="{{tr}}module-{{$currModule->mod_name}}-long{{/tr}}" class="{{if $mod_name == $m}}textSelected{{else}}textNonSelected{{/if}}">
		      <img src="modules/{{$currModule->mod_name}}/images/icon.png" alt="Icone {{$currModule->mod_name}}" />
		      {{tr}}module-{{$currModule->mod_name}}-court{{/tr}}
		    </a>
	    {{/if}}
    {{/foreach}}
  </div>
  
  <script type="text/javascript">Menu.init();</script>
  
  <!-- System messages -->
  <div id="systemMsg">
    {{$errorMessage|nl2br|smarty:nodefaults}}
  </div>
  
</td>
  
{{else}}
<td id="topMenu">
<table id="header" cellspacing="0">
  <tr>
    <td id="mainHeader">
      <table>
        <tr>
          <td class="logo">
            {{thumb src="images/pictures/mbNormal.gif" w="140" f="png"}}
            <!-- <img src="./style/{{$uistyle}}/images/pictures/e-cap.jpg" alt="eCap logo" /> -->
          </td>
          <td width="1%">
            {{if !$offline}}
            <table class="titleblock">
              <tr>
                <td>
                  <img src="./modules/{{$m}}/images/icon.png" alt="Icone {{$m}}" width="24" height="24" />
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
            {{if $svnStatus}}
            <a href="tmp/svnlog.txt" target="_blank" title="{{$svnStatus.1|date_format:$dPconfig.datetime}} (r{{$svnStatus.0}})">
              {{tr}}Latest update{{/tr}} {{$svnStatus.relative.count}} {{tr}}{{$svnStatus.relative.unit}}{{if $svnStatus.relative.count > 1}}s{{/if}}{{/tr}}
            </a>
            {{/if}}
            <form name="ChangeGroup" action="" method="get">

            <input type="hidden" name="m" value="{{$m}}" />
            <select name="g" onchange="this.form.submit();">
              {{foreach from=$Etablissements item=currEtablissement key=keyEtablissement}}
              <option value="{{$keyEtablissement}}" {{if $keyEtablissement==$g}}selected="selected"{{/if}}>
                {{$currEtablissement->_view}}
              </option>
              {{/foreach}}
            </select>
            </form>
            {{/if}}
            <br />
            <span title="{{tr}}Last connection{{/tr}} : {{$app->user_last_login|date_format:$dPconfig.datetime}}">
            {{$app->user_first_name}} {{$app->user_last_name}}
            </span>

          </td>
        </tr>
      </table>
    </td>
  </tr>
  {{if !$offline}}
  <tr>
    <td id="menubar">
      <a href="{{$portal.help}}" title="{{tr}}portal-help{{/tr}}" target="_blank">{{tr}}portal-help{{/tr}}</a>
      {{foreach from=$modules item=currModule}}
      {{if $currModule->mod_ui_active && $currModule->_can->view}}
      <a href="?m={{$currModule->mod_name}}" class="{{if $currModule->mod_name==$m}}textSelected{{else}}textNonSelected{{/if}}">
        {{tr}}module-{{$currModule->mod_name}}-court{{/tr}}</a>
      {{/if}}
      {{/foreach}}
      <a href="javascript:popChgPwd()">{{tr}}menu-changePassword{{/tr}}</a>
      <a href="?m=mediusers&amp;a=edit_infos">{{tr}}menu-myInfo{{/tr}}</a>
      <a href="?m=admin&amp;a=edit_prefs&amp;user_id={{$app->user_id}}">{{tr}}mod-admin-tab-edit_prefs{{/tr}}</a>
      <a href="javascript:UserSwitch.popup()">{{tr}}menu-switchUser{{/tr}}</a>
      <a href="?logout=-1">{{tr}}menu-logout{{/tr}}</a>
    </td>
  </tr>
  {{/if}}
</table>

</td>
</tr>
<tr>
{{/if}}
{{/if}}

<td id="mainPane">

{{if $dialog}}
<div class="dialog" id="systemMsg">
  {{$errorMessage|nl2br|smarty:nodefaults}}
</div>
{{/if}}

