{{assign var=pdf_thumbnails value=$conf.dPcompteRendu.CCompteRendu.pdf_thumbnails}}
{{assign var=clean_word value=$conf.dPcompteRendu.CCompteRendu.clean_word}}
{{assign var=mode_play value=$app->user_prefs.mode_play}}

// Preloading extra plugins
CKEDITOR.plugins.addExternal("mbheader"    , "../../modules/dPcompteRendu/fcke_plugins/mbheader/plugin.js");
CKEDITOR.plugins.addExternal("mbfooter"    , "../../modules/dPcompteRendu/fcke_plugins/mbfooter/plugin.js");
CKEDITOR.plugins.addExternal("mbfields"    , "../../modules/dPcompteRendu/fcke_plugins/mbfields/plugin.js");
CKEDITOR.plugins.addExternal("mbfreetext"  , "../../modules/dPcompteRendu/fcke_plugins/mbfreetext/plugin.js");
CKEDITOR.plugins.addExternal("mbhelpers"   , "../../modules/dPcompteRendu/fcke_plugins/mbhelpers/plugin.js");
CKEDITOR.plugins.addExternal("mblists"     , "../../modules/dPcompteRendu/fcke_plugins/mblists/plugin.js");
{{if $mode_play && !$templateManager->isModele}}
  CKEDITOR.plugins.addExternal("mbplay"    , "../../modules/dPcompteRendu/fcke_plugins/mbplay/plugin.js");
{{/if}}
CKEDITOR.plugins.addExternal("mbprint"     , "../../modules/dPcompteRendu/fcke_plugins/mbprint/plugin.js");
CKEDITOR.plugins.addExternal("mbprinting"  , "../../modules/dPcompteRendu/fcke_plugins/mbprinting/plugin.js");
CKEDITOR.plugins.addExternal("mbprintPDF"  , "../../modules/dPcompteRendu/fcke_plugins/mbprintPDF/plugin.js");
CKEDITOR.plugins.addExternal("mbpagebreak" , "../../modules/dPcompteRendu/fcke_plugins/mbpagebreak/plugin.js");
CKEDITOR.plugins.addExternal("mblineheight", "../../modules/dPcompteRendu/fcke_plugins/mblineheight/plugin.js");
CKEDITOR.plugins.addExternal("usermessage"      , "../../modules/dPcompteRendu/fcke_plugins/usermessage/plugin.js");
{{if $use_apicrypt}}
  CKEDITOR.plugins.addExternal("apicrypt"    , "../../modules/dPcompteRendu/fcke_plugins/apicrypt/plugin.js");
{{/if}}

{{if $can->admin}}
  CKEDITOR.plugins.addExternal("mbthumbs"  , "../../modules/dPcompteRendu/fcke_plugins/mbthumbs/plugin.js");
{{/if}}
CKEDITOR.plugins.addExternal("mbbreakage"  , "../../modules/dPcompteRendu/fcke_plugins/mbbreakage/plugin.js");
CKEDITOR.plugins.addExternal("mbcap"       , "../../modules/dPcompteRendu/fcke_plugins/mbcap/plugin.js");
CKEDITOR.plugins.addExternal("mbspace"     , "../../modules/dPcompteRendu/fcke_plugins/mbspace/plugin.js");

