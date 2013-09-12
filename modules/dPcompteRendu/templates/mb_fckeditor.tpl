{{assign var=pdf_thumbnails value=$conf.dPcompteRendu.CCompteRendu.pdf_thumbnails}}
{{assign var=clean_word value=$conf.dPcompteRendu.CCompteRendu.clean_word}}
{{assign var=mode_play value=$app->user_prefs.mode_play}}

// Preloading extra plugins
var list_plugins = {
  apicrypt:     "../../modules/dPcompteRendu/fcke_plugins/apicrypt/",
  mbcap:        "../../modules/dPcompteRendu/fcke_plugins/mbcap/",
  mbfields:     "../../modules/dPcompteRendu/fcke_plugins/mbfields/",
  mbfooter:     "../../modules/dPcompteRendu/fcke_plugins/mbfooter/",
  mbfreetext:   "../../modules/dPcompteRendu/fcke_plugins/mbfreetext/",
  mbheader:     "../../modules/dPcompteRendu/fcke_plugins/mbheader/",
  mbhelpers:    "../../modules/dPcompteRendu/fcke_plugins/mbhelpers/",
  mblineheight: "../../modules/dPcompteRendu/fcke_plugins/mblineheight/",
  mblists:      "../../modules/dPcompteRendu/fcke_plugins/mblists/",
  mbpagebreak:  "../../modules/dPcompteRendu/fcke_plugins/mbpagebreak/",
  {{if $mode_play && !$templateManager->isModele}}
  mbplay:       "../../modules/dPcompteRendu/fcke_plugins/mbplay/",
  {{/if}}
  mbprint:      "../../modules/dPcompteRendu/fcke_plugins/mbprint/",
  mbprinting:   "../../modules/dPcompteRendu/fcke_plugins/mbprinting/",
  mbprintPDF:   "../../modules/dPcompteRendu/fcke_plugins/mbprintPDF/",
  mbreplace:    "../../modules/dPcompteRendu/fcke_plugins/mbreplace/",
  mbspace:      "../../modules/dPcompteRendu/fcke_plugins/mbspace/",
  {{if $can->admin}}
  mbthumbs:     "../../modules/dPcompteRendu/fcke_plugins/mbthumbs/",
  {{/if}}
  usermessage:  "../../modules/dPcompteRendu/fcke_plugins/usermessage/",
};

date = new Date();
date = Math.round(date.getTime()/3600000);

$H(list_plugins).each(function(plugin) {
  CKEDITOR.plugins.addExternal(plugin[0], plugin[1], "plugin.js?"+date);
});

CKEDITOR.editorConfig = function(config) {
  config.language = 'fr';
  config.defaultLanguage = 'fr';
  config.contentsLanguage = 'fr';
  config.enterMode = CKEDITOR.ENTER_BR;
  config.allowedContent = true;
  //config.startupFocus = true;
  config.pasteFromWordPromptCleanup = true;
  config.pasteFromWordRemoveFontStyles = "{{$clean_word}}";
  config.pasteFromWordRemoveStyles = "{{$clean_word}}";
  config.fontSize_sizes  = '8/8pt;9/9pt;10/10pt;11/11pt;12/12pt;14/14pt;16/16pt;18/18pt;20/20pt;22/22pt;24/24pt;26/26pt;28/28pt;36/36pt;48/48pt;72/72pt;';
  config.fontSize_sizes += 'xx-small/xx-small;x-small/x-small;small/small;medium/medium;large/large;x-large/x-large;xx-large/xx-large';

  config.font_names =
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
  config.indentOffset = 10;
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
  config.removePlugins = 'elementspath,iframe,showblocks,templates,wsc{{if $templateManager->printMode}},save{{/if}}';

  {{if $templateManager->printMode}}
    config.toolbar = [['Preview', 'Print', '-','Find']];

  {{elseif $templateManager->simplifyMode}}
    config.toolbar = [
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
    config.extraPlugins += 'mbheader,mbfooter,mbpagebreak,mblineheight{{if "printing"|module_active && !$templateManager->isModele}},mbprinting{{/if}}{{if $can->admin}},mbthumbs{{/if}},mbcap,mbreplace';
    {{if !$templateManager->isModele}}
      config.extraPlugins += ',usermessage{{if $use_apicrypt}},apicrypt{{/if}}';
    {{/if}}

    config.toolbar = [
    ['Save','Preview'], [{{if $pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}'mbprintPDF',{{/if}} textForPrint, 'mbprinting', 'SelectAll', 'Cut', 'Copy', 'PasteText', 'PasteFromWord', 'Find', 'Undo', 'Redo'],
    [{{if !$templateManager->isModele}}'mbheader', 'mbfooter',{{/if}} 'mbpagebreak'],
    ['Table','HorizontalRule','Image','SpecialChar', 'mbspace', 'Checkbox'],
    ['Maximize', 'Source'], '/',
    ['Font', 'FontSize'],
    ['RemoveFormat', 'Bold', 'Italic', 'Underline', 'Strike', 'mbcap', 'mbreplace'],
    ['TransformTextSwitcher', 'TransformTextToLowercase', 'TransformTextToUppercase', 'TransformTextCapitalize'],
    ['Subscript', 'Superscript', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', 'NumberedList', 'BulletedList'],
    ['Outdent', 'Indent', 'mblineheight', 'TextColor', 'BGColor'],'/',
    [{{if !$templateManager->isModele && $mode_play}}'mbplay', {{/if}} 'mbfields', {{if $templateManager->isModele}}'mblists', 'mbfreetext', {{/if}}{{if !$templateManager->isModele}}'mbhelpers', 'usermessage', {{if $use_apicrypt}}'apicrypt', {{/if}}{{/if}}{{if $can->admin}}'mbthumbs', {{/if}}{{if $pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}'mbhidethumbs'{{/if}}]];

    window.parent.fields = [];
    window.parent.listeChoix = [];
    window.parent.helpers = [];

    // Champs
    var aOptionsFields = [];

    window.parent.fields = {
    commandName: "MbField",
    spanClass: {{if $templateManager->valueMode}} "value" {{else}} "field" {{/if}},
    commandLabel: "Champs",
    options: aOptionsFields
    };

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
              {{if @$_property.options.data}}
                "[{{$_property.field|smarty:nodefaults|escape:"javascript"}}]",
              {{else}}
                "[{{$_property.field|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}]",
              {{/if}}
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
            {{if @$property.options.barcode || @$property.options.image || @$property.options.data}}
              "{{$property.field|smarty:nodefaults|escape:"javascript"}}" ,
            {{else}}
              "{{$property.value|smarty:nodefaults|nl2br|escape:"javascript"}}",
            {{/if}}
          {{else}}
            {{if @$property.options.barcode || @$property.options.image || @$property.options.data}}
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
}
