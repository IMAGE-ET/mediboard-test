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
  {{$mediboardCommonStyle|smarty:nodefaults}}
  {{$mediboardStyle|smarty:nodefaults}}
  {{$mediboardScript|smarty:nodefaults}}
  
  <!--[if IE]>
  <link rel="stylesheet" type="text/css" href="style/mediboard/ie.css?build={{$version.build}}" media="all" />
  <![endif]-->
  
  <script type="text/javascript">
    {{if $offline}}
    var config = {{$configOffline|@json}};
    {{/if}}
    var Preferences = {{$app->user_prefs|@json}},
        userId = parseInt({{$app->user_id|@json}});
    
    {{if $dialog}}
    Event.observe(document, 'keydown', closeWindowByEscape);
    {{/if}}
  </script>
  
  {{if 0 && $app->user_id}}
  <script type="text/javascript" src="?m=system&amp;a=js_locales&amp;suppressHeaders=1&amp;cache=1&amp;build={{$version.build}}"></script>
  {{/if}}
</head>

<body>

<!--[if lte IE 6]>
<div style="background-color: #ffc; padding: 0.5em; border-bottom: 1px solid #333; font-size: 1.2em;"><img src="images/icons/warning.png" />Votre navigateur web est trop ancien, Mediboard ne peut pas fonctionner correctement, <a href="http://mediboard.org/public/Firefox" target="_blank">cliquez ici</a> pour installer Firefox et profiter d'une meilleure expérience.</div>
<![endif]-->

<!--[if IE 7]>
<script type="text/javascript">
var cookiejar = new CookieJar();
Main.add(function () {
  if (cookiejar.get('IE7WarningClosed') != 'closed') {
    $('ie7warning').show();
  }
});
</script>
<div id="ie7warning" style="background-color: #ffc; border-bottom: 1px solid #333; padding: 0.3em; height: 1.4em; display: none;">
  <a href="#1" style="float: right;" onclick="$('ie7warning').hide(); cookiejar.put('IE7WarningClosed', 'closed');">Fermer</a>
  <img src="images/icons/warning.png" style="float: left;"/>
  <span style="margin: 0.2em;">Pour un meilleur confort d'utilisation, nous vous conseillons d'utiliser le navigateur Firefox. <a href="http://mediboard.org/public/Firefox" target="_blank" style="font-weight: bold; text-decoration: underline;">Cliquez ici</a> pour l'installer.</span>
</div>
<![endif]-->

<!-- Loading divs -->
<div id="waitingMsgMask" class="chargementMask" style="display: none;"></div>
<div id="waitingMsgText" class="chargementText" style="top: -1500px;"><!-- This trick is to preload the background image -->
  <div class="loading">Chargement en cours</div>
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

<div id="console" style="display:none">
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