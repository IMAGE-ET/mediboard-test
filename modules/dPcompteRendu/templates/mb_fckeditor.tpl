// Mediboard Combo configuration
var aMbCombos = [];
var bAutoSelectSpans = {{if $templateManager->valueMode}} false {{else}} true {{/if}};

// FCK editor general configuration
sMbPath = "../../../";
sMbPluginsPath = sMbPath + "modules/dPcompteRendu/fcke_plugins/" ;
FCKConfig.EditorAreaCSS = sMbPath + "style/mediboard/htmlarea.css?build={{$version.build}}";
FCKConfig.EnterMode = 'br';
FCKConfig.Debug = false;
FCKConfig.FirefoxSpellChecker = true;
FCKConfig.BrowserContextMenuOnCtrl = true;
FCKConfig.AutoDetectPasteFromWord = true;

{{if $templateManager->printMode}}
FCKConfig.ToolbarSets["Default"] = [
	['Preview', 'Print', '-','Find'],
	['About']
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
var aOptions = [];
aMbCombos.push({
  commandName: "MbField",
  spanClass: {{if $templateManager->valueMode}} "value" {{else}} "field" {{/if}},
  commandLabel: "Champs",
  options: aOptions
});

{{foreach from=$templateManager->sections key=title item=section}}
  aOptions.push({view: "<b>{{$title|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}</b>" ,item: ""});
{{foreach from=$section item=property}}
  aOptions.push( {
    view: '<span style="padding-left: 1em;">{{$property.view|smarty:nodefaults|escape:"javascript"}}' ,
    item: 
      {{if $templateManager->valueMode}}
        {{if @$property.options.barcode}}
          "{{$property.field|smarty:nodefaults|escape:"javascript"}}" 
        {{else}}
          "{{$property.value|utf8_encode|smarty:nodefaults|nl2br|escape:"javascript"}}" 
        {{/if}}
      {{else}} 
        {{if @$property.options.barcode}}
          "{{$property.field|smarty:nodefaults|escape:"javascript"}}" 
        {{else}}
          "[{{$property.field|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}]" 
        {{/if}}
      {{/if}}
    });
{{/foreach}}
{{/foreach}}


{{if !$templateManager->valueMode}}
// Add name lists Combo
var aOptions = [];
aMbCombos.push({
  commandName: "MbNames",
  spanClass: "name",
  commandLabel: "Liste de choix",
  options: aOptions
});

{{foreach from=$templateManager->lists item=list}}
  aOptions.push( { 
    view: "{{$list.name|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}" ,
    item: "[Liste - {{$list.name|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}]"
    });
{{/foreach}}
{{/if}}

// Add helpers Combo
var aOptions = [];
aMbCombos.push({
  commandName: "MbHelpers",
  spanClass: "helper",
  commandLabel: "Aides &agrave; la saisie",
  options: aOptions
});

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
aMbToolbar = [];
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

{{/if}}
