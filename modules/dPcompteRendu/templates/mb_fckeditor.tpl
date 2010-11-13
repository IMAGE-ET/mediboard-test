{{assign var=pdf_thumbnails value=$dPconfig.dPcompteRendu.CCompteRendu.pdf_thumbnails}}

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

{{elseif $templateManager->simplifyMode}}
FCKConfig.ToolbarSets["Default"] = [
    ['Save','Preview'],
    ['FontFormat', 'FontSize'],
    ['Bold','Italic','Underline', '-','Subscript','Superscript'],
    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
    ['OrderedList','UnorderedList','-','Outdent','Indent'],
    ['TextColor','BGColor']
  ];
{{else}}
  {{if $app->user_prefs.saveOnPrint}}
    var textForPrint = 'mbPrint';
  {{else}}
    var textForPrint = 'Print';
  {{/if}}

  FCKConfig.ToolbarSets["Default"] = [
    ['Save','Preview'], [{{if $pdf_thumbnails == 1}}'mbPrintPDF',{{/if}} textForPrint, 'SelectAll', 'Cut', 'Copy','Paste', 'PasteText', 
     'PasteWord', 'Undo','Redo', 'Find'],
    [{{if !$templateManager->isModele}}'mbHeader','mbFooter',{{/if}}'mbPageBreak'],
    ['Table','Rule','Image','SpecialChar'],
    ['FitWindow', 'Source', 'About'], '/',
    ['FontFormat', 'FontName', 'FontSize'],
    ['RemoveFormat', 'Bold','Italic','Underline', 'StrikeThrough'],
    ['Subscript','Superscript','JustifyLeft','JustifyCenter','JustifyRight','JustifyFull','OrderedList','UnorderedList'],
    ['Outdent','Indent','TextColor','BGColor'],
    '/',
    ['mbfields', {{if $templateManager->isModele}}'mblists', 'mbFreeText', {{/if}}'mbhelpers']];

window.parent.fields = [];
window.parent.listeChoix = [];
window.parent.helpers = [];

// Champs
var aOptionsFields = [];

window.parent.fields.push({
  commandName: "MbField",
  spanClass: {{if $templateManager->valueMode}} "value" {{else}} "field" {{/if}},
  commandLabel: "Champs",
  options: aOptionsFields
});

{{foreach from=$templateManager->sections key=title item=section}}
  aOptionsFields["{{$title|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}"] = [];
{{foreach from=$section item=property key=_index}}
  {{if strpos($_index, ' - ') === false}}
    aOptionsFields["{{$title|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}"]["{{$_index|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}"] = [
    {{foreach from=$property item=_property name=subproperty}}
      {
      view: "{{$_property.view|smarty:nodefaults|escape:"javascript"}}" ,
      item: 
        {{if $templateManager->valueMode}}
          "{{$_property.value|utf8_encode|smarty:nodefaults|nl2br|escape:"javascript"}}",
        {{else}} 
          "[{{$_property.field|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}]", 
        {{/if}}
       shortview: "{{$_property.shortview|utf8_encode|smarty:nodefaults|escape:"javascript"}}"
      }
    {{if $smarty.foreach.subproperty.index != $smarty.foreach.subproperty.last}}
      ,
    {{/if}}
    {{/foreach}}
    ];
  {{else}}
  aOptionsFields["{{$title|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}"]["{{$_index|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}"] =
  {
      view: '{{$property.view|smarty:nodefaults|escape:"javascript"}}' ,
      item: 
        {{if $templateManager->valueMode}}
          {{if @$property.options.barcode}}
            "{{$property.field|smarty:nodefaults|escape:"javascript"}}" ,
          {{else}}
            "{{$property.value|utf8_encode|smarty:nodefaults|nl2br|escape:"javascript"}}",
          {{/if}}
        {{else}} 
          {{if @$property.options.barcode}}
            "{{$property.field|smarty:nodefaults|escape:"javascript"}}",
          {{else}}
            "[{{$property.field|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}]", 
          {{/if}}
        {{/if}}
       shortview: "{{$property.shortview|utf8_encode|smarty:nodefaults|escape:"javascript"}}"
    };
  {{/if}}
{{/foreach}}
{{/foreach}}

// Liste de choix
{{if !$templateManager->valueMode}}
  // Add name lists Combo
  var aOptionsList = [];
  window.parent.listeChoix.push({
    commandName: "MbNames",
    spanClass: "name",
    commandLabel: "Liste de choix",
    options: aOptionsList
  });
  
  {{foreach from=$templateManager->lists item=list}}
    aOptionsList.push( {
      view: "{{$list.name|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}" ,
      item: "[Liste - {{$list.name|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}]"
      });
  {{/foreach}}
{{/if}}

// Aides à la saisie
aOptionsHelpers = {{$templateManager->helpers|@json|smarty:nodefaults}};
window.parent.helpers.push({
  commandName: "MbHelpers",
  spanClass: "helper",
  commandLabel: "Aides &agrave; la saisie",
  options: aOptionsHelpers
});

aToolbarSet = FCKConfig.ToolbarSets["Default"];

// Add MbCombos toolbar
/*aMbToolbar = [];
for (var i = 0; i < aMbCombos.length; i++) {
  aMbToolbar.push(aMbCombos[i].commandName);
}*/

//aToolbarSet.push(aMbToolbar);

{{/if}}

FCKConfig.Plugins.Add( 'mbpagebreak', 'en,fr', sMbPluginsPath );
//FCKConfig.Plugins.Add( 'mbcombo', 'en,fr', sMbPluginsPath );
FCKConfig.Plugins.Add( 'mbhelp', 'en,fr', sMbPluginsPath );
FCKConfig.Plugins.Add( 'mbprint', 'en,fr', sMbPluginsPath );
 FCKConfig.Plugins.Add( 'mbfreetext', 'en,fr', sMbPluginsPath );
 
{{if $pdf_thumbnails == 1}}
  FCKConfig.Plugins.Add( 'mbprintPDF', 'en,fr', sMbPluginsPath );
{{/if}}

{{if !$templateManager->isModele}}
FCKConfig.Plugins.Add( 'mbheader' , 'en,fr', sMbPluginsPath );
FCKConfig.Plugins.Add( 'mbfooter' , 'en,fr', sMbPluginsPath );
{{/if}}

FCKConfig.Plugins.Add( 'mbfields' , 'en,fr', sMbPluginsPath );
FCKConfig.Plugins.Add( 'mblists'  , 'en,fr', sMbPluginsPath );
FCKConfig.Plugins.Add( 'mbhelpers', 'en,fr', sMbPluginsPath );

// Definition of custom keystrokes
FCKConfig.Keystrokes[21] = [ CTRL + 72 /*H*/, "mbHeader" ];
FCKConfig.Keystrokes[22] = [ CTRL + 79 /*O*/, "mbFooter" ];
FCKConfig.Keystrokes[23] = [ CTRL + 75 /*K*/, "mbPageBreak" ];
FCKConfig.Keystrokes[24] = [ CTRL + ALT + 85 /*U*/, "Source" ];

if (window.parent.pdf_thumbnails == 1)
  FCKConfig.Keystrokes[25] = [ CTRL + 80 /*P*/, "mbPrintPDF" ];
