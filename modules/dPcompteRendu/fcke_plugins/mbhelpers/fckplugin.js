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
var sMbHelpersName = "mbhelpers";

// Defines command class
var FCKMbHelpersCommand = function() {
  this.Name = "helpers";
}
  
FCKMbHelpersCommand.prototype.Execute = function() {
  FCKDialog.OpenDialog('helpers',"Ins&eacute;rer une aide &agrave; la saisie",
      '../../../modules/dPcompteRendu/fcke_plugins/mbhelpers/helpers.html',480,300,'PlainText');
}

FCKMbHelpersCommand.prototype.GetState = function() {
  return FCK_TRISTATE_OFF ;
}

// Registers command object
var oCommand = new FCKMbHelpersCommand();
FCKCommands.RegisterCommand("mbhelpers", oCommand);

// Defines toolbar item class
var FCKToolbarMbHelpers = function() {
  this.Command = FCKCommands.GetCommand("mbhelpers");
  this.commandName = "mbhelpers";
}

// Inherit from FCKToolbarButton.
FCKToolbarMbHelpers.prototype = new FCKToolbarButton("mbhelpers", FCKLang.mbhelpers, null, FCK_TOOLBARITEM_ICONTEXT) ;

FCKToolbarMbHelpers.prototype.GetLabel = function() {
  return "mbhelpers" ;
}

var oMbHelpersItem = new FCKToolbarMbHelpers ;

oMbHelpersItem.IconPath = sMbPluginsPath + 'mbhelpers/images/mbhelpers.png';

// Registers toolbar item object
FCKToolbarItems.RegisterItem("mbhelpers", oMbHelpersItem) ;
