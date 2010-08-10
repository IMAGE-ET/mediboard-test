/* $Id: fckplugin.js 5238 2008-11-18 15:25:26Z phenxdesign $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: 5238 $
 * @author Romain OLLIVIER
 *
 * Mediboard additional page-break button plugin for FCKeditor
 */
 
// Define the commande name
var sMbFreeTextName = "mbFreeText";

// Defines command class
var FCKMbFreeTextCommand = function() {
  this.Name = "FreeText";
}
  
FCKMbFreeTextCommand.prototype.Execute = function() {
  
  FCKDialog.OpenDialog('FreeText',"Ins&eacute;rer une zone de texte",
      '../../../modules/dPcompteRendu/fcke_plugins/mbfreetext/fcke_insert_area.html',400,200,'PlainText');

  //FCK.InsertHtml("<span class=\"field\">[[Texte libre]]</span>&nbsp;");
}

FCKMbFreeTextCommand.prototype.GetState = function() {
  return FCK_TRISTATE_OFF ;
}

// Registers command object
var oCommand = new FCKMbFreeTextCommand();
FCKCommands.RegisterCommand("mbFreeText", oCommand);

// Defines toolbar item class
var FCKToolbarMbFreeText = function() {
  this.Command = FCKCommands.GetCommand("mbFreeText");
  this.commandName = "mbFreeText";
}

// Inherit from FCKToolbarButton.
FCKToolbarMbFreeText.prototype = new FCKToolbarButton("mbFreeText", FCKLang.mbPageBreak) ;

FCKToolbarMbFreeText.prototype.GetLabel = function() {
  return "mbFreeText" ;
}

var oMbFreeTextItem = new FCKToolbarMbFreeText ;

oMbFreeTextItem.IconPath = sMbPluginsPath + 'mbfreetext/images/mbFreeTextPics.png';

// Registers toolbar item object
FCKToolbarItems.RegisterItem("mbFreeText", oMbFreeTextItem) ;
