<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision: $
 *  @author Thomas Despoix
 */
 
global $can, $m, $AppUI, $dPconfig;

$can->needsAdmin();

$config = $dPconfig[$m]["CCatalogueLabo"];

if (null == $remote_name = @$config["remote_name"]) {
  $AppUI->stepAjax("Remote name not configured", UI_MSG_ERROR);
}

if (null == $remote_url = @$config["remote_url"]) {
  $AppUI->stepAjax("Remote URL not configured", UI_MSG_ERROR);
}

if (false === $content = file_get_contents($remote_url)) {
  $AppUI->stepAjax("Couldn't connect to remote url", UI_MSG_ERROR);
}

$xml = new SimpleXMLElement($content);

mbTrace($xml, "XML");

?>
