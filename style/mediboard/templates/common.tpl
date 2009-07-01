<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
  <title>
    {{$dPconfig.page_title}} 
    &mdash; {{tr}}module-{{$m}}-court{{/tr}}
  </title>
  <meta http-equiv="Content-Type" content="text/html;charset={{$localeCharSet}}" />
  <meta name="Description" content="Mediboard: Plateforme Open Source pour les Etablissements de Santé" />
  <meta name="Version" content="{{$version.string}}" />
  {{$mediboardShortIcon|smarty:nodefaults}}
  {{if $uistyle != 'mediboard'}}
    {{$mediboardCommonStyle|smarty:nodefaults}}
  {{/if}}
  {{$mediboardStyle|smarty:nodefaults}}
  {{$mediboardScript|smarty:nodefaults}}
  
  <!--[if lt IE 8]>
  <link rel="stylesheet" type="text/css" href="style/mediboard/ie.css?build={{$version.build}}" media="all" />
  <![endif]-->
  
  <script type="text/javascript">
    {{if $offline}}
    var config = {{$configOffline|@json}};
    {{/if}}
    var Preferences = {{$app->user_prefs|@json}},
        userId = parseInt({{$app->user_id|@json}}),
        sessionLocked = {{$smarty.session.locked|@json}};
    
    {{if $dialog}}
    Event.observe(document, 'keydown', closeWindowByEscape);
    {{/if}}
  </script>
</head>

<body class="{{if @$app->user_prefs.touchscreen == 1}}touchscreen{{/if}}">

{{if $browser.name == "msie"}}
  {{include file="../../mediboard/templates/ie.tpl" nodebug=true}}
{{/if}}

<!-- Loading divs -->
<div id="waitingMsgMask" class="chargementMask" style="display: none;"></div>

<div id="waitingMsgText" class="chargementText" style="top: -1500px;"><!-- This trick is to preload the background image -->
  <div class="loading">Chargement en cours</div>
</div>

<div id="sessionLock" style="display: none;">
  {{if $app->_ref_user}}
  <h1>Session verrouillée - {{$app->_ref_user}}</h1>
  <form name="sessionLockForm" method="post" action="?" onsubmit="return Session.request(this)">
    <input type="hidden" name="unlock" value="unlock" />
    <input type="hidden" name="username" value="{{$app->_ref_user->_user_username}}" />
    <div>
      <label for="password">Mote de passe </label>
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
  <h1>Changement d'utilisateur</h1>
  <form name="userSwitchForm" method="post" action="?" onsubmit="return UserSwitch.login(this)">
    <input type="hidden" name="m" value="admin" />
    <input type="hidden" name="dosql" value="do_login_as" />
    <div style="text-align: right;">
      <label for="username">Utilisateur </label><input name="username" tabIndex="1000" type="text" class="notNull" /><br />
      <label for="password">Mot de passe </label><input name="password" tabIndex="1001" type="password" />
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

<div id="console" style="display: none">
  <div id="console-title">
    <div id="console-hide" onclick="Console.hide()"></div>
    Javascript console
  </div>
</div>

<!-- Up button -->
<div id="goUp" title="Retour en haut de la page" onclick="document.documentElement.scrollTop = 0;"></div>

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