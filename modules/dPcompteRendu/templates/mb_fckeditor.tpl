// Mediboard Combo configuration
var aMbCombos = new Array();
var bAutoSelectSpans = {{if $templateManager->valueMode}} false {{else}} true {{/if}};

// FCK editor general configuration
sMbPath = "../../../";
sMbPluginsPath = sMbPath + "modules/dPcompteRendu/fcke_plugins/" ;
FCKConfig.EditorAreaCSS = sMbPath + "style/mediboard/htmlarea.css?build={{$version.build}}";
FCKConfig.EnterMode = 'br';
FCKConfig.Debug = false;

{{if $templateManager->printMode}}
FCKConfig.ToolbarSets["Default"] = [
	['Preview', 'Print', '-','Find','About'],
	['FitWindow','Source',]
] ;

{{else}}
  {{if $app->user_prefs.saveOnPrint}}
    var textForPrint = 'mbPrint';
  {{else}}
    var textForPrint = 'Print';
  {{/if}}
// Toolbar Configuration
FCKConfig.ToolbarSets["Default"] = [

	['Save','Preview'],
{{if !$templateManager->simplifyMode}}
	['Cut','Copy','Paste','PasteText','PasteWord','-',textForPrint,'-','mbHeader','mbFooter'],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	['Table','Rule','Image', 'Smiley','SpecialChar','mbPageBreak'],
	['FitWindow','Source','About'],
	'/',
{{/if}}
	['FontFormat',{{if !$templateManager->simplifyMode}}'FontName',{{/if}}'FontSize'],
	['Bold','Italic','Underline',{{if !$templateManager->simplifyMode}}'StrikeThrough',{{/if}}'-','Subscript','Superscript'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
	['OrderedList','UnorderedList','-','Outdent','Indent'],
	['TextColor','BGColor']
] ;

// Add properties Combo
var oMbCombo = new Object();
oMbCombo.commandName = "MbField";
oMbCombo.spanClass = {{if $templateManager->valueMode}} "value" {{else}} "field" {{/if}};
oMbCombo.commandLabel = "Champs";

var aOptions = new Array();
oMbCombo.options = aOptions;
aMbCombos.push(oMbCombo);

{{foreach from=$templateManager->sections key=title item=section}}
  aOptions.push({view: "<b>{{$title|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}</b>" ,item: ""});
{{foreach from=$section item=property}}
  aOptions.push( {
    view: '<span style="padding-left: 1em;">{{$property.view|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}' ,
    item: 
      {{if $templateManager->valueMode}}
        "{{$property.value|smarty:nodefaults|escape:"htmlall"|nl2br|escape:"javascript"}}" 
      {{else}} 
        "[{{$property.field|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}]" 
      {{/if}}
    });
{{/foreach}}
{{/foreach}}


{{if !$templateManager->valueMode}}
// Add name lists Combo
var oMbCombo = new Object();
oMbCombo.commandName = "MbNames";
oMbCombo.spanClass = "name";
oMbCombo.commandLabel = "Liste de choix";

var aOptions = new Array();
oMbCombo.options = aOptions;
aMbCombos.push(oMbCombo);

{{foreach from=$templateManager->lists item=list}}
  aOptions.push( { 
    view: "{{$list.name|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}" ,
    item: "[Liste - {{$list.name|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}]"
    });
{{/foreach}}
{{/if}}

// Add helpers Combo
var oMbCombo = new Object();
oMbCombo.commandName = "MbHelpers";
oMbCombo.spanClass = "helper";
oMbCombo.commandLabel = "Aides &agrave; la saisie";

var aOptions = new Array();
oMbCombo.options = aOptions;
aMbCombos.push(oMbCombo);

{{foreach from=$templateManager->helpers key=helperName item=helperText}}
  {{if $helperText == ""}}
  aOptions.push( { 
    view: "<b>{{$helperName|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}</b>" ,
    item: ""
    });
  {{else}}  
  
  aOptions.push( { 
    view: "<span style='padding-left: 1em;'>{{$helperName|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}</span>" ,
    item: "{{$helperText|smarty:nodefaults|escape:"htmlall"|nl2br|escape:"javascript"}}"
    });
  {{/if}}
{{/foreach}}

aToolbarSet = FCKConfig.ToolbarSets["Default"];

// Add MbCombos toolbar
aMbToolbar = new Array();
for (var i = 0; i < aMbCombos.length; i++) {
  aMbToolbar.push(aMbCombos[i].commandName);
}
{{if !$templateManager->simplifyMode}}
  aToolbarSet.push(aMbToolbar);
{{/if}}

FCKConfig.Plugins.Add( 'mbpagebreak', 'en,fr', sMbPluginsPath );
FCKConfig.Plugins.Add( 'mbcombo', 'en,fr', sMbPluginsPath );
FCKConfig.Plugins.Add( 'mbprint', 'en,fr', sMbPluginsPath );
FCKConfig.Plugins.Add( 'mbheader', 'en,fr', sMbPluginsPath );
FCKConfig.Plugins.Add( 'mbfooter', 'en,fr', sMbPluginsPath );
FCKConfig.Debug = false;

{{/if}}
