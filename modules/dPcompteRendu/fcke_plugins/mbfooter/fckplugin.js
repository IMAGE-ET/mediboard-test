/* $Id: fckplugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author Thomas Despoix
 *
 */

// Defines command class
var FCKMbFooterCommand = function() {
  this.Name = "mbFooter";
}

FCKMbFooterCommand.prototype.Execute = function() {
	var oFooter = FCK.EditorDocument.getElementById("footer");
	console.debug("Be");
	console.debug("fore");
	console.debug(oFooter.style.display);
	oFooter.style.display = "block";
	oMbFooterItem.RefreshState();
}

FCKMbFooterCommand.prototype.GetState = function() {
	var oFooter = FCK.EditorDocument.getElementById("footer");
	
	if (oFooter == null) {
	  return FCK_TRISTATE_DISABLED;
	}
	
	console.debug("After");
	console.debug(oFooter.style.display);
	return oFooter.style.display == "block" ? FCK_TRISTATE_ON : FCK_TRISTATE_OFF;
}

// Registers command object
var oCommand = new FCKMbFooterCommand();
FCKCommands.RegisterCommand("mbFooter", oCommand);

// Defines toolbar item class
var FCKToolbarMbFooter = function() {
  this.Command = FCKCommands.GetCommand("mbFooter");
  this.commandName = "mbFooter";
}

// Inherit from FCKToolbarButton.
FCKToolbarMbFooter.prototype = new FCKToolbarButton("mbFooter", FCKLang.mbFooter) ;

FCKToolbarMbFooter.prototype.GetLabel = function() {
  return "mbFooter" ;
}

var oMbFooterItem = new FCKToolbarMbFooter ;

oMbFooterItem.IconPath = sMbPluginsPath + 'mbfooter/images/icon.gif';

// Registers toolbar item object
FCKToolbarItems.RegisterItem("mbFooter", oMbFooterItem) ;
