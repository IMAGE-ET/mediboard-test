/* $Id: fckplugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author Sébastien Fillonneau
 *
 */

// Define the commande name
var sMbPrintName = "mbPrint";

//Defines command class
var FCKMbPrintCommand = function() {
  this.Name = "mbPrint";
}

FCKMbPrintCommand.prototype.Execute = function() {
  FCK.EditorWindow.print();
  if (window.parent.Preferences.saveOnPrint == 2 || confirm("Souhaitez-vous enregistrer ce document ?")){
    FCKSaveCommand.prototype.Execute();
  }
}

FCKMbPrintCommand.prototype.GetState = function() {
  return FCK_TRISTATE_OFF ;
}

//Registers command object
var oCommand = new FCKMbPrintCommand();
FCKCommands.RegisterCommand("mbPrint", oCommand);

//Defines toolbar item class
var FCKToolbarMbPrint = function() {
  this.Command = FCKCommands.GetCommand("mbPrint");
  this.commandName = "mbPrint";
}

//***********************************


// Inherit from FCKToolbarButton.
FCKToolbarMbPrint.prototype = new FCKToolbarButton("mbPrint", FCKLang.mbPrint) ;

FCKToolbarMbPrint.prototype.GetLabel = function() {
  return "mbPrint" ;
}

var oMbPrintItem = new FCKToolbarMbPrint ;

oMbPrintItem.IconPath = sMbPluginsPath + 'mbprint/images/mbprint.gif';

// Registers toolbar item object
FCKToolbarItems.RegisterItem("mbPrint", oMbPrintItem) ;
