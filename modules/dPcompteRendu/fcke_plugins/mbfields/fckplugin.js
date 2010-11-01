/* $Id: fckplugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author Romain OLLIVIER
 *
 * Add fields in the editor.
 */
 
// Define the commande name
var sMbFieldsName = "mbfields";

// Defines command class
var FCKMbFieldsCommand = function() {
  this.Name = "fields";
}
  
FCKMbFieldsCommand.prototype.Execute = function() {
  FCKDialog.OpenDialog('fields',"Ins&eacute;rer un champ",
      '../../../modules/dPcompteRendu/fcke_plugins/mbfields/fields.html',600,300,'PlainText');
}

FCKMbFieldsCommand.prototype.GetState = function() {
  return FCK_TRISTATE_OFF ;
}

// Registers command object
var oCommand = new FCKMbFieldsCommand();
FCKCommands.RegisterCommand("mbfields", oCommand);

// Defines toolbar item class
var FCKToolbarMbFields = function() {
  this.Command = FCKCommands.GetCommand("mbfields");
  this.commandName = "mbfields";
}

// Inherit from FCKToolbarButton.
FCKToolbarMbFields.prototype = new FCKToolbarButton("mbfields", FCKLang.mbfields, null, FCK_TOOLBARITEM_ICONTEXT) ;

FCKToolbarMbFields.prototype.GetLabel = function() {
  return "mbfields" ;
}

var oMbFieldsItem = new FCKToolbarMbFields ;

oMbFieldsItem.IconPath = sMbPluginsPath + 'mbfields/images/mbfields.png';

// Registers toolbar item object
FCKToolbarItems.RegisterItem("mbfields", oMbFieldsItem) ;
