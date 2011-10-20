<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision:$
* @author SARL Openxtrem
*/

CCanDo::checkRead();

$cr_id = CValue::get("compte_rendu_id");
$update_date_print = CValue::get("update_date_print", 0);
$cr = new CCompteRendu();
$cr->load($cr_id);
$cr->loadContent();

if (!$cr->canRead()) return;

// Mise à jour de la date d'impression
if ($update_date_print) {
  $cr->date_print = "now";
  if ($msg = $cr->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
  }
}
echo "<!DOCTYPE html>
<html>
	<head>
	  <link type=\"text/css\" rel=\"stylesheet\" href=\"style/mediboard/htmlarea.css\" media=\"all\" />
	</head>
	<body>
	  $cr->_source
	</body>
</html>";