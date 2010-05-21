<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{{$localeInfo.alpha2}}" lang="{{$localeInfo.alpha2}}">

<head>
  <!-- Content-Type meta tags need to be the first in the page (even before title) -->
  <meta http-equiv="Content-Type" content="text/html;charset={{$localeInfo.charset}}" />
  
  <title>
    {{$dPconfig.page_title}} 
    &mdash; {{tr}}module-{{$m}}-court{{/tr}}
    {{if $a || $tab}}
      &mdash; {{tr}}mod-{{$m}}-tab-{{if $tab}}{{$tab}}{{else}}{{$a}}{{/if}}{{/tr}}
    {{/if}}
  </title>
  
  <meta name="Description" content="Mediboard: Plateforme Open Source pour les Etablissements de Santé" />
  <meta name="Version" content="{{$version.string}}" />
  <meta http-equiv="X-UA-Compatible" content="IE=100" /> <!-- IE8+ mode -->
  
  <script type="text/javascript">
    // This needs to be at the very beginning of the page
    __loadStart = (new Date).getTime();
  </script>
  
  {{$mediboardShortIcon|smarty:nodefaults}}
  {{if $uistyle != 'mediboard'}}
    {{$mediboardCommonStyle|smarty:nodefaults}}
  {{/if}}
  {{$mediboardStyle|smarty:nodefaults}}
  {{$mediboardScript|smarty:nodefaults}}
  
  <!--[if lte IE 8]>
  <link rel="stylesheet" type="text/css" href="style/mediboard/ie.css?build={{$version.build}}" media="all" />
  <![endif]-->
  
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
    
    /*if (Preferences.INFOSYTEM == 0) {
      var disableBackButton = function(back){
        dsHistory.addFunction(disableBackButton);
      }
      
      // This need to be set here, not in a JS file, or it won't work.
      window.onload = function(){
        // This need to be called twice
        dsHistory.addFunction(disableBackButton);
        dsHistory.addFunction(disableBackButton);
      }
    }*/
  </script>
</head>

<body class="{{if @$app->user_prefs.touchscreen == 1}}touchscreen{{/if}}">

{{if $browser.name == "msie"}}
  {{include file="../../mediboard/templates/ie.tpl" nodebug=true}}
{{/if}}

<!-- Loading divs -->
<div id="waitingMsgMask" style="display: none;"></div>

<div id="waitingMsgText" style="top: -1500px;"><!-- This trick is to preload the background image -->
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

<!-- Javascript Console -->
<div id="console" style="display: none;">
  <div class="title">
    <div class="close" onclick="Console.hide()">X</div>
    Javascript console
  </div>
  <div class="body"></div>
</div>

<!-- Mails -->
{{if !$dialog && @count($mails)}}
<div class="small-mail" onmouseover="ObjectTooltip.createDOM(this, 'mail-details');">
	<label>
		{{tr}}CMbMail{{/tr}} :
		
		{{if array_key_exists("received", $mails)}}
			{{$mails.received|@count}} {{tr}}CMbMail._to_state.received{{/tr}}
		{{/if}}
		
		{{if count($mails) == 2}}&ndash;{{/if}}
		
		{{if array_key_exists("starred", $mails)}}
			{{$mails.starred|@count}} {{tr}}CMbMail._to_state.starred{{/tr}}
		{{/if}}
	</label>
</div>

<div id="mail-details" style="display: none;">
	<table class="tbl">
	{{foreach from=$mails key=to_state item=_mails}}
		<tr>
		  <th class="category" colspan="10">{{tr}}CMbMail._to_state.{{$to_state}}{{/tr}}</th>
		</tr>
		{{foreach from=$_mails item=_mail}}
			<tr>
			  <td>
			  	<div class="mediuser" style="border-color: #{{$_mail->_ref_user_from->_ref_function->color}};">{{$_mail->_ref_user_from}}</div>
			  </td>
			  <td>
			  	<a href="#Read-{{$_mail->_guid}}" onclick="MbMail.edit({{$_mail->_id}})">{{$_mail->subject}}</a>
			  </td>
			  <td>
	        <label title="{{mb_value object=$_mail field=date_sent}}">
	          {{mb_value object=$_mail field=date_sent format=relative}}
	        </label>
			  </td>
			</tr>
		{{/foreach}}
	{{/foreach}}
	</table>
</div>
{{/if}}