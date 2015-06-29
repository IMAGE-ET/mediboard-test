{{mb_include style=mediboard template=common nodebug=true}}

<script>
  OnSearch = function(input) {
    if (input.value == "") {
      // Click on the clearing button
      filterModule(input, 'li', 'nav');
    }
  }
</script>

<table style="width: 100%;">
  <tr>
    {{if !$offline && !$dialog}}
      <td style="vertical-align: top; width: 1%; padding-right: 0; text-align: center" id="leftMenu">

        {{mb_include style="mediboard" template="logo" id="mediboard-logo" alt="MediBoard logo" width="140"}}

        <div id="user">
          <div class="welcome" title="{{tr}}Last connection{{/tr}} : {{$app->user_last_login|date_format:$conf.datetime}}">
            {{$app->user_first_name}} {{$app->user_last_name}}
          </div>

          {{mb_include style=mediboard template=svnstatus}}

          <div class="menu">
            {{mb_include style=mediboard template=inc_help}}
            {{if $portal.tracker}}
              <a href="{{$portal.tracker}}" title="{{tr}}portal-tracker{{/tr}}" target="_blank"><img src="style/{{$uistyle}}/images/icons/modif.png" alt="{{tr}}portal-tracker{{/tr}}" /></a>
            {{/if}}

            <a href="#1" onclick="popChgPwd()" title="{{tr}}menu-changePassword{{/tr}}"><img src="style/{{$uistyle}}/images/icons/passwd.png" alt="{{tr}}menu-changePassword{{/tr}}" /></a>
            <a href="?m=mediusers&amp;a=edit_infos" title="{{tr}}menu-myInfo{{/tr}}"><img src="style/{{$uistyle}}/images/icons/myinfos.png" alt="{{tr}}menu-myInfo{{/tr}}" /></a>
            <a href="#1" onclick="UserSwitch.popup()" title="{{tr}}menu-switchUser{{/tr}}"><img src="images/icons/switch.png" alt="{{tr}}menu-switchUser{{/tr}}" /></a>
            <a href="#1" onclick="Session.lock()" title="{{tr}}menu-lockSession{{/tr}}"><img src="style/{{$uistyle}}/images/icons/lock.png" alt="{{tr}}menu-lockSession{{/tr}}" /></a>
            <a href="?logout=-1" title="{{tr}}menu-logout{{/tr}}"><img src="style/{{$uistyle}}/images/icons/logout.png" alt="{{tr}}menu-logout{{/tr}}" /></a>
          </div>

          {{assign var=style value="max-width: 140px;"}}
          {{mb_include style=mediboard template=change_group}}

          {{assign var=modules_count value=0}}
          {{foreach from=$modules key=mod_name item=currModule}}
            {{if $currModule->mod_ui_active && $currModule->_can->view}}
              {{assign var=modules_count value=$modules_count+1}}
            {{/if}}
          {{/foreach}}

          {{if $modules_count > 10}}
            <fieldset style="margin-bottom: 2px;">
              <input type="search" id="module_search" placeholder="Recherche de module..." style="width: 135px;"
                     title="{{tr}}Press Alt+A to get the focus{{/tr}}"
                     onkeyup="filterModule(this, 'li', 'nav')" onsearch="OnSearch(this);" />
            </fieldset>
          {{/if}}

          {{foreach from=$placeholders item=placeholder}}
            <hr />
            <div class="minitoolbar">
              {{mb_include module=$placeholder->module template=$placeholder->minitoolbar}}
            </div>
          {{/foreach}}
        </div>

        <ul id="nav">
           {{foreach from=$modules key=mod_name item=currModule}}
           {{if $currModule->mod_ui_active && $currModule->_can->view}}
          <li {{if $mod_name==$m}}class="selected"{{/if}}>
          <a href="?m={{$mod_name}}">
            <img src="./modules/{{$mod_name}}/images/icon.png" alt="{{tr}}module-{{$mod_name}}-court{{/tr}}" />
            {{tr}}module-{{$mod_name}}-court{{/tr}}
          </a>
          </li>
          {{/if}}
          {{/foreach}}
        </ul>
      </td>

      {{*<td class="separator" style="background-color: #FFBA8C !important; color: black !important" onclick="MbObject.toggleColumn(this, $(this).previous())"></td>*}}
    {{/if}}
    <td id="main" class="{{if $dialog}}dialog{{/if}} {{$m}}">
      {{mb_include style=mediboard template=obsolete_module}}
      {{mb_include style=mediboard template=message nodebug=true}}
      
      <div id="systemMsg">
        {{$errorMessage|nl2br|smarty:nodefaults|nl2br}}
      </div>
      
      {{if !$dialog && !$offline}}
      <div class="title">
        <img src="./modules/{{$m}}/images/icon.png" alt="Icone {{$m}}" height="24" width="24" />
        <h1>{{tr}}module-{{$m}}-long{{/tr}}</h1>
      </div>
      {{/if}}
