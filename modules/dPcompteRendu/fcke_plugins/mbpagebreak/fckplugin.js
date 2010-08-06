/* $Id$
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision$
 * @author Romain OLLIVIER
 *
 * Mediboard additional page-break button plugin for FCKeditor
 */
 
// Define the commande name
var sMbPageBreakName = "mbPageBreak";

// Defines command class
var FCKMbPageBreakCommand = function() {
  this.Name = "mbPageBreak";
}
  
FCKMbPageBreakCommand.prototype.Execute = function() {
  FCK.Focus();
  FCK.InsertHtml("<hr class='pagebreak' />");
}

FCKMbPageBreakCommand.prototype.GetState = function() {
  return FCK_TRISTATE_OFF ;
}

// Registers command object
var oCommand = new FCKMbPageBreakCommand();
FCKCommands.RegisterCommand("mbPageBreak", oCommand);

// Defines toolbar item class
var FCKToolbarMbPageBreak = function() {
  this.Command = FCKCommands.GetCommand("mbPageBreak");
  this.commandName = "mbPageBreak";
}

// Inherit from FCKToolbarButton.
FCKToolbarMbPageBreak.prototype = new FCKToolbarButton("mbPageBreak", FCKLang.mbPageBreak) ;

FCKToolbarMbPageBreak.prototype.GetLabel = function() {
  return "mbPageBreak" ;
}

var oMbPageBreakItem = new FCKToolbarMbPageBreak ;

oMbPageBreakItem.IconPath = sMbPluginsPath + 'mbpagebreak/images/mbpageBreakPics.gif';

// Registers toolbar item object
FCKToolbarItems.RegisterItem("mbPageBreak", oMbPageBreakItem) ;
