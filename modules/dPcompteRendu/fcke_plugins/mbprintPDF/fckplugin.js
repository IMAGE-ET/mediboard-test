/* $Id: fckplugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author Sébastien Fillonneau
 *
 */

// Define the commande name
var sMbPrintName = "mbPrintPDF";

//Defines command class
var FCKMbPrintCommand = function() {
  this.Name = "mbPrintPDF";
}

FCKMbPrintCommand.prototype.Execute = function() {
	var content = FCKeditorAPI.Instances.source.GetHTML();
	var form = window.parent.document.forms["download-pdf-form"];
	form.elements.content.value = encodeURIComponent(content);
	form.onsubmit();
}

FCKMbPrintCommand.prototype.GetState = function() {
  return FCK_TRISTATE_OFF ;
}

//Registers command object
var oCommand = new FCKMbPrintCommand();
FCKCommands.RegisterCommand("mbPrintPDF", oCommand);

//Defines toolbar item class
var FCKToolbarMbPrint = function() {
  this.Command = FCKCommands.GetCommand("mbPrintPDF");
  this.commandName = "mbPrintPDF";
}

//***********************************

// Inherit from FCKToolbarButton.
FCKToolbarMbPrint.prototype = new FCKToolbarButton("mbPrintPDF", FCKLang.mbPrintPDF) ;

FCKToolbarMbPrint.prototype.GetLabel = function() {
  return "mbPrintPDF" ;
}

var oMbPrintItem = new FCKToolbarMbPrint ;

oMbPrintItem.IconPath = sMbPluginsPath + 'mbprintPDF/images/mbprintPDF.gif';

// Registers toolbar item object
FCKToolbarItems.RegisterItem("mbPrintPDF", oMbPrintItem) ;
