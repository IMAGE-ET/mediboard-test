{{mb_include style=mediboard template=common nodebug=true}}

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

{{if !$dialog}}
  {{mb_include style=mediboard template=message nodebug=true}}
{{/if}}

<table id="main" class="{{if $dialog}}dialog{{/if}} {{$m}}">
  <tr>
  
{{if !$dialog}}

{{if @$app->user_prefs.MenuPosition == "left"}}
<td id="leftMenu">
  {{mb_include style="mediboard" template="logo" id="mediboard-logo" alt="MediBoard logo" width="140"}}
  
  <script type="text/javascript">
    Main.add(function(){
      $("mediboard-logo").resample();
    });
  </script>
  
  {{if !$offline}}
  {{assign var=style value="width: 130px;"}}
  {{mb_include style=mediboard template=change_group}}
  
  <!-- Welcome -->
  <div>
    <label title="{{tr}}Last connection{{/tr}} : {{$app->user_last_login|date_format:$conf.datetime}}">
      {{$app->user_first_name}} {{$app->user_last_name}}
    </label>
    <br />
    {{mb_include style=mediboard template=svnstatus}}
  </div>
  {{/if}}

  <div id="menubar" class="iconed">
    <div id="menuTools">
      <a id="toggleIcons" href="#1" onclick="Menu.toggle()" title="{{tr}}menu-toggleIcons{{/tr}}"></a>
      
      {{if $portal.help}}
      <a href="{{$portal.help}}" title="{{tr}}portal-help{{/tr}}" target="_blank">
        <img src="style/{{$uistyle}}/images/icons/help.png" alt="{{tr}}portal-help{{/tr}}" />
      </a>
      {{/if}}
      
      {{if $portal.tracker}}
      <a href="{{$portal.tracker}}" title="{{tr}}portal-tracker{{/tr}}" target="_blank">
        <img src="style/{{$uistyle}}/images/icons/modif.png" alt="{{tr}}portal-tracker{{/tr}}" />
      </a>
      {{/if}}
      
      <a href="#1" onclick="popChgPwd()" title="{{tr}}menu-changePassword{{/tr}}">
        <img src="style/{{$uistyle}}/images/icons/passwd.png" alt="{{tr}}menu-changePassword{{/tr}}" />
      </a>
      <a href="?m=mediusers&amp;a=edit_infos" title="{{tr}}menu-myInfo{{/tr}}">
        <img src="style/{{$uistyle}}/images/icons/myinfos.png" alt="{{tr}}menu-myInfo{{/tr}}" />
      </a>
      <a href="#1" onclick="Session.lock()" title="{{tr}}menu-lockSession{{/tr}}">
        <img src="style/{{$uistyle}}/images/icons/lock.png" alt="{{tr}}menu-lockSession{{/tr}}" />
      </a>
      <a href="#1" onclick="UserSwitch.popup()" title="{{tr}}menu-switchUser{{/tr}}">
        <img src="./images/icons/switch.png" alt="{{tr}}menu-switchUser{{/tr}}" />
      </a>
      <a href="?logout=-1" title="{{tr}}menu-logout{{/tr}}">
        <img src="style/{{$uistyle}}/images/icons/logout.png" alt="{{tr}}menu-logout{{/tr}}" />
      </a>
    </div>

    <hr />
    {{foreach from=$modules key=mod_name item=_module}}
      {{if $_module->mod_ui_active && $_module->_can->view}}
        <a href="?m={{$_module->mod_name}}" title="{{tr}}module-{{$_module->mod_name}}-long{{/tr}}" class="{{if $mod_name == $m}}textSelected{{else}}textNonSelected{{/if}}">
          <img src="modules/{{$_module->mod_name}}/images/icon.png" alt="Icone {{$_module->mod_name}}" />
          {{tr}}module-{{$_module->mod_name}}-court{{/tr}}
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
<table id="header">
  <tr>
    <td id="mainHeader">
      <table>
        <tr>
          <td class="logo">
            {{mb_include style="mediboard" template="logo" id="mediboard-logo" alt="MediBoard logo" width="140"}}
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
              {{mb_include module=style template=svnstatus}}
              {{mb_include module=style template=change_group}}
            {{/if}}
            <br />
            <span title="{{tr}}Last connection{{/tr}} : {{$app->user_last_login|date_format:$conf.datetime}}">
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
      {{foreach from=$modules item=_module}}
      {{if $_module->mod_ui_active && $_module->_can->view}}
      <a href="?m={{$_module->mod_name}}" class="{{if $_module->mod_name==$m}}textSelected{{else}}textNonSelected{{/if}}">
        {{tr}}module-{{$_module->mod_name}}-court{{/tr}}</a>
      {{/if}}
      {{/foreach}}
      
      {{if $portal.help}}
        <a href="{{$portal.help}}" title="{{tr}}portal-help{{/tr}}" target="_blank">
          <img src="style/{{$uistyle}}/images/icons/help.png" alt="{{tr}}portal-help{{/tr}}" />
        </a>
      {{/if}}
      
      {{if $portal.tracker}}
        <a href="{{$portal.tracker}}" title="{{tr}}portal-tracker{{/tr}}" target="_blank">
          <img src="style/{{$uistyle}}/images/icons/modif.png" alt="{{tr}}portal-tracker{{/tr}}" />
        </a>
      {{/if}}
      
      <a href="#1" onclick="popChgPwd()" title="{{tr}}menu-changePassword{{/tr}}">
        <img src="style/{{$uistyle}}/images/icons/passwd.png" alt="{{tr}}menu-changePassword{{/tr}}" />
      </a>
      <a href="?m=mediusers&amp;a=edit_infos" title="{{tr}}menu-myInfo{{/tr}}">
        <img src="style/{{$uistyle}}/images/icons/myinfos.png" alt="{{tr}}menu-myInfo{{/tr}}" />
      </a>
      <a href="#1" onclick="Session.lock()" title="{{tr}}menu-lockSession{{/tr}}">
        <img src="style/{{$uistyle}}/images/icons/lock.png" alt="{{tr}}menu-lockSession{{/tr}}" />
      </a>
      <a href="#1" onclick="UserSwitch.popup()" title="{{tr}}menu-switchUser{{/tr}}">
        <img src="./images/icons/switch.png" alt="{{tr}}menu-switchUser{{/tr}}" />
      </a>
      <a href="?logout=-1" title="{{tr}}menu-logout{{/tr}}">
        <img src="style/{{$uistyle}}/images/icons/logout.png" alt="{{tr}}menu-logout{{/tr}}" />
      </a>
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
  {{mb_include style=mediboard template=obsolete_module}}

{{if $dialog}}
<div class="dialog" id="systemMsg">
  {{$errorMessage|nl2br|smarty:nodefaults}}
</div>
{{/if}}

