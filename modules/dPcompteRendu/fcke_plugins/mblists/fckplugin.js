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
var sMbListsName = "mblists";

// Defines command class
var FCKMbListsCommand = function() {
  this.Name = "lists";
}
  
FCKMbListsCommand.prototype.Execute = function() {
  FCKDialog.OpenDialog('lists',"Ins&eacute;rer une liste",
      '../../../modules/dPcompteRendu/fcke_plugins/mblists/lists.html',300,300,'PlainText');
}

FCKMbListsCommand.prototype.GetState = function() {
  return FCK_TRISTATE_OFF ;
}

// Registers command object
var oCommand = new FCKMbListsCommand();
FCKCommands.RegisterCommand("mblists", oCommand);

// Defines toolbar item class
var FCKToolbarMbLists = function() {
  this.Command = FCKCommands.GetCommand("mblists");
  this.commandName = "mblists";
}

// Inherit from FCKToolbarButton.
FCKToolbarMbLists.prototype = new FCKToolbarButton("mblists", FCKLang.mblists, null, FCK_TOOLBARITEM_ICONTEXT) ;

FCKToolbarMbLists.prototype.GetLabel = function() {
  return "mblists" ;
}

var oMbListsItem = new FCKToolbarMbLists ;

oMbListsItem.IconPath = sMbPluginsPath + 'mblists/images/mblists.png';

// Registers toolbar item object
FCKToolbarItems.RegisterItem("mblists", oMbListsItem) ;