// CK editor general configuration
CKEDITOR.editorConfig = function(config) {
  config.skin = "v2";
  config.language = 'fr';
  config.defaultLanguage = 'fr';
  config.contentsLanguage = 'fr';
  config.enterMode = CKEDITOR.ENTER_BR;
  config.pasteFromWordPromptCleanup = true;
  config.pasteFromWordRemoveFontStyles = "{{$clean_word}}";
  config.pasteFromWordRemoveStyles = "{{$clean_word}}";
  config.fontSize_sizes  = '8/8px;9/9px;10/10px;11/11px;12/12px;14/14px;16/16px;18/18px;20/20px;22/22px;24/24px;26/26px;28/28px;36/36px;48/48px;72/72px;';
  config.fontSize_sizes += 'xx-small/xx-small;x-small/x-small;small/small;medium/medium;large/large;x-large/x-large;xx-large/xx-large';
  
  CKEDITOR.config.font_names =
  'Arial/Arial, Helvetica, sans-serif;' +
  'Calibri/Calibri, Helvetica, sans-serif;'+
  'Comic Sans MS/Comic Sans MS, cursive;' +
  'Courier New/Courier New, Courier, monospace;' +
  'Georgia/Georgia, serif;' +
  'Lucida Sans Unicode/Lucida Sans Unicode, Lucida Grande, sans-serif;' +
  'Symbol/Symbol;' +
  'Tahoma/Tahoma, Geneva, sans-serif;' +
  'Times New Roman/Times New Roman, Times, serif;' +
  'Trebuchet MS/Trebuchet MS, Helvetica, sans-serif;' +
  'Verdana/Verdana, Geneva, sans-serif;' +
  'ZapfDingBats/ZapfDingBats';
  
  var css = ["style/mediboard/htmlarea.css?build={{$version.build}}"];
  if (Prototype.Browser.IE) {
    css.push("style/mediboard/ie.css?build={{$version.build}}");
  }
  config.contentsCss = css;
  
  config.docType = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
  config.filebrowserImageBrowseUrl = "lib/kcfinder/browse.php?type=image";
  config.tabSpaces = 13;
  config.disableNativeSpellChecker = false;
  config.resize_maxWidth = "100%";
  config.resize_minWidth = "100%";
  
  {{if $templateManager->font != ""}}
    config.font_defaultLabel = '{{$templateManager->font}}';
  {{else}}
    config.font_defaultLabel = '{{$conf.dPcompteRendu.CCompteRendu.default_font}}';
  {{/if}}
  
  {{if $templateManager->size != ""}}
    config.fontSize_defaultLabel = '{{$templateManager->size}}' 
  {{else}}
    config.fontSize_defaultLabel = '{{$conf.dPcompteRendu.CCompteRendu.default_size}}'
  {{/if}}
  
  // Suppression du redimensionnement manuel
  config.resize_enabled = false;
  // Suppression du bouton de masquage des barres d'outils
  config.toolbarCanCollapse = false;
  // Suppression de la barre d'état avec la dom
  config.removePlugins = 'a11yhelp,about,elementspath,flash,iframe,link,newpage,pagebreak,scayt,showblocks,smiley,templates,wsc';
  
  {{if $templateManager->printMode}}
    config.toolbar_Full = [['Preview', 'Print', '-','Find']];

  {{elseif $templateManager->simplifyMode}}
    config.toolbar_Full = [
        ['Save', 'Preview'],
        ['Font', 'FontSize'],
        ['Bold', 'Italic', 'Underline', '-', 'Subscript', 'Superscript'],
        ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
        ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'],
        ['TextColor', 'BGColor']
      ];
  {{else}}
    {{if $app->user_prefs.saveOnPrint}}
      var textForPrint = 'mbprint';
    {{else}}
      var textForPrint = 'Print';
    {{/if}}
    
    config.extraPlugins =  'mbfields,mbfreetext,mbhelpers,mblists,{{if $mode_play && !$templateManager->isModele}}mbplay,{{/if}},mbprint,mbprintPDF,mbspace,';
    config.extraPlugins += 'mbheader,mbfooter,mbpagebreak,mblineheight{{if "printing"|module_active && !$templateManager->isModele}},mbprinting{{/if}}{{if $can->admin}},mbthumbs{{/if}},mbcap,mbbreakage';
    {{if !$templateManager->isModele}}
      config.extraPlugins += ',usermessage{{if $use_apicrypt}},apicrypt{{/if}}';
    {{/if}}    
    config.toolbar_Full = [
      ['Save','Preview'], [{{if $pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}'mbprintPDF',{{/if}} textForPrint, 'mbprinting', 'SelectAll', 'Cut', 'Copy', 'PasteText', 'PasteFromWord', 'Find', 'Undo', 'Redo'],
      [{{if !$templateManager->isModele}}'mbheader', 'mbfooter',{{/if}} 'mbpagebreak'],
      ['Table','HorizontalRule','Image','SpecialChar', 'mbspace', 'Checkbox'],
      ['Maximize', 'Source'], '/',
      ['Font', 'FontSize'],
      ['RemoveFormat', 'Bold', 'Italic', 'Underline', 'Strike', 'mbbreakage', 'mbcap'],
      ['Subscript', 'Superscript', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', 'NumberedList', 'BulletedList'],'/',
      ['Outdent', 'Indent', 'mblineheight', 'TextColor', 'BGColor'],'/',
      [{{if !$templateManager->isModele && $mode_play}}'mbplay', {{/if}} 'mbfields', {{if $templateManager->isModele}}'mblists', 'mbfreetext', {{/if}}{{if !$templateManager->isModele}}'mbhelpers', 'usermessage', {{if $use_apicrypt}}'apicrypt', {{/if}}{{/if}}{{if $can->admin}}'mbthumbs'{{/if}}]];

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

    {{assign var=i value=0}}
    {{foreach from=$templateManager->sections key=title item=section}}
    
      aOptionsFields[{{$i}}] = [];
  {{assign var=j value=0}}
  {{foreach from=$section item=property key=_index}}
    {{if strpos($_index, ' - ') === false}}
      aOptionsFields[{{$i}}][{{$j}}] = [
      {{foreach from=$property item=_property name=subproperty}}
        {
        section: "{{$title|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}",
        itemname: "{{$_index|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}",
        view: "{{$_property.view|smarty:nodefaults|escape:"javascript"}}" ,
        item: 
          {{if $templateManager->valueMode}}
            "{{$_property.value|smarty:nodefaults|nl2br|escape:"javascript"}}",
          {{else}} 
            "[{{$_property.field|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}]", 
          {{/if}}
         shortview: "{{$_property.shortview|smarty:nodefaults|escape:"javascript"}}"
        }
      {{if $smarty.foreach.subproperty.index != $smarty.foreach.subproperty.last}}
        ,
      {{/if}}
      {{/foreach}}
      ];
    {{else}}
    aOptionsFields[{{$i}}][{{$j}}] =
    {   section: "{{$title|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}",
        view: '{{$property.view|smarty:nodefaults|escape:"javascript"}}' ,
        item: 
          {{if $templateManager->valueMode}}
            {{if @$property.options.barcode || @$property.options.image}}
              "{{$property.field|smarty:nodefaults|escape:"javascript"}}" ,
            {{else}}
              "{{$property.value|smarty:nodefaults|nl2br|escape:"javascript"}}",
            {{/if}}
          {{else}} 
            {{if @$property.options.barcode || @$property.options.image}}
              "{{$property.field|smarty:nodefaults|escape:"javascript"}}",
            {{else}}
              "[{{$property.field|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}]", 
            {{/if}}
          {{/if}}
         shortview: "{{$property.shortview|smarty:nodefaults|escape:"javascript"}}"
      };
    {{/if}}
    {{math equation="$j+1" assign=j}}
  {{/foreach}}
    {{math equation="$i+1" assign=i}}
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
  aOptionsHelpers = {{"utf8_encode"|array_map_recursive:$templateManager->helpers|@json|smarty:nodefaults}};
  window.parent.helpers.push({
    commandName: "MbHelpers",
    spanClass: "helper",
    commandLabel: "Aides &agrave; la saisie",
    options: aOptionsHelpers
  });
  
  window.parent.destinataires = {{"utf8_encode"|array_map_recursive:$templateManager->destinataires|@json|smarty:nodefaults}};
{{/if}}


  // Definition of custom keystrokes
  config.keystrokes.push( [CKEDITOR.CTRL + 72 /*H*/, "mbheader"] );
  config.keystrokes.push( [CKEDITOR.CTRL + 79 /*O*/, "mbfooter"] );
  config.keystrokes.push( [CKEDITOR.CTRL + 75 /*K*/, "mbpagebreak"] );
  config.keystrokes.push( [CKEDITOR.CTRL + CKEDITOR.ALT + 85 /*U*/, "Source"] );
  config.keystrokes.push( [CKEDITOR.CTRL + 83 /*S*/, "save"] );  
  if (window.parent.pdf_thumbnails && window.parent.Preferences.pdf_and_thumbs == 1)
    config.keystrokes.push( [CKEDITOR.CTRL + 80 /*P*/, "mbprintPDF"] );
}