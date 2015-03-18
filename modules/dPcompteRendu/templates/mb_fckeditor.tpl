{{assign var=pdf_thumbnails value=$conf.dPcompteRendu.CCompteRendu.pdf_thumbnails}}
{{assign var=clean_word value=$conf.dPcompteRendu.CCompteRendu.clean_word}}
{{assign var=mode_play value=$app->user_prefs.mode_play}}

// Preloading extra plugins
window.list_plugins = [
  "apicrypt",
  "mbcap",
  "mbfields",
  "mbfooter",
  "mbfreetext",
  "mbheader",
  "mbhelpers",
  "mblineheight",
  "mblists",
  "mbpagebreak",
  {{if $mode_play && !$templateManager->isModele}}
  "mbplay",
  {{/if}}
  "mbprint",
  "mbprinting",
  "mbprintPDF",
  "mbreplace",
  "mbspace",
  {{if $can->admin}}
  "mbthumbs",
  {{/if}}
  "mssante",
  "usermessage"
];

date = new Date();
date = Math.round(date.getTime()/3600000);

list_plugins.each(function(plugin) {
  CKEDITOR.plugins.addExternal(plugin, "../../modules/dPcompteRendu/fcke_plugins/" + plugin + "/", "plugin.js?"+date);
});

CKEDITOR.editorConfig = function(config) {
  config.language = 'fr';
  config.defaultLanguage = 'fr';
  config.contentsLanguage = 'fr';
  config.enterMode = CKEDITOR.ENTER_BR;
  config.allowedContent = true;
  config.title = false;
  //config.startupFocus = true;
  config.pasteFromWordPromptCleanup = true;
  config.pasteFromWordRemoveFontStyles = "{{$clean_word}}";
  config.pasteFromWordRemoveStyles = "{{$clean_word}}";
  config.fontSize_sizes  = '8/8pt;9/9pt;10/10pt;11/11pt;12/12pt;14/14pt;16/16pt;18/18pt;20/20pt;22/22pt;24/24pt;26/26pt;28/28pt;36/36pt;48/48pt;72/72pt;';
  config.fontSize_sizes += 'xx-small/xx-small;x-small/x-small;small/small;medium/medium;large/large;x-large/x-large;xx-large/xx-large';

  config.font_names = "{{$conf.dPcompteRendu.CCompteRendu.default_fonts}}";

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
  config.entities_additional="#039";

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
  config.removePlugins = 'elementspath,iframe,magicline,showblocks,templates,wsc{{if $templateManager->printMode}},save{{/if}}';

  {{if $templateManager->printMode}}
    config.extraPlugins = '{{if $pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}mbprintPDF,{{/if}}usermessage';
    config.toolbar = [['Preview' {{if $app->user_prefs.show_old_print}},'Print'{{/if}} {{if $pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}},'mbprintPDF'{{/if}}, '-','Find', 'usermessage']];
  {{elseif $templateManager->simplifyMode}}
    config.toolbar = [
    ['Save', 'Preview'],
    ['Font', 'FontSize'],
    ['Bold', 'Italic', 'Underline', '-', 'Subscript', 'Superscript'],
    ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
    ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'],
    ['TextColor', 'BGColor']
    ];
   {{elseif $templateManager->messageMode}}
      config.toolbar = [
        ['Font', 'FontSize'],
        ['Bold', 'Italic', 'Underline', '-', 'Subscript', 'Superscript'],
        ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
        ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'],
        ['TextColor', 'BGColor']
      ];
   {{else}}
    {{if $app->user_prefs.saveOnPrint || ($pdf_thumbnails && $app->user_prefs.pdf_and_thumbs)}}
      var textForPrint = 'mbprint';
    {{else}}
      var textForPrint = 'Print';
    {{/if}}

    config.extraPlugins =  'mbfields,mbfreetext,mbhelpers,mblists,{{if $mode_play && !$templateManager->isModele}}mbplay,{{/if}},mbprint,mbprintPDF,mbspace,';
    config.extraPlugins += 'mbheader,mbfooter,mbpagebreak,mblineheight{{if "printing"|module_active && !$templateManager->isModele}},mbprinting{{/if}}{{if $can->admin}},mbthumbs{{/if}},mbcap,mbreplace';
    {{if !$templateManager->isModele}}
      config.extraPlugins += ',usermessage{{if $use_apicrypt}},apicrypt{{/if}}{{if $use_mssante}},mssante{{/if}}';
    {{/if}}

    config.toolbar = [
    ['Save','Preview'], [{{if $pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}'mbprintPDF',{{/if}} {{if $app->user_prefs.show_old_print}}textForPrint,{{/if}} 'mbprinting', 'SelectAll', 'Cut', 'Copy', 'PasteText', 'PasteFromWord', 'Find', 'Undo', 'Redo'],
    [{{if !$templateManager->isModele}}'mbheader', 'mbfooter',{{/if}} 'mbpagebreak'],
    ['Table','HorizontalRule','Image','SpecialChar', 'mbspace', 'Checkbox'],
    ['Maximize', 'Source'], '/',
    ['Font', 'FontSize'],
    ['RemoveFormat', 'Bold', 'Italic', 'Underline', 'Strike', 'mbcap', 'mbreplace'],
    ['TransformTextSwitcher', 'TransformTextToLowercase', 'TransformTextToUppercase', 'TransformTextCapitalize'],
    ['Subscript', 'Superscript', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', 'NumberedList', 'BulletedList'],
    ['Outdent', 'Indent', 'mblineheight', 'TextColor', 'BGColor'],'/',
    [{{if !$templateManager->isModele && $mode_play}}'mbplay', {{/if}} 'mbfields', {{if $templateManager->isModele}}'mblists', 'mbfreetext', {{/if}}{{if !$templateManager->isModele}}'mbhelpers', 'usermessage', {{if $use_apicrypt}}'apicrypt', {{/if}}{{if $use_mssante}}'mssante', {{/if}}{{/if}}{{if $can->admin}}'mbthumbs', {{/if}}{{if $pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}'mbhidethumbs'{{/if}}]];

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
              {{if @$_property.options.barcode || @$_property.options.image || @$_property.options.data}}
                "{{$_property.field|smarty:nodefaults|escape:"javascript"}}" ,
              {{else}}
                "{{$_property.value|smarty:nodefaults|nl2br|escape:"javascript"}}",
              {{/if}}
            {{else}}
              {{if @$_property.options.barcode || @$_property.options.image || @$_property.options.data}}
                "{{$_property.field|smarty:nodefaults|escape:"javascript"}}",
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
  {{/if}}
  window.parent.destinataires_courrier = {{"utf8_encode"|array_map_recursive:$templateManager->destinataires|@json|smarty:nodefaults}};
}
