<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision:$
* @author SARL Openxtrem
*/

CCanDo::checkRead();

$cr_id = CValue::get("compte_rendu_id");

$cr = new CCompteRendu();
$cr->load($cr_id);
$cr->loadContent();

if (!$cr->canRead()) return;

echo "<!DOCTYPE html>
<html>
	<head>
	  <link type=\"text/css\" rel=\"stylesheet\" href=\"style/mediboard/htmlarea.css\" media=\"all\" />
	</head>
	<body>
	  $cr->_source
	</body>
</html>";