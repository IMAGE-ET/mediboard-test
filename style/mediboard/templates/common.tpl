<!DOCTYPE html>

{{* MOTW http://msdn.microsoft.com/en-us/library/ms537628(v=vs.85).aspx *}}
{{if $allInOne}}
<!-- saved from url=(0014)about:internet --> 
{{/if}}

{{* When AIO is active, UA is not detected server side, but IE needs it (for vertical text) *}}
<!--[if IE 7]> <html lang="{{$localeInfo.alpha2}}" class="ua-msie ua-msie-7"> <![endif]-->
<!--[if IE 8]> <html lang="{{$localeInfo.alpha2}}" class="ua-msie ua-msie-8"> <![endif]-->
<!--[if IE 9]> <html lang="{{$localeInfo.alpha2}}" class="ua-msie ua-msie-9"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="{{$localeInfo.alpha2}}" class="ua-{{$browser.name}} ua-{{$browser.name}}-{{$browser.majorver}}"> <!--<![endif]-->
<head>
  <!-- Content-Type meta tags need to be the first in the page (even before title) -->
  <meta http-equiv="Content-Type" content="text/html;charset={{$localeInfo.charset}}" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" /> {{* For IE in All-in-one mode *}}
  
  <title>
    {{if !$dialog}}
      {{$conf.page_title}} 
      &mdash; {{tr}}module-{{$m}}-court{{/tr}}
      {{if $a || $tab}}
        &mdash; {{tr}}mod-{{$m}}-tab-{{if $tab}}{{$tab}}{{else}}{{$a}}{{/if}}{{/tr}}
      {{/if}}
    {{else}}
      {{tr}}mod-{{$m}}-tab-{{if $tab}}{{$tab}}{{else}}{{$a}}{{/if}}{{/tr}}
    {{/if}}
  </title>
  
  <meta name="Description" content="Mediboard: Plateforme Open Source pour les Etablissements de Santé" />
  <meta name="Version" content="{{$version.string}}" />
  
  <!-- iOS specific -->
  {{* Can't use the "apple-mobile-web-app-capable" meta tags because any hyperlink will be opened in Safari *}}
  <link rel="apple-touch-icon" href="images/icons/apple-touch-icon.png?{{$version.build}}" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
  <meta name="format-detection" content="telephone=no" />
  
  {{if $browser.name == "msie" && $app->user_id}}
    <!-- IE9 specific JumpLists -->
    <meta name="application-name" content="{{$conf.page_title}}" />
    <meta name="application-tooltip" content="{{$conf.page_title}}" />
    <meta name="msapplication-starturl" content="./" />
    
    {{foreach from=$modules key=mod_name item=currModule}} 
      {{if $currModule->_can->view && $currModule->mod_ui_active}}
        <meta name="msapplication-task" content="name={{tr}}module-{{$mod_name}}-court{{/tr}};action-uri=./?m={{$mod_name}};icon-uri=./lib/phpThumb/phpThumb.php?src=../../modules/{{$mod_name}}/images/icon.png&amp;ws=16&amp;f=ico" />
      {{/if}}
    {{/foreach}}
  {{/if}}

  {{include file="../../mediboard/templates/system_date.tpl" nodebug=true}}

  <script>
    var Preferences = {{"utf8_encode"|array_map_recursive:$app->user_prefs|@json}},
        User = {{if $app->_ref_user}}{{"utf8_encode"|array_map_recursive:$app->_ref_user->_basic_info|@json}}{{else}}{}{{/if}};

    App = {
      m: "{{$m}}",
      a: "{{$a}}",
      tab: "{{$tab}}",
      action: "{{$action}}",
      actionType: "{{$actionType}}",
      dialog: "{{$dialog}}",
      config: {
        log_js_errors: {{if $conf.log_js_errors}}true{{else}}false{{/if}},
        instance_role: {{$conf.instance_role|@json}}
      },
      readonly: "{{$conf.readonly}}" == 1 && User.id != null,
      touchDevice: /^ipad|iphone|nexus 7$/i.test("{{$browser.name}}"),
      sessionLifetime: {{"CSessionHandler::getLifeTime"|static_call:""}},
      sessionLocked: {{$smarty.session.locked|@json}}
    };

    var Mediboard = {{$version|@json}};
  </script>

  {{$mediboardShortIcon|smarty:nodefaults}}
  {{$mediboardStyle|smarty:nodefaults}}
  {{$mediboardScript|smarty:nodefaults}}

  <script>
    AideSaisie.timestamp = "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}";

    {{if $app->_ref_user}}
      //for holidays in datepicker
      Calendar.ref_pays = {{$conf.ref_pays|default:1}};   // france
      Calendar.ref_cp   = {{$cp_group|default:"00000"}};  // fake cp
    {{/if}}

    {{if $dialog}}
      Event.observe(document, 'keydown', closeWindowByEscape);
    {{/if}}
    
    {{if @$conf.weinre_debug_host && !@$smarty.get.nodebug}}
      setTimeout(function() {
        $$('head')[0].insert(DOM.script({src: 'http://{{$conf.weinre_debug_host}}/target/target-script-min.js'}));
      }, 0);
    {{/if}}
    
    {{if $allInOne}}
      {{* any ajax method > /dev/null *}}
      Class.extend(Url, {
        requestUpdate: function(){},
        requestJSON: function(){},
        periodicalUpdate: function(){}
      });
    {{/if}}
  </script>
  {{if "didacticiel"|module_active}}
    {{mb_include module="didacticiel" template="inc_permanence_didacticiel"}}
  {{/if}}
</head>

<body class="
{{if @$app->user_prefs.accessibility_dyslexic == 1}} dyslexic {{/if}}
{{if @$app->user_prefs.touchscreen == 1 || $browser.name == 'ipad' || $browser.useragent|stripos:'nexus 7' !== false}} touchscreen {{else}} desktop {{/if}}
{{if $browser.name == 'ipad'}} ipad {{/if}}">

{{if $browser.name == "msie"}}
  {{include file="../../mediboard/templates/ie.tpl" nodebug=true}}
{{/if}}

{{* if IDE is configured *}}
{{if @$conf.dPdeveloppement.ide_url || @$conf.dPdeveloppement.ide_path}}
  <iframe name="ide-launch-iframe" id="ide-launch-iframe" style="display: none;"></iframe>
{{/if}}

{{if $conf.readonly}}
<div class="big-info not-printable">
  <strong>{{tr}}Mode-readonly-title{{/tr}}</strong><br />
  {{tr}}Mode-readonly-description{{/tr}}<br />
  {{tr}}Mode-readonly-disclaimer{{/tr}}
</div>
{{/if}}

<!-- Loading divs -->
<div id="waitingMsgMask" style="display: none;"></div>

<div id="waitingMsgText" style="top: -1500px;"><!-- This trick is to preload the background image -->
  <div class="loading">{{tr}}Loading in progress{{/tr}}</div>
</div>

<div id="sessionLock" style="display: none;">
  {{if $app->_ref_user}}
  <form name="sessionLockForm" method="get" action="?" onsubmit="return Session.request(this)">
    <input type="hidden" name="unlock" value="unlock" />
    <input type="hidden" name="username" value="{{$app->_ref_user->_user_username}}" />
    <table class="main form">
      <tr>
        <th><label for="password">{{tr}}Password{{/tr}}</label></th>
        <td><input type="password" name="password" /></td>
      </tr>
      <tr>
        <th></th>
        <td>
          <button type="submit" class="unlock">{{tr}}Unlock{{/tr}}</button>
          <button type="button" class="logout" onclick="Session.close()">{{tr}}Logout{{/tr}}</button>
        </td>
      </tr>
      <tr>
        <th></th>
        <td>
          <button type="button" class="switch" onclick="Session.window.close(); UserSwitch.popup();">
            {{tr}}User switch{{/tr}}
          </button>
        </td>
      </tr>
      <tr>
        <td colspan="2" class="login-message"></td>
      </tr>
    </table>
  </form>
  {{/if}}
</div>

<div id="userSwitch" style="display: none;">
  {{if $m == "admin" && $tab == "chpwd"}}
    <div class="big-error">
      Vous ne pourrez vous substituer qu'après avoir changé votre mot de passe.
    </div>

    <div style="text-align: center;">
      <button type="button" class="logout" onclick="Session.close()">{{tr}}Logout{{/tr}}</button>
    </div>
  {{else}}
    <form name="userSwitchForm" method="post" action="?" onsubmit="return UserSwitch.login(this)">
      <input type="hidden" name="m" value="admin" />
      <input type="hidden" name="dosql" value="do_login_as" />
      <table class="main form">
        <tr>
          <th><label for="username">{{tr}}User{{/tr}}</label></th>
          <td><input name="username" tabIndex="1000" type="text" class="notNull" /></td>
        </tr>

        {{if ($app->user_type != 1) || ($conf.admin.LDAP.ldap_connection && !$conf.admin.LDAP.allow_login_as_admin)}}
          <tr>
            <th><label for="password">{{tr}}Password{{/tr}}</label></th>
            <td><input name="password" tabIndex="1001" type="password" /></td>
          </tr>
        {{/if}}

        <tr>
          <th></th>
          <td>
            <button type="submit" class="tick">{{tr}}Switch{{/tr}}</button>
          </td>
        </tr>
      </table>
      <div class="login-message"></div>
    </form>
  {{/if}}
</div>

<!-- Javascript Console -->
<div id="console" style="display: none;">
  <div class="title">
    <div class="close" onclick="Console.hide()">X</div>
    <div class="toggle" onclick="Console.toggle()">_</div>
    Javascript console
  </div>
  <div class="body"></div>
  <form name="debug-console" method="get" onsubmit="return Console.exec(this)">
    <input type="text" size="90" name="code" value="" />
    <button class="tick notext" onclick=""></button>
  </form>
</div>

<!-- Mails -->
{{if !$dialog && $app->user_id && $mails|@count}}
  <div id="usermessage_notification" class="not-printable">
    <a href="?m=messagerie&tab=vw_list_internalMessages" title="{{$mails|@count}} nouveaux messages">
      <p>
        <span>
          {{$mails|@count}}
        </span>
      </p>
    </a>
  </div>
{{/if}}