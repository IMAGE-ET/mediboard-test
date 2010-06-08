/* $Id: fckplugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author Thomas Despoix
 *
 */

// Defines command class
var FCKMbHeaderCommand = function() {
  this.Name = "mbHeader";
}

FCKMbHeaderCommand.prototype.Execute = function() {
	var oHeader = FCK.EditorDocument.getElementById("header");
  if (oHeader.style.display == "block" || oHeader.style.display == "") {
    oHeader.style.display = "none";
  }
  else {
   oHeader.style.display = "block";
  }
	oMbHeaderItem.RefreshState();
}

FCKMbHeaderCommand.prototype.GetState = function() {
	var oHeader = FCK.EditorDocument.getElementById("header");
	
	if (oHeader == null) {
	  return FCK_TRISTATE_DISABLED;
	}
	
	return oHeader.style.display != "none" ? FCK_TRISTATE_ON : FCK_TRISTATE_OFF;
}

// Registers command object
var oCommand = new FCKMbHeaderCommand();
FCKCommands.RegisterCommand("mbHeader", oCommand);

// Defines toolbar item class
var FCKToolbarMbHeader = function() {
  this.Command = FCKCommands.GetCommand("mbHeader");
  this.commandName = "mbHeader";
}

// Inherit from FCKToolbarButton.
FCKToolbarMbHeader.prototype = new FCKToolbarButton("mbHeader", FCKLang.mbHeader) ;

FCKToolbarMbHeader.prototype.GetLabel = function() {
  return "mbHeader" ;
}

var oMbHeaderItem = new FCKToolbarMbHeader ;

oMbHeaderItem.IconPath = sMbPluginsPath + 'mbheader/images/icon.gif';

// Registers toolbar item object
FCKToolbarItems.RegisterItem("mbHeader", oMbHeaderItem) ;
