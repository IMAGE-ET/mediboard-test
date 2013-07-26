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
  <meta http-equiv="X-UA-Compatible" content="IE={{if $conf.browser_enable_ie9 == 0}}8{{elseif $conf.browser_enable_ie9 == 1}}9{{else}}edge{{/if}}" /> {{* For IE in All-in-one mode *}}
  
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

  <script type="text/javascript">
    {{if @$conf.system_date}}
      (function(){
        // Ne fonctionne pas sous IE8
        if (document.documentMode == 8) {
          alert("La fonctionnalité 'Date système' ne fonctionne que sur des navigateurs récents, vous utilisez Internet Explorer 8, veuillez le mettre à jour.");
          return;
        }

        var bind = Function.prototype.bind;
        var unbind = bind.bind(bind);

        function instantiate(constructor, args) {
          return new (unbind(constructor, null).apply(null, args));
        }

        window.DateOrig = Date;

        var systemDate = "{{$conf.system_date}}".match(/^(\d{4})-(\d{2})-(\d{2})/);
        DateOrig.systemDate = [
          parseInt(systemDate[1], 10),
          parseInt(systemDate[2], 10),
          parseInt(systemDate[3], 10)
        ];

        window.Date = function () {
          var date = instantiate(DateOrig, arguments);

          if (arguments.length == 0) {
            date.setFullYear(DateOrig.systemDate[0]);
            date.setMonth(DateOrig.systemDate[1]);
            date.setDate(DateOrig.systemDate[2]);
          }

          return date;
        };

        Date.prototype = DateOrig.prototype;
      })();
    {{/if}}

    // This needs to be at the very beginning of the page
    __loadStart = (new Date).getTime();

    {{if $offline}}
    var config = {{$configOffline|@json}};
    {{/if}}

    var Preferences = {{"utf8_encode"|array_map_recursive:$app->user_prefs|@json}},
        User = {{if $app->_ref_user}}{{"utf8_encode"|array_map_recursive:$app->_ref_user->_basic_info|@json}}{{else}}{}{{/if}},
        sessionLocked = {{$smarty.session.locked|@json}},
        App = {
          m: "{{$m}}",
          a: "{{$a}}",
          tab: "{{$tab}}",
          action: "{{$action}}",
          actionType: "{{$actionType}}",
          dialog: "{{$dialog}}",
          config: {
            log_js_errors: {{if $conf.log_js_errors}}true{{else}}false{{/if}}
          },
          readonly: "{{$conf.readonly}}" == 1 && User.id != null,
          touchDevice: /^ipad|iphone|nexus 7$/i.test("{{$browser.name}}")
        };

    var Mediboard = {{$version|@json}};
  </script>

  {{$mediboardShortIcon|smarty:nodefaults}}
  {{$mediboardStyle|smarty:nodefaults}}
  
  <!--[if lte IE 8]>
  <link rel="stylesheet" type="text/css" href="style/mediboard/ie.css?build={{$version.build}}" media="all" />
  <![endif]-->

  {{$mediboardScript|smarty:nodefaults}}

  <script type="text/javascript">
    AideSaisie.timestamp = "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}";

    //for holidays in datepicker
    Calendar.ref_pays = {{$country}};
    {{if $cp_group}}
      Calendar.ref_cp   = {{$cp_group}};
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

<body class="{{if @$app->user_prefs.accessibility_dyslexic == 1}} dyslexic {{/if}} {{if @$app->user_prefs.touchscreen == 1 || $browser.name == 'ipad' || $browser.useragent|stripos:'nexus 7' !== false}} touchscreen {{else}} desktop {{/if}} {{if $browser.name == 'ipad'}} ipad {{/if}}" {{if $app->touch_device}}style="margin-bottom:250px"{{/if}}>

{{if $browser.name == "msie"}}
  {{include file="../../mediboard/templates/ie.tpl" nodebug=true}}
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
          <button type="submit" class="tick">{{tr}}Unlock{{/tr}}</button>
          <button type="button" class="cancel" onclick="Session.close()">{{tr}}Logout{{/tr}}</button>
        </td>
      </tr>
      <tr>
        <th></th>
        <td>
          <button type="button" class="switch" onclick="Session.window.close(); $('main').show(); UserSwitch.popup();">
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
{{if !$dialog && @count($mails)}}
<div class="small-mail not-printable" onmouseover="ObjectTooltip.createDOM(this, 'mail-details');">
    {{tr}}CUserMessage{{/tr}} :
    
    {{if array_key_exists("received", $mails)}}
      {{$mails.received|@count}} {{tr}}CUserMessage._to_state.received{{/tr}}
    {{/if}}
    
    {{if count($mails) == 2}}&ndash;{{/if}}
    
    {{if array_key_exists("starred", $mails)}}
      {{$mails.starred|@count}} {{tr}}CUserMessage._to_state.starred{{/tr}}
    {{/if}}
</div>

<div id="mail-details" class="not-printable" style="display: none;">
  <table class="tbl">
  {{foreach from=$mails key=to_state item=_mails}}
    <tr>
      <th class="category" colspan="10">{{tr}}CUserMessage._to_state.{{$to_state}}{{/tr}}</th>
    </tr>
    {{foreach from=$_mails item=_mail}}
      <tr>
        <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_mail->_ref_user_from}}</td>
        <td>{{$_mail->subject}}</td>
        <td>
          <label title="{{mb_value object=$_mail field=date_sent}}">
            {{mb_value object=$_mail field=date_sent format=relative}}
          </label>
        </td>
        <td>
          <a href="#Read-{{$_mail->_guid}}" onclick="UserMessage.edit({{$_mail->_id}})">{{tr}}CUserMessage.read{{/tr}}</a>
        </td>
      </tr>
    {{/foreach}}
  {{/foreach}}
  </table>
</div>
{{/if}}