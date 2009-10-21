<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{{$localeInfo.alpha2}}" lang="{{$localeInfo.alpha2}}">

<head>
  <title>
    {{$dPconfig.page_title}}
     &gt; {{tr}}module-{{$m}}-court{{/tr}}
    {{if $a || $tab}}
      &gt; {{tr}}mod-{{$m}}-tab-{{if $tab}}{{$tab}}{{else}}{{$a}}{{/if}}{{/tr}}
    {{/if}}
  </title>
  <meta http-equiv="Content-Type" content="application/xhtml+xml; charset={{$localeInfo.charset}}" />
  <meta name="Description" content="Mediboard: Plateforme Open Source pour les Etablissements de Santé" />
  <meta name="Version" content="{{$version.string}}" />
  {{$mediboardShortIcon|smarty:nodefaults}}
  {{*$mediboardCommonStyle|smarty:nodefaults*}}
  {{$mediboardStyle|smarty:nodefaults}}
  {{$mediboardScript|smarty:nodefaults}}
  
  <script type="text/javascript">
    {{if $offline}}
    var config = {{$configOffline|@json}};
    {{/if}}
    var Preferences = {{$app->user_prefs|@json}},
        User = {{if $app->_ref_user}}{{"utf8_encode"|array_map_recursive:$app->_ref_user->_basic_info|@json}}{{else}}{}{{/if}},
        sessionLocked = {{$smarty.session.locked|@json}};
    
    {{if $dialog}}
    Event.observe(document, 'keydown', closeWindowByEscape);
    {{/if}}
  </script>
</head>

<body class="{{if @$app->user_prefs.touchscreen == 1}}touchscreen{{/if}}">

<!-- Loading divs -->
<div id="waitingMsgMask" class="chargementMask" style="display: none;"></div>

<div id="waitingMsgText" class="chargementText" style="top: -1500px;"><!-- This trick is to preload the background image -->
  <div class="loading">{{tr}}Loading in progress{{/tr}}</div>
</div>

<div id="sessionLock" style="display: none;">
  {{if $app->_ref_user}}
  <h1>{{tr}}Session locked{{/tr}} - {{$app->_ref_user}}</h1>
  <form name="sessionLockForm" method="post" action="?" onsubmit="return Session.request(this)">
    <input type="hidden" name="unlock" value="unlock" />
    <input type="hidden" name="username" value="{{$app->_ref_user->_user_username}}" />
    <div>
      <label for="password">{{tr}}Password{{/tr}}</label>
      <input type="password" name="password" />
    </div>
    <div>
      <button type="submit" class="tick">{{tr}}Unlock{{/tr}}</button>
      <button type="button" class="cancel" onclick="Session.close()">{{tr}}Logout{{/tr}}</button>
    </div>
    <div class="login-message"></div>
  </form>
  {{/if}}
</div>

<div id="userSwitch" style="display: none;">
  <h1>{{tr}}User switch{{/tr}}</h1>
  <form name="userSwitchForm" method="post" action="?" onsubmit="return UserSwitch.login(this)">
    <input type="hidden" name="m" value="admin" />
    <input type="hidden" name="dosql" value="do_login_as" />
    <div style="text-align: right;">
      <label for="username">{{tr}}User{{/tr}} </label><input name="username" tabIndex="1000" type="text" class="notNull" />
      
      {{if $app->user_type != 1}}
        <br /><label for="password">{{tr}}Password{{/tr}} </label><input name="password" tabIndex="1001" type="password" />
      {{/if}}
    </div>
    <div>
      <button type="submit" class="tick">{{tr}}Switch{{/tr}}</button>
      <button type="button" class="cancel" onclick="UserSwitch.cancel()">{{tr}}Cancel{{/tr}}</button>
    </div>
    <div class="login-message"></div>
  </form>
</div>

<!-- Tooltip div used for dom clonage -->
<div id="tooltipTpl" style="display: none;">
  <table class="decoration" cellspacing="0">
    <tr>
      <td class="deco top-left" />
      <td class="deco top" />
      <td class="deco top-right" />
    </tr>
    <tr>
      <td class="deco left" />
      <td class="content"></td>
      <td class="deco right" />
    </tr>
    <tr>
      <td class="deco bottom-left" />
      <td class="deco bottom" />
      <td class="deco bottom-right" />
    </tr>
  </table>
</div>

<!-- Javascript Console -->
<div id="console" style="display:none;">
  <div class="title">
    <div class="hide" onclick="Console.hide()"></div>
    Javascript console
  </div>
</div>
