<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Mediboard :: Système de gestion des structures de santé</title>
  <meta http-equiv="Content-Type" content="text/html;charset={{$localeCharSet}}" />
  <meta name="Description" content="Mediboard: Plateforme Open Source pour les Etablissement de Santé" />
  <meta name="Version" content="{{$mediboardVersion}}" />
  {{$mediboardShortIcon|smarty:nodefaults}}
  {{$mediboardCommonStyle|smarty:nodefaults}}
  {{$mediboardStyle|smarty:nodefaults}}
  {{$mediboardScript|smarty:nodefaults}}
  <script type="text/javascript">
    {{if $offline}}
    var config = {{$configOffline|@json}};
    {{/if}}
    var Preferences = {{$app->user_prefs|@json}};
    
    {{if $dialog}}
    Event.observe(document, 'keydown', closeWindowByEscape);
    {{/if}}
  </script>
</head>

<body onload="main()">

<!-- Loading divs -->
<div id="waitingMsgMask" class="chargementMask" style="display: none;"></div>
<div id="waitingMsgText" class="chargementText" style="display: none;">
  <table class="tbl">
    <tr>
      <th class="title">
        <div class="loading"><span id="waitingInnerMsgText">Chargement en cours</span></div>
      </th>
    </tr>
  </table>
</div>

<!-- Tooltip div used for dom clonage -->

<div id="tooltipTpl" class="tooltip" style="display: none;">
  <table class="decoration">
    <tr>
      <td class="top-left" />
      <td class="top" />
	    <td class="top-right" />
	  </tr>
	  <tr>
	    <td class="left" />
	    <td class="content"></td>
	    <td class="right" />
	  </tr>
	  <tr>
	    <td class="bottom-left" />
	    <td class="bottom" />
	    <td class="bottom-right" />
	  </tr>
	</table>
</div>

{{if !$offline}}
<script type="text/javascript">
function popChgPwd() {
  var url = new Url;
  url.setModuleAction("admin", "chpwd");
  url.popup(400, 300, "ChangePassword");
}
</script>
{{/if}}