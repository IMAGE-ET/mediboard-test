/* $Id: fckplugin.js 5238 2008-11-18 15:25:26Z phenxdesign $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: 5238 $
 * @author Romain OLLIVIER
 *
 * Mediboard additional help button for shortcuts for FCKeditor
 */
 
// Define the commande name
var sMbHelpName = "mbHelp";

// Defines command class
var FCKMbHelpCommand = function() {
  this.Name = "mbHelp";
}
  
FCKMbHelpCommand.prototype.Execute = function() {
  var url = window.open(FCKPlugins.Items["mbhelp"].Path+"help.html", FCKLang.mbHelp.replace(/[ -]/gi, "_"), "width=500,height=360");
}

FCKMbHelpCommand.prototype.GetState = function() {
  return FCK_TRISTATE_OFF ;
}

// Registers command object
//FCKLang.mbHelp,FCKPlugins.Items["mbhelp"].Path
//FCKCommands.RegisterCommand("mbHelp",

var oCommand = new FCKMbHelpCommand();
FCKCommands.RegisterCommand("mbHelp", oCommand);

// Defines toolbar item class
var FCKToolbarMbHelp = function() {
  this.Command = FCKCommands.GetCommand("mbHelp");
  this.commandName = "mbHelp";
}

// Inherit from FCKToolbarButton.
FCKToolbarMbHelp.prototype = new FCKToolbarButton("mbHelp", FCKLang.mbHelp) ;

FCKToolbarMbHelp.prototype.GetLabel = function() {
  return "mbHelp" ;
}

var oMbHelpItem = new FCKToolbarMbHelp ;

oMbHelpItem.IconPath = sMbPluginsPath + 'mbhelp/images/mbhelp.gif';

// Registers toolbar item object
FCKToolbarItems.RegisterItem("mbHelp", oMbHelpItem) ;
