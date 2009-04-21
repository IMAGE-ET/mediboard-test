<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Romain Ollivier
*/

global $can;
$can->read = 1;
$can->edit = 0;

CAppUI::requireModuleFile("dPbloc", "vw_suivi_salles")

?>