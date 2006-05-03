{literal}


function debugAlert(oTarget, sLabel) {
  var sMsg = sLabel + " : " ;
  
  if (typeof oTarget == "object") {
    sMsg += "Object";
    
    for (var sProp in oTarget) {
      sMsg += "\n  => " + sProp + ": ";
      var oProp = oTarget[sProp];
      switch (typeof oProp) {
        case "object": sMsg += "object"; break;
        case "function": sMsg += "function"; break;
        default: sMsg += oProp; break;
      }
    }
  } else {
    sMsg += oTarget;    
  }
  
  alert(sMsg);
}
{/literal}


// Mediboard Combo configuration
var aMbCombos = new Array();

var bAutoSelectSpans = {if $templateManager->valueMode} false {else} true {/if};

// Add properties Combo
var oMbCombo = new Object();
oMbCombo.commandName = "MbField";
oMbCombo.spanClass = {if $templateManager->valueMode} "value" {else} "field" {/if};
oMbCombo.commandLabel = "Champs";

var aOptions = new Array();
oMbCombo.options = aOptions;
aMbCombos.push(oMbCombo);

{foreach from=$templateManager->properties item=property}
  aOptions.push( {ldelim} 
    view: "{$property.field|escape:"htmlall"|escape:"javascript"}" ,
    item: 
      {if $templateManager->valueMode} 
        "{$property.value|escape:"htmlall"|nl2br|escape:"javascript"}" 
      {else} 
        "[{$property.field|escape:"htmlall"|escape:"javascript"}]" 
      {/if}
    {rdelim});
{/foreach}

{if !$templateManager->valueMode}
// Add name lists Combo
var oMbCombo = new Object();
oMbCombo.commandName = "MbNames";
oMbCombo.spanClass = "name";
oMbCombo.commandLabel = "Liste de choix";

var aOptions = new Array();
oMbCombo.options = aOptions;
aMbCombos.push(oMbCombo);

{foreach from=$templateManager->lists item=list}
  aOptions.push( {ldelim} 
    view: "{$list.name|escape:"htmlall"|escape:"javascript"}" ,
    item: "[Liste - {$list.name|escape:"htmlall"|escape:"javascript"}]"
    {rdelim});
{/foreach}
{/if}

// Add helpers Combo
var oMbCombo = new Object();
oMbCombo.commandName = "MbHelpers";
oMbCombo.spanClass = "helper";
oMbCombo.commandLabel = "Aides &agrave; la saisie";

var aOptions = new Array();
oMbCombo.options = aOptions;
aMbCombos.push(oMbCombo);

{foreach from=$templateManager->helpers key=helperName item=helperText}
  aOptions.push( {ldelim} 
    view: "{$helperName|escape:"htmlall"|escape:"javascript"}" ,
    item: "{$helperText|escape:"htmlall"|nl2br|escape:"javascript"}"
    {rdelim});
{/foreach}

// Toolbar configuration
aToolbarSet = FCKConfig.ToolbarSets['Default'];

// Add Table toolbar
// aTableToolbar = ['Table','-','TableInsertRow','TableDeleteRows','TableInsertColumn','TableDeleteColumns','TableInsertCell','TableDeleteCells','TableMergeCells','TableSplitCell'];
// aToolbarSet.push(aTableToolbar); 

// Add MbCombos toolbar
aMbToolbar = new Array();
for (var i = 0; i < aMbCombos.length; i++) {ldelim}
  aMbToolbar.push(aMbCombos[i].commandName);
{rdelim}
// Add MbPageBreak button
aMbToolbar.push("mbPageBreak");
aToolbarSet.push(aMbToolbar);

// Remove Form Toolbar
aToolbarSet.splice(8, 1);

// Remove About Toolbar
aToolbarSet.splice(11, 1);

// FCK editor general configuration
sMbPath = "../../../";

{if $configAlert}
alert('{$configAlert|escape:javascript}');
{/if}

FCKConfig.Plugins.Add( 'tablecommands', null);

sMbPluginsPath = sMbPath + "modules/dPcompteRendu/fcke_plugins/" ;
FCKConfig.Plugins.Add( 'mbcombo'    , 'en,fr', sMbPluginsPath ) ;
FCKConfig.Plugins.Add( 'mbpagebreak', 'en,fr', sMbPluginsPath ) ;

FCKConfig.EditorAreaCSS = sMbPath + "style/mediboard/htmlarea.css?build={$mb_version_build}";
FCKConfig.DefaultLanguage = "fr" ;
FCKConfig.AutoDetectLanguage = false ;

// Warning: fckeditor/editor/filemanager/browser/default/connectors/php/config.php must contain:
// $Config['UserFilesPath'] = '/mediboard/files/editor/' ;

FCKConfig.LinkBrowserURL = FCKConfig.BasePath + 'filemanager/browser/default/browser.html?Connector=connectors/php/connector.php' ; 
FCKConfig.ImageBrowserURL = FCKConfig.BasePath + 'filemanager/browser/default/browser.html?Type=Image&Connector=connectors/php/connector.php' ;

// Screws the context menu style away
// FCKConfig.SkinPath = "./skins/default/";


