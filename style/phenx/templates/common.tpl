<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
  <title>
    {{$dPconfig.page_title}} &gt; 
    {{tr}}module-{{$m}}-court{{/tr}}
    {{if ($a || $tab) && $tab != '1'}}
      &gt; {{tr}}mod-{{$m}}-tab-{{if $tab}}{{$tab}}{{else}}{{$a}}{{/if}}{{/tr}}
    {{/if}}
  </title>
  <meta http-equiv="Content-Type" content="application/xhtml+xml; charset={{$localeCharSet}}" />
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
        userId = parseInt({{$app->user_id}});
    
    {{if $dialog}}
    Event.observe(document, 'keydown', closeWindowByEscape);
    {{/if}}
  </script>
</head>

<body class="{{if @$app->user_prefs.touchscreen == 1}}touchscreen{{/if}}">

<!-- Loading divs -->

<div id="waitingMsgMask" class="chargementMask" style="display: none;"></div>
<div id="waitingMsgText" class="chargementText" style="top: -1500px;"><!-- This trick is to preload the background image -->
  <div class="loading">Chargement en cours</div>
</div>
<div id="sessionLockMask" style="display: none;">
  {{if $app->user_id}}
  <div class="window" style="position: absolute; text-align: center; -moz-border-radius: 10px; background: #eee;">
    <div style="background: #ccc; -moz-border-radius: 5px 5px 0 0; font-weight: bold; margin: 0; padding: 5px 1em;">
      Session verrouillée - {{$app->_ref_user}}
    </div>
    <form name="unlockSession" method="post" action="?" onsubmit="return false" 
     style="display: block; margin: 1em;">
      <input type="hidden" name="login" value="{{$app->_ref_user->_user_username}}" />
      <div style="margin: 0.7em; display: block;">
        <label for="password">Mote de passe: </label>
        <input type="password" name="password" />
      </div>
      <button class="tick" onclick="Session.unlock()">Déverouiller</button>
      <button class="cancel" onclick="Session.close()">Déconnecter</button>
    </form>
  </div>
  {{/if}}
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

<!-- Up button -->
<div id="goUp" title="Retour en haut de la page" onclick="document.documentElement.scrollTop = 0;"></div>